<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_contract_tables_exist(): void
    {
        $tables = [
            'users', 'menus', 'ingredients', 'expense_categories',
            'recipes', 'recipe_items', 'stock_movements', 'stock_adjustments',
            'purchases', 'purchase_items', 'sales', 'sale_items',
            'sale_status_histories', 'expenses', 'production_forecasts',
            'production_forecast_items', 'settings', 'activity_logs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} harus ada");
        }
    }

    public function test_seeder_creates_complete_dataset_and_is_idempotent(): void
    {
        $this->seed();
        $counts = $this->tableCounts();

        $this->assertSame(4, $counts['users']);
        $this->assertSame(6, $counts['menus']);
        $this->assertSame(8, $counts['ingredients']);
        $this->assertSame(6, $counts['recipes']);
        $this->assertSame(2, $counts['purchases']);
        $this->assertSame(8, $counts['sales']);
        $this->assertSame(5, $counts['expenses']);
        $this->assertSame(2, $counts['production_forecasts']);
        $this->assertSame(4, $counts['settings']);
        $this->assertGreaterThan(0, $counts['recipe_items']);
        $this->assertGreaterThan(0, $counts['stock_movements']);
        $this->assertGreaterThan(0, $counts['sale_items']);
        $this->assertGreaterThan(0, $counts['sale_status_histories']);

        $this->seed();

        $this->assertSame($counts, $this->tableCounts());
    }

    public function test_stock_ledger_is_consistent_with_current_stock(): void
    {
        $this->seed();

        foreach (DB::table('ingredients')->get() as $ingredient) {
            $ledger = DB::table('stock_movements')
                ->where('ingredient_id', $ingredient->id)
                ->selectRaw('SUM(quantity_in) AS total_in, SUM(quantity_out) AS total_out')
                ->first();

            $expected = (float) $ledger->total_in - (float) $ledger->total_out;

            $this->assertEqualsWithDelta(
                (float) $ingredient->current_stock,
                $expected,
                0.001,
                "Stok {$ingredient->name} tidak sesuai ledger",
            );
        }

        foreach (DB::table('stock_movements')->get() as $movement) {
            $expected = (float) $movement->balance_before
                + (float) $movement->quantity_in
                - (float) $movement->quantity_out;

            $this->assertEqualsWithDelta((float) $movement->balance_after, $expected, 0.001);
        }
    }

    public function test_manual_sale_has_no_items_and_no_stock_impact(): void
    {
        $this->seed();

        $sale = DB::table('sales')->where('type', 'manual')->first();

        $this->assertNotNull($sale);
        $this->assertSame(0, DB::table('sale_items')->where('sale_id', $sale->id)->count());
        $this->assertSame(0, DB::table('stock_movements')
            ->where('reference_type', 'Sale')
            ->where('reference_id', $sale->id)
            ->count());
    }

    public function test_menu_sale_has_snapshots_and_status_history(): void
    {
        $this->seed();

        $sale = DB::table('sales')->where('transaction_code', 'TRX-20261104-0001')->first();
        $item = DB::table('sale_items')->where('sale_id', $sale->id)->first();

        $this->assertNotEmpty($item->menu_name_snapshot);
        $this->assertGreaterThan(0, (float) $item->unit_price_snapshot);
        $this->assertEqualsWithDelta(
            (float) $item->quantity * (float) $item->unit_price_snapshot,
            (float) $item->subtotal,
            0.01,
        );
        $this->assertSame(3, DB::table('sale_status_histories')->where('sale_id', $sale->id)->count());
    }

    public function test_cancelled_sale_has_reversal_movement(): void
    {
        $this->seed();

        $sale = DB::table('sales')->where('transaction_code', 'TRX-20261110-0007')->first();

        $this->assertSame('cancelled', $sale->status);
        $this->assertNotNull($sale->reversal_done_at);
        $this->assertGreaterThan(0, DB::table('stock_movements')
            ->where('reference_type', 'SaleReversal')
            ->where('reference_id', $sale->id)
            ->count());
    }

    public function test_forecast_separates_predicted_and_adjusted_values(): void
    {
        $this->seed();

        $adjusted = DB::table('production_forecast_items')
            ->whereNotNull('adjusted_quantity')
            ->get();

        $this->assertGreaterThan(0, $adjusted->count());

        foreach ($adjusted as $item) {
            $this->assertNotEmpty($item->adjustment_note);
        }

        $this->assertSame(1, DB::table('production_forecasts')->where('data_status', 'terbatas')->count());
    }

    /**
     * @return array<string, int>
     */
    private function tableCounts(): array
    {
        $tables = [
            'users', 'menus', 'ingredients', 'expense_categories', 'recipes',
            'recipe_items', 'stock_movements', 'stock_adjustments', 'purchases',
            'purchase_items', 'sales', 'sale_items', 'sale_status_histories',
            'expenses', 'production_forecasts', 'production_forecast_items',
            'settings', 'activity_logs',
        ];

        $counts = [];

        foreach ($tables as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        return $counts;
    }
}
