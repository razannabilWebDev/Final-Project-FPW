<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_movement_history_is_available_to_admin_and_pengelola_only(): void
    {
        $ingredient = Ingredient::factory()->create(['current_stock' => '10.000']);

        foreach ([UserRole::Admin, UserRole::Pengelola] as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)
                ->get(route('stock.movements'))
                ->assertOk()
                ->assertSee('Riwayat Pergerakan Stok');
        }

        $staf = User::factory()->create();

        $this->actingAs($staf)->get(route('stock.movements'))->assertForbidden();
    }

    #[DataProvider('allRoles')]
    public function test_all_roles_can_record_physical_adjustment(UserRole $role): void
    {
        $user = User::factory()->state(['role' => $role])->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => '20.000']);

        $response = $this->actingAs($user)->post(route('stock.adjustments.store'), [
            'ingredient_id' => $ingredient->id,
            'physical_quantity' => '18.000',
            'reason' => 'Hitung fisik harian',
        ]);

        $response->assertRedirect(route('stock.adjustments.create'));
        $this->assertSame('18.000', $ingredient->fresh()->current_stock);
        $this->assertDatabaseHas('stock_adjustments', [
            'ingredient_id' => $ingredient->id,
            'reason' => 'Hitung fisik harian',
            'created_by' => $user->id,
        ]);
    }

    public function test_admin_and_staf_can_record_waste_but_pengelola_cannot(): void
    {
        foreach ([UserRole::Admin, UserRole::Staf] as $role) {
            $user = User::factory()->state(['role' => $role])->create();
            $ingredient = Ingredient::factory()->create(['current_stock' => '10.000']);

            $this->actingAs($user)->post(route('stock.waste.store'), [
                'ingredient_id' => $ingredient->id,
                'quantity' => '2.500',
                'reason' => 'Bahan rusak',
            ])->assertRedirect(route('stock.waste.create'));

            $this->assertSame('7.500', $ingredient->fresh()->current_stock);
            $this->assertDatabaseHas('stock_movements', [
                'ingredient_id' => $ingredient->id,
                'movement_type' => 'waste',
                'quantity_out' => '2.500',
            ]);
        }

        $pengelola = User::factory()->pengelola()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => '10.000']);

        $this->actingAs($pengelola)->post(route('stock.waste.store'), [
            'ingredient_id' => $ingredient->id,
            'quantity' => '1.000',
            'reason' => 'Bahan rusak',
        ])->assertForbidden();

        $this->assertSame('10.000', $ingredient->fresh()->current_stock);
    }

    public function test_movement_history_can_be_filtered_by_type(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create();
        StockMovement::factory()->create([
            'ingredient_id' => $ingredient->id,
            'movement_type' => 'waste',
        ]);
        StockMovement::factory()->create([
            'ingredient_id' => $ingredient->id,
            'movement_type' => 'purchase_in',
        ]);

        $this->actingAs($admin)
            ->get(route('stock.movements', ['movement_type' => 'waste']))
            ->assertOk()
            ->assertViewHas('selectedType', 'waste')
            ->assertSee('Susut/Rusak')
            ->assertViewHas('movements', fn ($movements): bool => $movements->total() === 1);
    }

    public function test_waste_above_available_stock_redirects_with_error_and_keeps_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create(['current_stock' => '5.000']);

        $this->actingAs($admin)->post(route('stock.waste.store'), [
            'ingredient_id' => $ingredient->id,
            'quantity' => '9.000',
            'reason' => 'Bahan busuk',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertSame('5.000', $ingredient->fresh()->current_stock);
        $this->assertSame(0, $ingredient->stockMovements()->where('movement_type', 'waste')->count());
    }

    public function test_waste_request_requires_complete_input(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('stock.waste.store'), [])
            ->assertSessionHasErrors(['ingredient_id', 'quantity', 'reason']);
    }

    /**
     * @return array<string, array{UserRole}>
     */
    public static function allRoles(): array
    {
        return [
            'admin' => [UserRole::Admin],
            'pengelola' => [UserRole::Pengelola],
            'staf' => [UserRole::Staf],
        ];
    }
}
