<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Support\Facades\DB;

class RecipeService
{
    /**
     * @param  array{notes?: string|null}  $data
     * @param  list<array{ingredient_id: int, quantity: string, unit: string}>  $items
     */
    public function create(Menu $menu, array $data, array $items): Recipe
    {
        return DB::transaction(function () use ($menu, $data, $items): Recipe {
            $version = (int) Recipe::where('menu_id', $menu->id)->max('version') + 1;

            $recipe = Recipe::create([
                'menu_id' => $menu->id,
                'notes' => $data['notes'] ?? null,
                'version' => $version,
            ]);

            $this->syncItems($recipe, $items);

            return $recipe;
        });
    }

    /**
     * Perubahan takaran/komposisi menghasilkan versi baru. Perubahan catatan
     * saja memperbarui versi saat ini agar histori biaya tetap utuh (D7).
     *
     * @param  array{notes?: string|null}  $data
     * @param  list<array{ingredient_id: int, quantity: string, unit: string}>  $items
     */
    public function update(Recipe $recipe, array $data, array $items): Recipe
    {
        return DB::transaction(function () use ($recipe, $data, $items): Recipe {
            if (! $this->compositionChanged($recipe, $items)) {
                $recipe->update($data);

                return $recipe;
            }

            $next = Recipe::create([
                'menu_id' => $recipe->menu_id,
                'notes' => $data['notes'] ?? $recipe->notes,
                'version' => $recipe->version + 1,
            ]);

            $this->syncItems($next, $items);

            return $next;
        });
    }

    public function latestForMenu(int $menuId): ?Recipe
    {
        return Recipe::with('items.ingredient')
            ->where('menu_id', $menuId)
            ->where('is_active', true)
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Nonaktifkan seluruh versi resep sebuah menu. Versi lama tetap tersimpan
     * untuk audit, tetapi tidak dipakai lagi sebagai HPP transaksi baru.
     */
    public function deactivateMenuRecipes(int $menuId): int
    {
        return DB::transaction(fn (): int => Recipe::where('menu_id', $menuId)
            ->where('is_active', true)
            ->update(['is_active' => false]));
    }

    /**
     * HPP per porsi sebagai decimal string, dihitung dari versi resep terbaru.
     */
    public function costPerPortion(Recipe $recipe): string
    {
        $cost = '0.00';

        foreach ($recipe->items as $item) {
            $averageCost = (string) ($item->ingredient?->average_cost ?? '0');
            $cost = bcadd($cost, bcmul((string) $item->quantity, $averageCost, 2), 2);
        }

        return $cost;
    }

    /**
     * @return list<array{ingredient_id: int, name: string, quantity: string, unit: string, average_cost: string, subtotal: string}>
     */
    public function detailedItems(Recipe $recipe): array
    {
        $rows = [];

        foreach ($recipe->items as $item) {
            $averageCost = (string) ($item->ingredient?->average_cost ?? '0');
            $rows[] = [
                'ingredient_id' => (int) $item->ingredient_id,
                'name' => $item->ingredient?->name ?? '-',
                'quantity' => (string) $item->quantity,
                'unit' => (string) $item->unit,
                'average_cost' => $averageCost,
                'subtotal' => bcmul((string) $item->quantity, $averageCost, 2),
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array{ingredient_id: int, quantity: string, unit: string}>  $items
     */
    private function syncItems(Recipe $recipe, array $items): void
    {
        foreach ($items as $item) {
            $ingredient = Ingredient::findOrFail($item['ingredient_id']);

            RecipeItem::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $ingredient->id,
                // D5: resep disimpan dalam satuan dasar bahan.
                'quantity' => $item['quantity'],
                'unit' => $ingredient->base_unit,
            ]);
        }
    }

    /**
     * @param  list<array{ingredient_id: int, quantity: string, unit: string}>  $items
     */
    private function compositionChanged(Recipe $recipe, array $items): bool
    {
        $previous = $recipe->items
            ->mapWithKeys(fn ($item): array => [(int) $item->ingredient_id => number_format((float) $item->quantity, 3, '.', '')])
            ->all();

        $incoming = collect($items)
            ->mapWithKeys(fn (array $item): array => [(int) $item['ingredient_id'] => number_format((float) $item['quantity'], 3, '.', '')])
            ->all();

        ksort($previous);
        ksort($incoming);

        return $previous !== $incoming;
    }
}
