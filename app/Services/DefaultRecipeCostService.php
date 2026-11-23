<?php

namespace App\Services;

use App\Services\Contracts\RecipeCostService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stub sementara sampai service resmi Anggota 2 tersedia.
 * Menghitung HPP dari tabel recipes/recipe_items/ingredients bila ada.
 */
class DefaultRecipeCostService implements RecipeCostService
{
    public function costPerPortion(int $menuId): string
    {
        return $this->collectIngredients($menuId)['cost'];
    }

    public function ingredientsPerPortion(int $menuId): array
    {
        return $this->collectIngredients($menuId)['items'];
    }

    /**
     * @return array{cost: string, items: list<array{ingredient_id: int, quantity: string}>}
     */
    private function collectIngredients(int $menuId): array
    {
        if (! Schema::hasTable('recipes') || ! Schema::hasTable('recipe_items') || ! Schema::hasTable('ingredients')) {
            return ['cost' => '0.00', 'items' => []];
        }

        $rows = DB::table('recipe_items')
            ->join('recipes', 'recipe_items.recipe_id', '=', 'recipes.id')
            ->join('ingredients', 'recipe_items.ingredient_id', '=', 'ingredients.id')
            ->where('recipes.menu_id', $menuId)
            ->select(
                'recipe_items.ingredient_id',
                'recipe_items.quantity',
                'ingredients.average_cost'
            )
            ->get();

        $cost = '0.00';
        $items = [];

        foreach ($rows as $row) {
            $quantity = (string) $row->quantity;
            $items[] = ['ingredient_id' => (int) $row->ingredient_id, 'quantity' => $quantity];
            $cost = bcadd($cost, bcmul($quantity, (string) $row->average_cost, 2), 2);
        }

        return ['cost' => $cost, 'items' => $items];
    }
}
