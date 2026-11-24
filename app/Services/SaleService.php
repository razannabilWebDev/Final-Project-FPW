<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleStatusHistory;
use App\Models\User;
use App\Services\Contracts\RecipeCostService;
use App\Services\Contracts\StockService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        private readonly RecipeCostService $recipeCosts,
        private readonly StockService $stocks,
    ) {}

    /**
     * @param  array{
     *     type: string,
     *     payment_method: string,
     *     note: ?string,
     *     total_nominal?: numeric-string|float|int,
     *     items?: list<array{menu_id: int, menu_name: string, unit_price: numeric-string|float|int, quantity: numeric-string|float|int}>
     * }  $data
     */
    public function create(array $data, User $user): Sale
    {
        $type = $data['type'];
        $payment = PaymentMethod::fromString($data['payment_method']);
        $isManual = $type === 'manual';

        return DB::transaction(function () use ($data, $user, $payment, $isManual): Sale {
            $sale = Sale::create([
                'transaction_code' => $this->generateCode(),
                'type' => $isManual ? 'manual' : 'menu',
                'status' => SaleStatus::Pending->value,
                'payment_method' => $payment->value,
                'total' => 0,
                'note' => $data['note'] ?? null,
                'created_by' => $user->id,
            ]);

            $total = '0.00';

            if ($isManual) {
                $total = number_format((float) ($data['total_nominal'] ?? 0), 2, '.', '');
            } else {
                foreach ($data['items'] ?? [] as $item) {
                    $menuId = (int) $item['menu_id'];
                    $quantity = number_format((float) $item['quantity'], 3, '.', '');
                    $unitPrice = number_format((float) $item['unit_price'], 2, '.', '');
                    $hpp = $this->recipeCosts->costPerPortion($menuId);
                    $subtotal = bcmul($quantity, $unitPrice, 2);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'menu_id' => $menuId,
                        'menu_name_snapshot' => $item['menu_name'],
                        'quantity' => $quantity,
                        'unit_price_snapshot' => $unitPrice,
                        'hpp_snapshot' => $hpp,
                        'subtotal' => $subtotal,
                    ]);

                    $total = bcadd($total, $subtotal, 2);
                }
            }

            $sale->update(['total' => $total]);

            SaleStatusHistory::create([
                'sale_id' => $sale->id,
                'from_status' => null,
                'to_status' => SaleStatus::Pending->value,
                'changed_by' => $user->id,
                'note' => 'Transaksi dibuat',
                'created_at' => now(),
            ]);

            return $sale->refresh();
        });
    }

    public function updateStatus(Sale $sale, SaleStatus $status, User $user, ?string $note = null): void
    {
        DB::transaction(function () use ($sale, $status, $user, $note): void {
            $from = $sale->status;

            if ($from === $status) {
                return;
            }

            if (! $this->canTransition($from, $status)) {
                throw new InvalidArgumentException(
                    "Transisi status {$from->value} → {$status->value} tidak diizinkan."
                );
            }

            $updates = ['status' => $status->value];

            // D1: potong stok sekali, baik saat masuk processing maupun langsung
            // ke completed (misalnya transaksi yang tidak melalui dapur).
            if (in_array($status, [SaleStatus::Processing, SaleStatus::Completed], true)
                && $sale->stock_deducted_at === null
                && $sale->type === 'menu') {
                $items = [];
                foreach ($sale->items as $item) {
                    foreach ($this->recipeCosts->ingredientsPerPortion((int) $item->menu_id) as $ingredient) {
                        $needed = bcmul((string) $item->quantity, $ingredient['quantity'], 3);
                        $id = $ingredient['ingredient_id'];
                        $items[$id] = bcadd($items[$id] ?? '0', $needed, 3);
                    }
                }

                $flat = [];
                foreach ($items as $id => $quantity) {
                    $flat[] = ['ingredient_id' => $id, 'quantity' => $quantity];
                }

                // Exception diteruskan agar DB::transaction rollback seluruh perubahan.
                $this->stocks->deductForSale($sale->id, $flat, $user->id);

                $sale->refresh();
                if ($sale->stock_deducted_at !== null) {
                    $updates['stock_deducted_at'] = $sale->stock_deducted_at;
                }
            }

            // D6: reversal sekali saat batal setelah stok terpotong.
            if ($status === SaleStatus::Cancelled && $sale->stock_deducted_at !== null && $sale->reversal_done_at === null) {
                $this->stocks->reverseForSale($sale->id, $user->id);
                $sale->refresh();
                if ($sale->reversal_done_at !== null) {
                    $updates['reversal_done_at'] = $sale->reversal_done_at;
                }
            }

            // D2: pendapatan final diakui saat completed.
            if ($status === SaleStatus::Completed && $sale->completed_at === null) {
                $updates['completed_at'] = now();
            }

            $sale->update($updates);

            SaleStatusHistory::create([
                'sale_id' => $sale->id,
                'from_status' => $from->value,
                'to_status' => $status->value,
                'changed_by' => $user->id,
                'note' => $note,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Status machine: transaksi batal bersifat terminal dan tidak boleh
     * dikembalikan ke processing; completed hanya boleh dibatalkan.
     */
    private function canTransition(SaleStatus $from, SaleStatus $to): bool
    {
        return in_array($to, $from->allowedTransitions(), true);
    }

    private function generateCode(): string
    {
        $prefix = 'TRX-'.now()->format('Ymd').'-';
        $last = Sale::where('transaction_code', 'like', $prefix.'%')
            ->orderByDesc('transaction_code')
            ->value('transaction_code');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
