<?php

namespace App\Services;

use App\Services\Contracts\RecipeCostService as RecipeCostServiceContract;

/**
 * Implementasi A2 untuk kontrak HPP yang dipakai Penjualan (A4) dan
 * Rekomendasi Produksi (A1). Versi resep terbaru menjadi sumber biaya.
 */
class RecipeCostService implements RecipeCostServiceContract
{
    public function __construct(private readonly RecipeService $recipes) {}

    public function costPerPortion(int $menuId): string
    {
        $recipe = $this->recipes->latestForMenu($menuId);

        return $recipe ? $this->recipes->costPerPortion($recipe) : '0.00';
    }

    public function ingredientsPerPortion(int $menuId): array
    {
        $recipe = $this->recipes->latestForMenu($menuId);

        if ($recipe === null) {
            return [];
        }

        $needs = [];

        foreach ($recipe->items as $item) {
            $id = (int) $item->ingredient_id;
            $needs[$id] = bcadd($needs[$id] ?? '0', (string) $item->quantity, 3);
        }

        $result = [];

        foreach ($needs as $id => $quantity) {
            $result[] = ['ingredient_id' => $id, 'quantity' => $quantity];
        }

        return $result;
    }
}
