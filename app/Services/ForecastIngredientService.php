<?php

namespace App\Services;

use App\Models\ProductionForecast;
use App\Services\Contracts\RecipeCostService;
use Illuminate\Support\Facades\DB;

class ForecastIngredientService
{
    public function __construct(private readonly RecipeCostService $recipeCost) {}

    /**
     * Estimasi kebutuhan bahan = porsi rekomendasi (nilai penyesuaian bila ada)
     * dikali takaran resep per porsi. Resep kosong ditandai, bukan dianggap nol.
     *
     * @return array{items: list<array{name: string, unit: string, quantity: string}>, missing_recipes: list<string>}
     */
    public function estimateFor(ProductionForecast $forecast): array
    {
        $forecast->loadMissing('items');

        $menuNames = DB::table('menus')->pluck('name', 'id');
        $ingredients = DB::table('ingredients')->select('id', 'name', 'base_unit')->get()->keyBy('id');

        $totals = [];
        $missingRecipes = [];

        foreach ($forecast->items as $item) {
            $portions = $item->adjusted_quantity ?? $item->predicted_quantity;

            if ($portions === null) {
                continue;
            }

            $recipeItems = $this->recipeCost->ingredientsPerPortion((int) $item->menu_id);

            if ($recipeItems === []) {
                $missingRecipes[] = $menuNames[$item->menu_id] ?? ('Menu #'.$item->menu_id);

                continue;
            }

            foreach ($recipeItems as $recipeItem) {
                $ingredientId = (int) $recipeItem['ingredient_id'];
                $quantity = bcmul((string) $portions, (string) $recipeItem['quantity'], 3);
                $totals[$ingredientId] = bcadd($totals[$ingredientId] ?? '0', $quantity, 3);
            }
        }

        $items = [];

        foreach ($totals as $ingredientId => $quantity) {
            $ingredient = $ingredients->get($ingredientId);
            $items[] = [
                'name' => $ingredient?->name ?? 'Bahan #'.$ingredientId,
                'unit' => $ingredient?->base_unit ?? '-',
                'quantity' => $quantity,
            ];
        }

        usort($items, fn (array $left, array $right): int => strcmp($left['name'], $right['name']));

        return ['items' => $items, 'missing_recipes' => array_values(array_unique($missingRecipes))];
    }
}
