<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\User;
use App\Services\Contracts\RecipeCostService;
use App\Services\RecipeService;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecipeManagementTest extends TestCase
{
    use RefreshDatabase;

    private function recipeWithIngredient(array $ingredient = [], string $quantity = '2.000'): Recipe
    {
        $ingredient = Ingredient::factory()->create(array_merge([
            'name' => 'Ayam',
            'base_unit' => 'gram',
            'average_cost' => '10000.00',
        ], $ingredient));

        $recipe = Recipe::factory()->create();
        RecipeItem::create([
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => $quantity,
            'unit' => $ingredient->base_unit,
        ]);

        return $recipe;
    }

    public function test_admin_can_view_recipe_index(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient();
        $recipe->menu->update(['name' => 'Nasi Ayam Geprek']);

        $this->actingAs($admin)
            ->get(route('recipes.index'))
            ->assertOk()
            ->assertSee('Pengelolaan Resep')
            ->assertSee('Total Resep')
            ->assertSee('Rata-rata Modal')
            ->assertSee('Potensi Margin')
            ->assertSee('Nasi Ayam Geprek');
    }

    public function test_hpp_is_calculated_from_quantity_and_average_cost(): void
    {
        $recipe = $this->recipeWithIngredient(quantity: '2.500');

        $this->assertSame('25000.00', app(RecipeService::class)->costPerPortion($recipe));
        $this->assertSame('25000.00', app(RecipeCostService::class)->costPerPortion($recipe->menu_id));
        $this->assertSame([
            ['ingredient_id' => $recipe->items->first()->ingredient_id, 'quantity' => '2.500'],
        ], app(RecipeCostService::class)->ingredientsPerPortion($recipe->menu_id));
    }

    public function test_changing_composition_creates_new_version_and_keeps_the_old_one(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient(quantity: '2.000');
        $item = $recipe->items->first();

        $this->actingAs($admin)
            ->put(route('recipes.update', $recipe), [
                'notes' => 'Takaran diperbarui.',
                'items' => [[
                    'ingredient_id' => $item->ingredient_id,
                    'quantity' => '3.000',
                    'unit' => 'gram',
                ]],
            ])
            ->assertRedirect(route('recipes.index'));

        $versions = Recipe::where('menu_id', $recipe->menu_id)->orderBy('version')->get();

        $this->assertCount(2, $versions);
        $this->assertSame(1, $versions[0]->version);
        $this->assertSame('2.000', $versions[0]->items->first()->quantity);
        $this->assertSame(2, $versions[1]->version);
        $this->assertSame('3.000', $versions[1]->items->first()->quantity);
    }

    public function test_editing_notes_only_does_not_create_a_new_version(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient();
        $item = $recipe->items->first();

        $this->actingAs($admin)
            ->put(route('recipes.update', $recipe), [
                'notes' => 'Catatan baru.',
                'items' => [[
                    'ingredient_id' => $item->ingredient_id,
                    'quantity' => '2.000',
                    'unit' => 'gram',
                ]],
            ])
            ->assertRedirect(route('recipes.index'));

        $this->assertSame(1, Recipe::where('menu_id', $recipe->menu_id)->count());
        $this->assertSame('Catatan baru.', $recipe->fresh()->notes);
    }

    public function test_recipe_update_does_not_rewrite_sale_snapshot(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient(quantity: '2.000');
        $menu = $recipe->menu;

        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => 'TRX-RESEPEST-0001',
            'type' => 'menu',
            'status' => 'completed',
            'payment_method' => 'cash',
            'total' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'menu_id' => $menu->id,
            'menu_name_snapshot' => $menu->name,
            'quantity' => 1,
            'unit_price_snapshot' => 50000,
            'hpp_snapshot' => 20000,
            'subtotal' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('recipes.update', $recipe), [
                'items' => [[
                    'ingredient_id' => $recipe->items->first()->ingredient_id,
                    'quantity' => '9.000',
                    'unit' => 'gram',
                ]],
            ])
            ->assertRedirect(route('recipes.index'));

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $saleId,
            'hpp_snapshot' => 20000,
        ]);
    }

    public function test_pengelola_can_view_recipes_but_cannot_change_takaran(): void
    {
        $pengelola = User::factory()->state(['role' => UserRole::Pengelola])->create();
        $recipe = $this->recipeWithIngredient();

        $this->actingAs($pengelola)
            ->get(route('recipes.index'))
            ->assertOk()
            ->assertSee('Daftar Resep Menu');

        $this->actingAs($pengelola)->get(route('recipes.show', $recipe))->assertOk();
        $this->actingAs($pengelola)->get(route('recipes.create'))->assertForbidden();
        $this->actingAs($pengelola)->post(route('recipes.store'), [])->assertForbidden();
        $this->actingAs($pengelola)->get(route('recipes.edit', $recipe))->assertForbidden();
        $this->actingAs($pengelola)->put(route('recipes.update', $recipe), [])->assertForbidden();
        $this->actingAs($pengelola)->delete(route('recipes.destroy', $recipe))->assertForbidden();
    }

    public function test_staff_cannot_view_recipes(): void
    {
        $staf = User::factory()->state(['role' => UserRole::Staf])->create();

        $this->actingAs($staf)->get(route('recipes.index'))->assertForbidden();
    }

    public function test_validation_rejects_duplicate_ingredients_and_wrong_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'base_unit' => 'gram',
            'average_cost' => '5000.00',
        ]);
        $payload = [
            'menu_id' => $menu->id,
            'items' => [
                ['ingredient_id' => $ingredient->id, 'quantity' => '1.000', 'unit' => 'gram'],
                ['ingredient_id' => $ingredient->id, 'quantity' => '2.000', 'unit' => 'gram'],
            ],
        ];

        $this->actingAs($admin)
            ->post(route('recipes.store'), $payload)
            ->assertSessionHasErrors('items.1.ingredient_id');

        $payload['items'][0]['unit'] = 'kg';

        $this->actingAs($admin)
            ->post(route('recipes.store'), $payload)
            ->assertSessionHasErrors('items.0.unit');

        $this->assertDatabaseCount('recipes', 0);
    }

    public function test_modal_partials_are_rendered_without_full_layout(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient();

        $this->actingAs($admin)
            ->get(route('recipes.create', ['partial' => 1]))
            ->assertOk()
            ->assertViewIs('recipes.partials.form')
            ->assertSee('id="create-recipe-form-element"', false);

        $this->actingAs($admin)
            ->get(route('recipes.edit', $recipe).'?partial=1')
            ->assertOk()
            ->assertViewIs('recipes.partials.form')
            ->assertSee('id="edit-recipe-form-element"', false);

        $this->actingAs($admin)
            ->get(route('recipes.show', $recipe).'?partial=1')
            ->assertOk()
            ->assertViewIs('recipes.partials.detail')
            ->assertSee('Komposisi Bahan');
    }

    public function test_deleting_recipe_deactivates_all_versions_and_hides_it(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient();
        $this->actingAs($admin)
            ->put(route('recipes.update', $recipe), [
                'items' => [[
                    'ingredient_id' => $recipe->items->first()->ingredient_id,
                    'quantity' => '3.000',
                    'unit' => 'gram',
                ]],
            ])
            ->assertRedirect(route('recipes.index'));

        $this->assertSame(2, Recipe::where('menu_id', $recipe->menu_id)->count());

        $this->actingAs($admin)
            ->delete(route('recipes.destroy', $recipe))
            ->assertRedirect(route('recipes.index'))
            ->assertSessionHas('success');

        $this->assertSame(0, Recipe::where('menu_id', $recipe->menu_id)->where('is_active', true)->count());
        $this->assertSame(2, Recipe::where('menu_id', $recipe->menu_id)->where('is_active', false)->count());
        $this->assertSame('0.00', app(RecipeCostService::class)->costPerPortion($recipe->menu_id));

        $this->actingAs($admin)
            ->get(route('recipes.index'))
            ->assertOk()
            ->assertViewHas('menus', fn ($paginator): bool => ! $paginator->contains('id', $recipe->menu_id));
    }

    public function test_edit_form_has_add_button_and_unit_dropdown(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient();

        $html = $this->actingAs($admin)
            ->get(route('recipes.edit', $recipe).'?partial=1')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('data-add-item', $html);
        $this->assertStringContainsString('data-item-template', $html);
        $this->assertStringContainsString('name="items[0][unit]"', $html);
        $this->assertStringContainsString('<option value="gram"', $html);
        $this->assertMatchesRegularExpression('/data-recipe-items[\s\S]*data-add-item[\s\S]*data-item-template/', $html);
    }

    public function test_quantity_is_displayed_without_trailing_zeros(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = $this->recipeWithIngredient(quantity: '2.000');

        $editHtml = $this->actingAs($admin)
            ->get(route('recipes.edit', $recipe).'?partial=1')
            ->assertOk()
            ->getContent();
        $detailHtml = $this->actingAs($admin)
            ->get(route('recipes.show', $recipe).'?partial=1')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('value="2.000"', $editHtml);
        $this->assertStringContainsString('value="2"', $editHtml);
        $this->assertStringNotContainsString('2.000 gram', $detailHtml);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('recipes.index'))->assertRedirect(route('login'));
    }
}
