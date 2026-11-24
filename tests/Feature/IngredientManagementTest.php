<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IngredientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_ingredient_with_opening_stock(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('ingredients.store'), [
            'name' => 'Ayam Fillet',
            'base_unit' => 'gram',
            'current_stock' => '25.000',
            'minimum_stock' => '5.000',
            'average_cost' => '40.00',
        ]);

        $ingredient = Ingredient::query()->where('name', 'Ayam Fillet')->firstOrFail();

        $response->assertRedirect(route('ingredients.index'));
        $this->assertSame('25.000', $ingredient->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $ingredient->id,
            'movement_type' => 'adjustment',
            'quantity_in' => '25.000',
        ]);
    }

    #[DataProvider('nonAdminRoles')]
    public function test_manage_ingredients_is_forbidden_for_role(UserRole $role): void
    {
        $user = User::factory()->state(['role' => $role])->create();

        $this->actingAs($user)->get(route('ingredients.create'))->assertForbidden();
        $this->actingAs($user)->post(route('ingredients.store'), [
            'name' => 'Bahan Terlarang',
            'base_unit' => 'gram',
            'current_stock' => '1',
            'minimum_stock' => '0',
            'average_cost' => '1',
        ])->assertForbidden();
    }

    public function test_all_roles_can_view_ingredient_list(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Beras Premium']);

        foreach (UserRole::cases() as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)
                ->get(route('ingredients.index'))
                ->assertOk()
                ->assertSee('Beras Premium');
        }
    }

    public function test_update_does_not_change_running_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create([
            'name' => 'Minyak',
            'current_stock' => '12.500',
        ]);

        $this->actingAs($admin)->put(route('ingredients.update', $ingredient), [
            'name' => 'Minyak Goreng',
            'base_unit' => 'ml',
            'minimum_stock' => '3.000',
            'average_cost' => '18.00',
        ])->assertRedirect(route('ingredients.index'));

        $ingredient->refresh();

        $this->assertSame('Minyak Goreng', $ingredient->name);
        $this->assertSame('12.500', $ingredient->current_stock);
    }

    public function test_deactivation_keeps_ingredient_in_database(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create();

        $this->actingAs($admin)->patch(route('ingredients.status', $ingredient), [
            'is_active' => 1,
        ])->assertRedirect(route('ingredients.index'));

        $this->assertFalse($ingredient->fresh()->is_active);
        $this->assertModelExists($ingredient);
    }

    public function test_index_filters_ingredients_by_search_and_stock_status(): void
    {
        $admin = User::factory()->admin()->create();
        Ingredient::factory()->create(['name' => 'Daging Ayam', 'current_stock' => '10.000', 'minimum_stock' => '2.000']);
        Ingredient::factory()->create(['name' => 'Cabai Rawit', 'current_stock' => '1.000', 'minimum_stock' => '2.000']);
        Ingredient::factory()->create(['name' => 'Daun Salad', 'current_stock' => '0.000', 'minimum_stock' => '1.000']);

        $this->actingAs($admin)
            ->get(route('ingredients.index', ['search' => 'Cabai']))
            ->assertOk()
            ->assertSee('Cabai Rawit')
            ->assertViewHas('ingredients', fn ($ingredients): bool => $ingredients->total() === 1);

        $this->actingAs($admin)
            ->get(route('ingredients.index', ['status' => 'menipis']))
            ->assertOk()
            ->assertSee('Cabai Rawit')
            ->assertViewHas('ingredients', fn ($ingredients): bool => $ingredients->total() === 1);
    }

    public function test_index_stock_kpi_comes_from_real_data(): void
    {
        $admin = User::factory()->admin()->create();
        Ingredient::factory()->create(['current_stock' => '10.000', 'minimum_stock' => '2.000']);
        Ingredient::factory()->create(['current_stock' => '1.000', 'minimum_stock' => '2.000']);
        Ingredient::factory()->create(['current_stock' => '0.000', 'minimum_stock' => '1.000']);

        $this->actingAs($admin)
            ->get(route('ingredients.index'))
            ->assertOk()
            ->assertViewHas('totalIngredients', 3)
            ->assertViewHas('safeStockCount', 1)
            ->assertViewHas('lowStockCount', 1)
            ->assertViewHas('outOfStockCount', 1)
            ->assertSee('Stok Habis');
    }

    public function test_unauthenticated_request_redirects_to_login(): void
    {
        $this->get(route('ingredients.index'))->assertRedirect(route('login'));
    }

    /**
     * @return array<string, array{UserRole}>
     */
    public static function nonAdminRoles(): array
    {
        return [
            'pengelola' => [UserRole::Pengelola],
            'staf' => [UserRole::Staf],
        ];
    }
}
