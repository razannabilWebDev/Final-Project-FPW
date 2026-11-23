<?php

namespace Tests\Feature;

use App\Enums\SaleStatus;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tabel pendukung (milik A2/A3) dibuat manual di test karena belum ada di branch ini.
        Schema::create('menus', function ($table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('selling_price', 12, 2);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('ingredients', function ($table): void {
            $table->id();
            $table->string('name');
            $table->string('base_unit');
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('minimum_stock', 12, 3)->default(0);
            $table->decimal('average_cost', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('recipes', function ($table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus');
            $table->text('notes')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('recipe_items', function ($table): void {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes');
            $table->foreignId('ingredient_id')->constrained('ingredients');
            $table->decimal('quantity', 12, 3);
            $table->string('unit');
            $table->timestamps();
        });
        Schema::create('stock_movements', function ($table): void {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients');
            $table->string('movement_type');
            $table->decimal('quantity_in', 12, 3)->default(0);
            $table->decimal('quantity_out', 12, 3)->default(0);
            $table->decimal('balance_before', 12, 3);
            $table->decimal('balance_after', 12, 3);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    private function actingUser(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }

    public function test_manual_sale_has_no_items_and_does_not_touch_stock(): void
    {
        $user = $this->actingUser();

        $response = $this->post(route('sales.store'), [
            'type' => 'manual',
            'payment_method' => 'cash',
            'total_nominal' => 125000,
            'note' => 'Penjualan manual',
        ]);

        $sale = Sale::firstOrFail();
        $response->assertRedirect(route('sales.show', $sale));
        $this->assertSame('manual', $sale->type);
        $this->assertSame('125000.00', (string) $sale->total);
        $this->assertCount(0, $sale->items);
        $this->assertNull($sale->stock_deducted_at);
        $this->assertSame('TRX-'.now()->format('Ymd').'-0001', $sale->transaction_code);
    }

    public function test_multi_item_sale_snapshots_name_price_and_hpp(): void
    {
        $user = $this->actingUser();
        $menu = DB::table('menus')->insertGetId([
            'name' => 'Nasi Ayam', 'selling_price' => 24000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $ingredient = DB::table('ingredients')->insertGetId([
            'name' => 'Ayam', 'base_unit' => 'kg', 'current_stock' => 10,
            'minimum_stock' => 1, 'average_cost' => 50000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $recipe = DB::table('recipes')->insertGetId([
            'menu_id' => $menu, 'version' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('recipe_items')->insert([
            'recipe_id' => $recipe, 'ingredient_id' => $ingredient,
            'quantity' => 0.2, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->post(route('sales.store'), [
            'type' => 'menu',
            'payment_method' => 'qris',
            'items' => [[
                'menu_id' => $menu,
                'menu_name' => 'Nasi Ayam',
                'unit_price' => 24000,
                'quantity' => 2,
            ]],
        ])->assertRedirect();

        $sale = Sale::firstOrFail();
        $this->assertSame('48000.00', (string) $sale->total);
        $item = $sale->items->first();
        $this->assertSame('Nasi Ayam', $item->menu_name_snapshot);
        $this->assertSame('24000.00', (string) $item->unit_price_snapshot);
        $this->assertSame('10000.00', (string) $item->hpp_snapshot);
        $this->assertSame('48000.00', (string) $item->subtotal);
    }

    public function test_processing_deducts_stock_once_and_cancelled_reverses_once(): void
    {
        $user = $this->actingUser();
        $menu = DB::table('menus')->insertGetId([
            'name' => 'Nasi Ayam', 'selling_price' => 24000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $ingredient = DB::table('ingredients')->insertGetId([
            'name' => 'Ayam', 'base_unit' => 'kg', 'current_stock' => 10,
            'minimum_stock' => 1, 'average_cost' => 50000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $recipe = DB::table('recipes')->insertGetId([
            'menu_id' => $menu, 'version' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('recipe_items')->insert([
            'recipe_id' => $recipe, 'ingredient_id' => $ingredient,
            'quantity' => 0.2, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->post(route('sales.store'), [
            'type' => 'menu', 'payment_method' => 'cash',
            'items' => [['menu_id' => $menu, 'menu_name' => 'Nasi Ayam', 'unit_price' => 24000, 'quantity' => 2]],
        ]);
        $sale = Sale::firstOrFail();

        // Status processing → potong 0.4 kg (2 porsi × 0.2).
        $this->post(route('sales.update-status', $sale), ['status' => 'processing']);
        $this->assertEquals(9.6, (float) DB::table('ingredients')->where('id', $ingredient)->value('current_stock'));
        $this->assertNotNull($sale->fresh()->stock_deducted_at);
        $this->assertSame(1, DB::table('stock_movements')->where('reference_type', 'Sale')->where('movement_type', 'sale_out')->count());

        // Ulangi processing → tidak memotong lagi (idempoten).
        $this->post(route('sales.update-status', $sale), ['status' => 'processing']);
        $this->assertEquals(9.6, (float) DB::table('ingredients')->where('id', $ingredient)->value('current_stock'));
        $this->assertSame(1, DB::table('stock_movements')->where('movement_type', 'sale_out')->count());

        // Batal → reversal sekali, stok kembali 10.
        $this->post(route('sales.update-status', $sale), ['status' => 'cancelled']);
        $this->assertEquals(10.0, (float) DB::table('ingredients')->where('id', $ingredient)->value('current_stock'));
        $this->assertNotNull($sale->fresh()->reversal_done_at);
        $this->assertSame(1, DB::table('stock_movements')->where('movement_type', 'reversal')->count());

        // Ulangi batal → reversal tidak dobel.
        $this->post(route('sales.update-status', $sale), ['status' => 'cancelled']);
        $this->assertEquals(10.0, (float) DB::table('ingredients')->where('id', $ingredient)->value('current_stock'));
        $this->assertSame(1, DB::table('stock_movements')->where('movement_type', 'reversal')->count());

        $this->assertCount(3, $sale->statusHistories);
    }

    public function test_insufficient_stock_fails_without_negative_stock(): void
    {
        $user = $this->actingUser();
        $menu = DB::table('menus')->insertGetId([
            'name' => 'Nasi Ayam', 'selling_price' => 24000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $ingredient = DB::table('ingredients')->insertGetId([
            'name' => 'Ayam', 'base_unit' => 'kg', 'current_stock' => 0.1,
            'minimum_stock' => 1, 'average_cost' => 50000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $recipe = DB::table('recipes')->insertGetId([
            'menu_id' => $menu, 'version' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('recipe_items')->insert([
            'recipe_id' => $recipe, 'ingredient_id' => $ingredient,
            'quantity' => 0.2, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->post(route('sales.store'), [
            'type' => 'menu', 'payment_method' => 'cash',
            'items' => [['menu_id' => $menu, 'menu_name' => 'Nasi Ayam', 'unit_price' => 24000, 'quantity' => 2]],
        ]);
        $sale = Sale::firstOrFail();

        $response = $this->post(route('sales.update-status', $sale), ['status' => 'processing']);
        $response->assertSessionHas('error');
        $this->assertEquals(0.1, (float) DB::table('ingredients')->where('id', $ingredient)->value('current_stock'));
        $this->assertSame(0, DB::table('stock_movements')->count());
        $this->assertSame('pending', $sale->fresh()->status->value);
    }

    public function test_completed_sets_completed_at(): void
    {
        $this->actingUser();
        $sale = Sale::factory()->create();

        $this->post(route('sales.update-status', $sale), ['status' => 'completed']);

        $this->assertNotNull($sale->fresh()->completed_at);
        $this->assertSame(SaleStatus::Completed, $sale->fresh()->status);
    }

    public function test_guest_cannot_access_sales(): void
    {
        $this->get(route('sales.index'))->assertRedirect();
    }
}
