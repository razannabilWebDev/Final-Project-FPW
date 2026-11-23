<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StockAndPurchaseSeeder extends Seeder
{
    private Carbon $now;

    private int $adminId;

    private int $stafId;

    public function run(): void
    {
        $this->now = now();
        $this->adminId = (int) DB::table('users')->where('email', 'admin@smartfood.test')->value('id');
        $this->stafId = (int) DB::table('users')->where('email', 'staf@smartfood.test')->value('id');

        $this->seedOpeningBalances();
        $this->seedApprovedPurchase();
        $this->seedRejectedPurchase();
        $this->seedWaste();
        $this->seedPhysicalAdjustment();
    }

    private function seedOpeningBalances(): void
    {
        foreach (DB::table('ingredients')->get() as $ingredient) {
            if (DB::table('stock_movements')
                ->where('reference_type', 'SeederOpenBalance')
                ->where('reference_id', $ingredient->id)
                ->exists()) {
                continue;
            }

            $stock = (float) $ingredient->current_stock;

            DB::table('stock_movements')->insert([
                'ingredient_id' => $ingredient->id,
                'movement_type' => 'adjustment',
                'quantity_in' => $stock,
                'quantity_out' => 0,
                'balance_before' => 0,
                'balance_after' => $stock,
                'reference_type' => 'SeederOpenBalance',
                'reference_id' => $ingredient->id,
                'note' => 'Stok awal dummy',
                'created_by' => $this->adminId,
                'created_at' => $this->now,
            ]);
        }
    }

    private function seedApprovedPurchase(): void
    {
        $code = 'PUR-20261118-0001';

        if (DB::table('purchases')->where('purchase_code', $code)->exists()) {
            return;
        }

        $items = [
            ['Ayam', 2000, 0.04],
            ['Minyak Goreng', 1000, 0.02],
            ['Beras', 2000, 0.01],
        ];

        $total = 0;
        foreach ($items as [, $quantity, $unitCost]) {
            $total += $quantity * $unitCost;
        }

        $purchaseId = DB::table('purchases')->insertGetId([
            'purchase_code' => $code,
            'purchase_date' => $this->now->copy()->subDays(20)->toDateString(),
            'supplier_name' => 'CV Bahan Segar',
            'status' => 'approved',
            'total' => $total,
            'receipt_path' => null,
            'created_by' => $this->stafId,
            'verified_by' => $this->adminId,
            'verified_at' => $this->now->copy()->subDays(20),
            'rejection_note' => null,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        foreach ($items as [$name, $quantity, $unitCost]) {
            $ingredient = DB::table('ingredients')->where('name', $name)->first();

            if ($ingredient === null) {
                continue;
            }

            DB::table('purchase_items')->insert([
                'purchase_id' => $purchaseId,
                'ingredient_id' => $ingredient->id,
                'quantity' => $quantity,
                'unit' => $ingredient->base_unit,
                'unit_cost' => $unitCost,
                'subtotal' => $quantity * $unitCost,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);

            $this->move(
                (int) $ingredient->id,
                'purchase_in',
                (float) $quantity,
                0,
                'Pembelian terverifikasi '.$code,
                'Purchase',
                $purchaseId,
            );
        }
    }

    private function seedRejectedPurchase(): void
    {
        $code = 'PUR-20261118-0002';

        if (DB::table('purchases')->where('purchase_code', $code)->exists()) {
            return;
        }

        $ingredient = DB::table('ingredients')->where('name', 'Cabai')->first();

        if ($ingredient === null) {
            return;
        }

        $purchaseId = DB::table('purchases')->insertGetId([
            'purchase_code' => $code,
            'purchase_date' => $this->now->copy()->subDays(15)->toDateString(),
            'supplier_name' => 'Supplier Tanpa Nota',
            'status' => 'rejected',
            'total' => 50,
            'receipt_path' => null,
            'created_by' => $this->stafId,
            'verified_by' => $this->adminId,
            'verified_at' => $this->now->copy()->subDays(15),
            'rejection_note' => 'Foto nota tidak terbaca, pembelian ditolak.',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        DB::table('purchase_items')->insert([
            'purchase_id' => $purchaseId,
            'ingredient_id' => $ingredient->id,
            'quantity' => 1000,
            'unit' => $ingredient->base_unit,
            'unit_cost' => 0.05,
            'subtotal' => 50,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }

    private function seedWaste(): void
    {
        if (DB::table('stock_movements')
            ->where('reference_type', 'SeederWaste')
            ->where('reference_id', 1)
            ->exists()) {
            return;
        }

        $ingredient = DB::table('ingredients')->where('name', 'Cabai')->first();

        if ($ingredient === null) {
            return;
        }

        $this->move(
            (int) $ingredient->id,
            'waste',
            0,
            100,
            'Stok cabai rusak',
            'SeederWaste',
            1,
        );
    }

    private function seedPhysicalAdjustment(): void
    {
        $reason = 'Penyesuaian stok fisik harian dummy';

        if (DB::table('stock_adjustments')->where('reason', $reason)->exists()) {
            return;
        }

        $ingredient = DB::table('ingredients')->where('name', 'Beras')->first();

        if ($ingredient === null) {
            return;
        }

        $systemQuantity = (float) $ingredient->current_stock;
        $physicalQuantity = $systemQuantity - 50;

        $adjustmentId = DB::table('stock_adjustments')->insertGetId([
            'ingredient_id' => $ingredient->id,
            'system_quantity' => $systemQuantity,
            'physical_quantity' => $physicalQuantity,
            'difference' => $physicalQuantity - $systemQuantity,
            'reason' => $reason,
            'created_by' => $this->adminId,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $this->move(
            (int) $ingredient->id,
            'adjustment',
            0,
            50,
            $reason,
            'StockAdjustment',
            $adjustmentId,
        );
    }

    private function move(
        int $ingredientId,
        string $movementType,
        float $quantityIn,
        float $quantityOut,
        string $note,
        ?string $referenceType,
        ?int $referenceId,
    ): void {
        $balanceBefore = (float) DB::table('ingredients')->where('id', $ingredientId)->value('current_stock');
        $balanceAfter = $balanceBefore + $quantityIn - $quantityOut;

        DB::table('stock_movements')->insert([
            'ingredient_id' => $ingredientId,
            'movement_type' => $movementType,
            'quantity_in' => $quantityIn,
            'quantity_out' => $quantityOut,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $this->adminId,
            'created_at' => $this->now,
        ]);

        DB::table('ingredients')->where('id', $ingredientId)->update([
            'current_stock' => $balanceAfter,
            'updated_at' => $this->now,
        ]);
    }
}
