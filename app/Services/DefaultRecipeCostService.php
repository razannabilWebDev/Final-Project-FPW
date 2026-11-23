<?php

namespace App\Services;

use App\Services\Contracts\RecipeCostService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Implementasi final perhitungan HPP per porsi milik Anggota 4.
 * Menghitung dari versi resep terbaru × ingredients.average_cost (D4, D7).
 * Perlu koordinasi dengan Anggota 2 bila format resep berubah.
 */
class DefaultRecipeCostService implements RecipeCostService
{
    public function costPerPortion(int $menuId): string
    {
        return $this->collect($menuId)['cost'];
    }

    public function ingredientsPerPortion(int $menuId): array
    {
        return $this->collect($menuId)['items'];
    }

    /**
     * @return array{cost: string, items: list<array{ingredient_id: int, quantity: string}>}
     */
    private function collect(int $menuId): array
    {
        if (! Schema::hasTable('recipes') || ! Schema::hasTable('recipe_items') || ! Schema::hasTable('ingredients')) {
            return ['cost' => '0.00', 'items' => []];
        }

        $latestVersion = DB::table('recipes')
            ->where('menu_id', $menuId)
            ->max('version');

        if ($latestVersion === null) {
            return ['cost' => '0.00', 'items' => []];
        }

        $rows = DB::table('recipe_items')
            ->join('recipes', 'recipe_items.recipe_id', '=', 'recipes.id')
            ->join('ingredients', 'recipe_items.ingredient_id', '=', 'ingredients.id')
            ->where('recipes.menu_id', $menuId)
            ->where('recipes.version', $latestVersion)
            ->select(
                'recipe_items.ingredient_id',
                'recipe_items.quantity',
                'ingredients.average_cost'
            )
            ->get();

        $needs = [];
        $costs = [];

        foreach ($rows as $row) {
            $id = (int) $row->ingredient_id;
            $quantity = (string) $row->quantity;
            $needs[$id] = bcadd($needs[$id] ?? '0', $quantity, 3);
            $costs[$id] = (string) $row->average_cost;
        }

        $items = [];
        $cost = '0.00';

        foreach ($needs as $id => $quantity) {
            $items[] = ['ingredient_id' => $id, 'quantity' => $quantity];
            $cost = bcadd($cost, bcmul($quantity, $costs[$id] ?? '0', 2), 2);
        }

        return ['cost' => $cost, 'items' => $items];
    }
}
