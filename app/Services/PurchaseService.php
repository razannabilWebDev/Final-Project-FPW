<?php

namespace App\Services;

use App\Enums\PurchaseStatus;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseService
{
    private const QUANTITY_SCALE = 3;

    private const MONEY_SCALE = 2;

    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array{
     *     purchase_date: string,
     *     supplier_name?: string|null,
     *     receipt_path?: string|null,
     *     items: list<array{ingredient_id: int|string, quantity: string, unit: string, unit_cost: string}>
     * }  $data
     */
    public function create(array $data, User $actor): Purchase
    {
        return DB::transaction(function () use ($data, $actor): Purchase {
            $items = $data['items'] ?? [];

            if ($items === []) {
                throw new InvalidArgumentException('Minimal satu item pembelian wajib diisi.');
            }

            $normalized = [];
            $total = '0.00';

            foreach ($items as $item) {
                $ingredientId = (int) ($item['ingredient_id'] ?? 0);
                $quantity = (string) ($item['quantity'] ?? '');
                $unit = strtolower(trim((string) ($item['unit'] ?? '')));
                $unitCost = (string) ($item['unit_cost'] ?? '');

                if ($ingredientId <= 0 || ! is_numeric($quantity) || bccomp($quantity, '0', self::QUANTITY_SCALE) <= 0) {
                    throw new InvalidArgumentException('Kuantitas tiap item pembelian harus lebih dari nol.');
                }

                if (! is_numeric($unitCost) || bccomp($unitCost, '0', self::MONEY_SCALE) <= 0) {
                    throw new InvalidArgumentException('Harga satuan tiap item pembelian harus lebih dari nol.');
                }

                /** @var Ingredient $ingredient */
                $ingredient = Ingredient::query()->findOrFail($ingredientId);

                if ($unit !== strtolower($ingredient->base_unit)) {
                    throw new InvalidArgumentException(
                        "Satuan {$ingredient->name} harus {$ingredient->base_unit} (satuan dasar bahan)."
                    );
                }

                $subtotal = bcmul($quantity, $unitCost, self::MONEY_SCALE);
                $total = bcadd($total, $subtotal, self::MONEY_SCALE);
                $normalized[] = [
                    'ingredient_id' => $ingredient->id,
                    'quantity' => $quantity,
                    'unit' => $ingredient->base_unit,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ];
            }

            $purchase = Purchase::create([
                'purchase_code' => $this->generateCode(),
                'purchase_date' => $data['purchase_date'],
                'supplier_name' => $data['supplier_name'] ?? null,
                'status' => PurchaseStatus::Pending,
                'total' => $total,
                'receipt_path' => $data['receipt_path'] ?? null,
                'created_by' => $actor->id,
            ]);

            foreach ($normalized as $item) {
                $purchase->items()->create($item);
            }

            return $purchase;
        });
    }

    public function verify(Purchase $purchase, bool $approve, ?string $note, User $actor): Purchase
    {
        return DB::transaction(function () use ($purchase, $approve, $note, $actor): Purchase {
            /** @var Purchase $locked */
            $locked = Purchase::query()->whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== PurchaseStatus::Pending) {
                throw new InvalidArgumentException('Pembelian ini sudah diverifikasi.');
            }

            if ($approve) {
                $locked->load('items.ingredient');

                foreach ($locked->items as $item) {
                    $this->stock->recordIn(
                        ingredient: $item->ingredient,
                        quantity: (string) $item->quantity,
                        referenceType: 'Purchase',
                        referenceId: $locked->id,
                        actor: $actor,
                        unitCost: (string) $item->unit_cost,
                        note: 'Stok masuk pembelian '.$locked->purchase_code,
                    );
                }

                $locked->status = PurchaseStatus::Approved;
            } else {
                $reason = trim((string) $note);

                if ($reason === '') {
                    throw new InvalidArgumentException('Alasan penolakan wajib diisi.');
                }

                $locked->status = PurchaseStatus::Rejected;
                $locked->rejection_note = $reason;
            }

            $locked->verified_by = $actor->id;
            $locked->verified_at = now();
            $locked->save();

            return $locked;
        });
    }

    private function generateCode(): string
    {
        $prefix = 'PUR-'.now()->timezone('Asia/Jakarta')->format('Ymd').'-';
        $last = Purchase::query()
            ->where('purchase_code', 'like', $prefix.'%')
            ->orderByDesc('purchase_code')
            ->value('purchase_code');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
