<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Services\Contracts\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stub sementara sampai service stok terpusat Anggota 3 tersedia.
 * Tetap menulis ledger stock_movements dan menjaga idempotensi.
 */
class DefaultStockService implements StockService
{
    public function deductForSale(int $saleId, array $items, ?int $userId = null): void
    {
        if (! Schema::hasTable('ingredients') || ! Schema::hasTable('stock_movements')) {
            return;
        }

        $sale = DB::table('sales')->where('id', $saleId)->first();
        if (! $sale || $sale->stock_deducted_at !== null) {
            return;
        }

        // Agregasi kebutuhan per bahan.
        $needs = [];
        foreach ($items as $item) {
            $id = (int) $item['ingredient_id'];
            $qty = (string) $item['quantity'];
            $needs[$id] = bcadd($needs[$id] ?? '0', $qty, 3);
        }

        if ($needs === []) {
            DB::table('sales')->where('id', $saleId)->update(['stock_deducted_at' => now()]);

            return;
        }

        $ingredients = DB::table('ingredients')
            ->whereIn('id', array_keys($needs))
            ->get()
            ->keyBy('id');

        $shortages = [];
        foreach ($needs as $id => $qty) {
            $ingredient = $ingredients->get($id);
            if (! $ingredient) {
                continue;
            }
            if (bccomp((string) $ingredient->current_stock, $qty, 3) < 0) {
                $shortages[] = [
                    'ingredient_id' => $id,
                    'name' => $ingredient->name,
                    'needed' => $qty,
                    'available' => (string) $ingredient->current_stock,
                ];
            }
        }

        if ($shortages !== []) {
            $names = implode(', ', array_column($shortages, 'name'));
            throw new InsufficientStockException("Stok tidak cukup untuk: {$names}.", $shortages);
        }

        foreach ($needs as $id => $qty) {
            $ingredient = $ingredients->get($id);
            if (! $ingredient) {
                continue;
            }

            $before = (string) $ingredient->current_stock;
            $after = bcsub($before, $qty, 3);

            DB::table('stock_movements')->insert([
                'ingredient_id' => $id,
                'movement_type' => 'sale_out',
                'quantity_in' => '0.000',
                'quantity_out' => $qty,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => 'Sale',
                'reference_id' => $saleId,
                'note' => 'Pengurangan stok penjualan #'.$sale->transaction_code,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            DB::table('ingredients')->where('id', $id)->update(['current_stock' => $after]);
        }

        DB::table('sales')->where('id', $saleId)->update(['stock_deducted_at' => now()]);
    }

    public function reverseForSale(int $saleId, ?int $userId = null): void
    {
        if (! Schema::hasTable('ingredients') || ! Schema::hasTable('stock_movements')) {
            return;
        }

        $sale = DB::table('sales')->where('id', $saleId)->first();
        if (! $sale || $sale->reversal_done_at !== null || $sale->stock_deducted_at === null) {
            return;
        }

        $movements = DB::table('stock_movements')
            ->where('reference_type', 'Sale')
            ->where('reference_id', $saleId)
            ->where('movement_type', 'sale_out')
            ->get();

        foreach ($movements as $movement) {
            $ingredient = DB::table('ingredients')->where('id', $movement->ingredient_id)->first();
            if (! $ingredient) {
                continue;
            }

            $before = (string) $ingredient->current_stock;
            $after = bcadd($before, (string) $movement->quantity_out, 3);

            DB::table('stock_movements')->insert([
                'ingredient_id' => $movement->ingredient_id,
                'movement_type' => 'reversal',
                'quantity_in' => (string) $movement->quantity_out,
                'quantity_out' => '0.000',
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => 'Sale',
                'reference_id' => $saleId,
                'note' => 'Reversal pembatalan #'.$sale->transaction_code,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            DB::table('ingredients')->where('id', $movement->ingredient_id)->update(['current_stock' => $after]);
        }

        DB::table('sales')->where('id', $saleId)->update(['reversal_done_at' => now()]);
    }
}
