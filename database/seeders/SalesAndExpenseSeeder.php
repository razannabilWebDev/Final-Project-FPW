<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAndExpenseSeeder extends Seeder
{
    private Carbon $now;

    private int $adminId;

    private int $stafId;

    public function run(): void
    {
        $this->now = now();
        $this->adminId = (int) DB::table('users')->where('email', 'admin@smartfood.test')->value('id');
        $this->stafId = (int) DB::table('users')->where('email', 'staf@smartfood.test')->value('id');

        $this->seedMenuSales();
        $this->seedManualSale();
        $this->seedCancelledSale();
        $this->seedExpenses();
    }

    private function seedMenuSales(): void
    {
        $sales = [
            ['code' => 'TRX-20261104-0001', 'days_ago' => 14, 'payment' => 'cash', 'items' => [['Nasi Ayam Geprek', 3], ['Ayam Kecap', 2]]],
            ['code' => 'TRX-20261105-0002', 'days_ago' => 13, 'payment' => 'transfer', 'items' => [['Ayam Bakar Madu', 2]]],
            ['code' => 'TRX-20261106-0003', 'days_ago' => 12, 'payment' => 'qris', 'items' => [['Soto Ayam Kampung', 4], ['Nasi Goreng Spesial', 2]]],
            ['code' => 'TRX-20261107-0004', 'days_ago' => 11, 'payment' => 'cash', 'items' => [['Rendang Ayam', 3]]],
            ['code' => 'TRX-20261108-0005', 'days_ago' => 10, 'payment' => 'cash', 'items' => [['Ayam Kecap', 5], ['Nasi Ayam Geprek', 2]]],
            ['code' => 'TRX-20261109-0006', 'days_ago' => 9, 'payment' => 'transfer', 'items' => [['Nasi Goreng Spesial', 6]]],
        ];

        foreach ($sales as $sale) {
            $this->createMenuSale($sale['code'], $sale['days_ago'], $sale['payment'], $sale['items']);
        }
    }

    /**
     * @param  list<array{string, int}>  $items
     */
    private function createMenuSale(string $code, int $daysAgo, string $payment, array $items): void
    {
        if (DB::table('sales')->where('transaction_code', $code)->exists()) {
            return;
        }

        $completedAt = $this->now->copy()->subDays($daysAgo);
        $total = 0;

        foreach ($items as [$menuName, $quantity]) {
            $menu = DB::table('menus')->where('name', $menuName)->first();

            if ($menu !== null) {
                $total += $quantity * (float) $menu->selling_price;
            }
        }

        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => $code,
            'type' => 'menu',
            'status' => 'completed',
            'payment_method' => $payment,
            'total' => $total,
            'note' => null,
            'created_by' => $this->stafId,
            'completed_at' => $completedAt,
            'stock_deducted_at' => $completedAt,
            'reversal_done_at' => null,
            'created_at' => $completedAt,
            'updated_at' => $completedAt,
        ]);

        $this->addStatusHistory($saleId, null, 'pending', $completedAt);
        $this->addStatusHistory($saleId, 'pending', 'processing', $completedAt);
        $this->addStatusHistory($saleId, 'processing', 'completed', $completedAt);

        foreach ($items as [$menuName, $quantity]) {
            $menu = DB::table('menus')->where('name', $menuName)->first();

            if ($menu === null) {
                continue;
            }

            $hpp = $this->hppForMenu((int) $menu->id);

            DB::table('sale_items')->insert([
                'sale_id' => $saleId,
                'menu_id' => $menu->id,
                'menu_name_snapshot' => $menu->name,
                'quantity' => $quantity,
                'unit_price_snapshot' => $menu->selling_price,
                'hpp_snapshot' => $hpp,
                'subtotal' => $quantity * (float) $menu->selling_price,
                'created_at' => $completedAt,
                'updated_at' => $completedAt,
            ]);

            $this->deductRecipe($saleId, (int) $menu->id, (float) $quantity, $code, $completedAt);
        }
    }

    private function seedManualSale(): void
    {
        $code = 'TRX-MANUAL-0001';

        if (DB::table('sales')->where('transaction_code', $code)->exists()) {
            return;
        }

        $completedAt = $this->now->copy()->subDays(8);

        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => $code,
            'type' => 'manual',
            'status' => 'completed',
            'payment_method' => 'cash',
            'total' => 150000,
            'note' => 'Penjualan manual tanpa rincian menu; HPP tidak tersedia.',
            'created_by' => $this->stafId,
            'completed_at' => $completedAt,
            'stock_deducted_at' => null,
            'reversal_done_at' => null,
            'created_at' => $completedAt,
            'updated_at' => $completedAt,
        ]);

        $this->addStatusHistory($saleId, null, 'pending', $completedAt);
        $this->addStatusHistory($saleId, 'pending', 'completed', $completedAt);
    }

    private function seedCancelledSale(): void
    {
        $code = 'TRX-20261110-0007';

        if (DB::table('sales')->where('transaction_code', $code)->exists()) {
            return;
        }

        $transactionAt = $this->now->copy()->subDays(7);
        $menu = DB::table('menus')->where('name', 'Nasi Ayam Geprek')->first();

        if ($menu === null) {
            return;
        }

        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => $code,
            'type' => 'menu',
            'status' => 'cancelled',
            'payment_method' => 'cash',
            'total' => 3 * (float) $menu->selling_price,
            'note' => 'Dibatalkan pelanggan; stok sudah direversal.',
            'created_by' => $this->stafId,
            'completed_at' => $transactionAt,
            'stock_deducted_at' => $transactionAt,
            'reversal_done_at' => $transactionAt,
            'created_at' => $transactionAt,
            'updated_at' => $transactionAt,
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'menu_id' => $menu->id,
            'menu_name_snapshot' => $menu->name,
            'quantity' => 3,
            'unit_price_snapshot' => $menu->selling_price,
            'hpp_snapshot' => $this->hppForMenu((int) $menu->id),
            'subtotal' => 3 * (float) $menu->selling_price,
            'created_at' => $transactionAt,
            'updated_at' => $transactionAt,
        ]);

        $this->addStatusHistory($saleId, null, 'pending', $transactionAt);
        $this->addStatusHistory($saleId, 'pending', 'processing', $transactionAt);
        $this->addStatusHistory($saleId, 'processing', 'completed', $transactionAt);
        $this->addStatusHistory($saleId, 'completed', 'cancelled', $transactionAt, 'Dibatalkan pelanggan');

        $this->deductRecipe($saleId, (int) $menu->id, 3, $code, $transactionAt);
        $this->reverseRecipe($saleId, (int) $menu->id, 3, $code, $transactionAt);
    }

    private function seedExpenses(): void
    {
        $expenses = [
            ['Listrik/Air', 350000, 6, 'Tagihan listrik dan air bulan ini'],
            ['Gas', 200000, 5, 'Isi ulang tabung gas'],
            ['Gaji', 1500000, 4, 'Upah staf dapur'],
            ['Transportasi', 120000, 3, 'Pengiriman bahan baku'],
            ['Perawatan', 175000, 2, 'Servis alat dapur'],
        ];

        foreach ($expenses as [$categoryName, $amount, $daysAgo, $description]) {
            $categoryId = DB::table('expense_categories')->where('name', $categoryName)->value('id');

            if ($categoryId === null) {
                continue;
            }

            if (DB::table('expenses')
                ->where('expense_category_id', $categoryId)
                ->where('description', $description)
                ->exists()) {
                continue;
            }

            DB::table('expenses')->insert([
                'expense_category_id' => $categoryId,
                'expense_date' => $this->now->copy()->subDays($daysAgo)->toDateString(),
                'amount' => $amount,
                'description' => $description,
                'created_by' => $this->adminId,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function hppForMenu(int $menuId): float
    {
        $recipeId = DB::table('recipes')
            ->where('menu_id', $menuId)
            ->where('version', 1)
            ->value('id');

        if ($recipeId === null) {
            return 0;
        }

        return (float) DB::table('recipe_items')
            ->join('ingredients', 'ingredients.id', '=', 'recipe_items.ingredient_id')
            ->where('recipe_items.recipe_id', $recipeId)
            ->selectRaw('SUM(recipe_items.quantity * ingredients.average_cost) as total')
            ->value('total') ?? 0;
    }

    private function addStatusHistory(
        int $saleId,
        ?string $fromStatus,
        string $toStatus,
        Carbon $at,
        ?string $note = null,
    ): void {
        DB::table('sale_status_histories')->insert([
            'sale_id' => $saleId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $this->stafId,
            'note' => $note,
            'created_at' => $at,
        ]);
    }

    private function deductRecipe(int $saleId, int $menuId, float $portions, string $code, Carbon $at): void
    {
        $this->applyRecipe($saleId, $menuId, $portions, $code, $at, 'sale_out');
    }

    private function reverseRecipe(int $saleId, int $menuId, float $portions, string $code, Carbon $at): void
    {
        $this->applyRecipe($saleId, $menuId, $portions, $code, $at, 'reversal');
    }

    private function applyRecipe(
        int $saleId,
        int $menuId,
        float $portions,
        string $code,
        Carbon $at,
        string $movementType,
    ): void {
        $recipeId = DB::table('recipes')
            ->where('menu_id', $menuId)
            ->where('version', 1)
            ->value('id');

        if ($recipeId === null) {
            return;
        }

        $referenceType = $movementType === 'reversal' ? 'SaleReversal' : 'Sale';

        foreach (DB::table('recipe_items')->where('recipe_id', $recipeId)->get() as $item) {
            $quantity = (float) $item->quantity * $portions;

            $this->move(
                (int) $item->ingredient_id,
                $movementType,
                $movementType === 'reversal' ? $quantity : 0,
                $movementType === 'reversal' ? 0 : $quantity,
                $code,
                $referenceType,
                $saleId,
                $at,
            );
        }
    }

    private function move(
        int $ingredientId,
        string $movementType,
        float $quantityIn,
        float $quantityOut,
        string $note,
        ?string $referenceType,
        ?int $referenceId,
        Carbon $at,
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
            'created_by' => $this->stafId,
            'created_at' => $at,
        ]);

        DB::table('ingredients')->where('id', $ingredientId)->update([
            'current_stock' => $balanceAfter,
            'updated_at' => $at,
        ]);
    }
}
