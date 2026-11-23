<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Ingredient;
use App\Models\User;
use App\Services\IngredientService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_in_updates_stock_average_cost_and_writes_ledger(): void
    {
        $service = app(StockService::class);
        $actor = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create([
            'current_stock' => '0.000',
            'average_cost' => '0.00',
        ]);

        $service->recordIn($ingredient, '10.000', 'Purchase', 1, $actor, '2.00', 'Pembelian pertama');
        $service->recordIn($ingredient, '10.000', 'Purchase', 2, $actor, '4.00', 'Pembelian kedua');

        $ingredient->refresh();

        $this->assertSame('20.000', $ingredient->current_stock);
        $this->assertSame('3.00', $ingredient->average_cost);
        $this->assertSame(2, $ingredient->stockMovements()->where('movement_type', 'purchase_in')->count());
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $ingredient->id,
            'movement_type' => 'purchase_in',
            'quantity_in' => '10.000',
            'balance_before' => '10.000',
            'balance_after' => '20.000',
            'created_by' => $actor->id,
        ]);
    }

    public function test_sale_deduction_is_idempotent_for_the_same_sale(): void
    {
        $service = app(StockService::class);
        $ingredient = Ingredient::factory()->create(['current_stock' => '100.000']);

        $service->deductForSale(50, [['ingredient_id' => $ingredient->id, 'quantity' => '60.000']]);
        $service->deductForSale(50, [['ingredient_id' => $ingredient->id, 'quantity' => '60.000']]);

        $this->assertSame('40.000', $ingredient->fresh()->current_stock);
        $this->assertSame(1, $ingredient->stockMovements()->where('movement_type', 'sale_out')->count());
    }

    public function test_insufficient_stock_rejects_entire_deduction_without_partial_writes(): void
    {
        $service = app(StockService::class);
        $scarce = Ingredient::factory()->create(['name' => 'Cabai', 'current_stock' => '20.000']);
        $enough = Ingredient::factory()->create(['name' => 'Beras', 'current_stock' => '100.000']);

        try {
            $service->deductForSale(51, [
                ['ingredient_id' => $scarce->id, 'quantity' => '30.000'],
                ['ingredient_id' => $enough->id, 'quantity' => '30.000'],
            ]);
            $this->fail('Deduksi seharusnya ditolak ketika stok tidak cukup.');
        } catch (InsufficientStockException $exception) {
            $this->assertSame('Cabai', $exception->shortages[0]['name']);
            $this->assertSame('20.000', $exception->shortages[0]['available']);
        }

        $this->assertSame('20.000', $scarce->fresh()->current_stock);
        $this->assertSame('100.000', $enough->fresh()->current_stock);
        $this->assertSame(0, $scarce->stockMovements()->where('movement_type', 'sale_out')->count());
        $this->assertSame(0, $enough->stockMovements()->where('movement_type', 'sale_out')->count());
    }

    public function test_reversal_restores_deducted_quantity_once(): void
    {
        $service = app(StockService::class);
        $ingredient = Ingredient::factory()->create(['current_stock' => '100.000']);

        $service->deductForSale(52, [['ingredient_id' => $ingredient->id, 'quantity' => '60.000']]);
        $service->reverseForSale(52);
        $service->reverseForSale(52);

        $this->assertSame('100.000', $ingredient->fresh()->current_stock);
        $this->assertSame(1, $ingredient->stockMovements()->where('movement_type', 'reversal')->count());
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'Sale',
            'reference_id' => 52,
            'movement_type' => 'reversal',
            'quantity_in' => '60.000',
            'balance_before' => '40.000',
            'balance_after' => '100.000',
        ]);
    }

    public function test_physical_adjustment_records_system_physical_and_difference(): void
    {
        $service = app(StockService::class);
        $actor = User::factory()->pengelola()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => '20.000']);

        $adjustment = $service->adjustPhysical($ingredient, '18.000', 'Hasil hitung fisik', $actor);

        $this->assertSame('20.000', $adjustment->system_quantity);
        $this->assertSame('18.000', $adjustment->physical_quantity);
        $this->assertSame('-2.000', $adjustment->difference);
        $this->assertSame('18.000', $ingredient->fresh()->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'StockAdjustment',
            'reference_id' => $adjustment->id,
            'movement_type' => 'adjustment',
            'quantity_out' => '2.000',
            'created_by' => $actor->id,
        ]);
    }

    public function test_waste_above_available_stock_is_rejected(): void
    {
        $service = app(StockService::class);
        $actor = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => '5.000']);

        try {
            $service->recordWaste($ingredient, '9.000', 'Bahan busuk', $actor);
            $this->fail('Stok susut seharusnya ditolak jika melebihi stok tersedia.');
        } catch (InsufficientStockException $exception) {
            $this->assertSame('9.000', $exception->shortages[0]['required']);
        }

        $this->assertSame('5.000', $ingredient->fresh()->current_stock);
        $this->assertSame(0, $ingredient->stockMovements()->where('movement_type', 'waste')->count());
    }

    public function test_creating_ingredient_with_opening_stock_writes_a_ledger_row(): void
    {
        $ingredient = app(IngredientService::class)->create([
            'name' => 'Ayam Fillet',
            'base_unit' => 'gram',
            'current_stock' => '25.000',
            'minimum_stock' => '5.000',
            'average_cost' => '40.00',
        ]);

        $this->assertSame('25.000', $ingredient->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $ingredient->id,
            'movement_type' => 'adjustment',
            'quantity_in' => '25.000',
            'balance_before' => '0.000',
            'balance_after' => '25.000',
        ]);
    }
}
