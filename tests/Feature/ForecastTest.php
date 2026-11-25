<?php

namespace Tests\Feature;

use App\Models\ProductionForecastItem;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForecastTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_forecast_from_completed_menu_sales(): void
    {
        $admin = User::factory()->admin()->create();
        $menuId = $this->createMenu();
        $this->createCompletedMenuSale($menuId, Carbon::parse('2026-11-22'), 10);
        $this->createSale($menuId, Carbon::parse('2026-11-21'), 99, 'pending');
        $this->createSale($menuId, Carbon::parse('2026-11-20'), 99, 'completed', 'manual');

        $this->actingAs($admin)
            ->post(route('forecast.generate'))
            ->assertRedirect(route('forecast.index'));

        $forecast = DB::table('production_forecasts')->first();
        $this->assertNotNull($forecast);
        $this->assertSame('terbatas', $forecast->data_status);
        $this->assertSame(28, $forecast->data_window);
        $this->assertSame(1, DB::table('production_forecast_items')->count());
        $this->assertDatabaseHas('production_forecast_items', [
            'menu_id' => $menuId,
            'predicted_quantity' => '0.357',
            'adjusted_quantity' => null,
        ]);
    }

    public function test_generating_a_forecast_twice_is_idempotent(): void
    {
        $admin = User::factory()->admin()->create();
        $this->createCompletedMenuSale($this->createMenu(), Carbon::parse('2026-11-22'), 10);

        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();
        $this->assertSame(1, DB::table('production_forecasts')->count());

        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();

        $this->assertSame(1, DB::table('production_forecasts')->count());
        $this->assertSame(1, DB::table('production_forecast_items')->count());
    }

    public function test_admin_can_adjust_without_overwriting_the_original_prediction(): void
    {
        $admin = User::factory()->admin()->create();
        $this->createCompletedMenuSale($this->createMenu(), Carbon::parse('2026-11-22'), 10);
        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();

        $item = ProductionForecastItem::firstOrFail();
        $original = (string) $item->predicted_quantity;

        $this->actingAs($admin)
            ->put(route('forecast.adjust', $item), [
                'adjusted_quantity' => '12.000',
                'adjustment_note' => 'Permintaan naik menjelang akhir pekan.',
            ])
            ->assertRedirect(route('forecast.index'));

        $item->refresh();
        $this->assertSame($original, (string) $item->predicted_quantity);
        $this->assertSame('12.000', (string) $item->adjusted_quantity);
        $this->assertSame('Permintaan naik menjelang akhir pekan.', $item->adjustment_note);
        $this->assertSame($admin->id, $item->forecast->adjusted_by);
    }

    public function test_pengelola_and_staf_cannot_generate_or_adjust(): void
    {
        $menuId = $this->createMenu();
        $this->createCompletedMenuSale($menuId, Carbon::parse('2026-11-22'), 10);
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();
        $item = ProductionForecastItem::firstOrFail();

        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)->post(route('forecast.generate'))->assertForbidden();
            $this->actingAs($user)->put(route('forecast.adjust', $item), [
                'adjusted_quantity' => '5',
            ])->assertForbidden();
        }
    }

    public function test_all_roles_can_view_the_forecast_but_only_admin_can_adjust(): void
    {
        $admin = User::factory()->admin()->create();
        $this->createCompletedMenuSale($this->createMenu(), Carbon::parse('2026-11-22'), 10);
        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();

        foreach (UserRole::cases() as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $response = $this->actingAs($user)
                ->get(route('forecast.index'))
                ->assertOk()
                ->assertSee('Rekomendasi Produksi');

            if ($role === UserRole::Admin) {
                $response->assertSee('Simpan');
            } else {
                $response->assertDontSee('Simpan');
            }
        }
    }

    public function test_ingredient_estimate_uses_recipe_and_adjusted_portions(): void
    {
        $admin = User::factory()->admin()->create();
        $menuId = $this->createMenu();
        $ingredientId = DB::table('ingredients')->insertGetId([
            'name' => 'Beras',
            'base_unit' => 'gram',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $recipeId = DB::table('recipes')->insertGetId([
            'menu_id' => $menuId,
            'version' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('recipe_items')->insert([
            'recipe_id' => $recipeId,
            'ingredient_id' => $ingredientId,
            'quantity' => '150.000',
            'unit' => 'gram',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createCompletedMenuSale($menuId, Carbon::parse('2026-11-22'), 10);

        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();
        $item = ProductionForecastItem::firstOrFail();
        $this->actingAs($admin)->put(route('forecast.adjust', $item), ['adjusted_quantity' => '10'])->assertRedirect();

        $this->actingAs($admin)
            ->get(route('forecast.index'))
            ->assertOk()
            ->assertSee('Beras')
            ->assertSee('1.500,0')
            ->assertSee('gram');
    }

    public function test_missing_recipe_is_reported_without_fabricating_numbers(): void
    {
        $admin = User::factory()->admin()->create();
        $this->createCompletedMenuSale($this->createMenu(), Carbon::parse('2026-11-22'), 10);

        $this->actingAs($admin)->post(route('forecast.generate'))->assertRedirect();

        $this->actingAs($admin)
            ->get(route('forecast.index'))
            ->assertOk()
            ->assertSee('Resep belum tersedia');
    }

    private function createMenu(string $name = 'Nasi Ayam Geprek'): int
    {
        return (int) DB::table('menus')->insertGetId([
            'name' => $name,
            'selling_price' => 25000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createCompletedMenuSale(int $menuId, Carbon $date, float $quantity): void
    {
        $this->createSale($menuId, $date, $quantity, 'completed', 'menu');
    }

    private function createSale(
        int $menuId,
        Carbon $date,
        float $quantity,
        string $status,
        string $type = 'menu',
    ): void {
        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => 'TRX-'.Str::upper(Str::random(8)),
            'type' => $type,
            'status' => $status,
            'payment_method' => 'cash',
            'total' => $quantity * 25000,
            'completed_at' => $status === 'completed' ? $date : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'menu_id' => $menuId,
            'menu_name_snapshot' => 'Nasi Ayam Geprek',
            'quantity' => $quantity,
            'unit_price_snapshot' => 25000,
            'hpp_snapshot' => 10000,
            'subtotal' => $quantity * 25000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
