<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IngredientService
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array{name: string, base_unit: string, current_stock: string, minimum_stock: string, average_cost: string, is_active?: bool}  $data
     */
    public function create(array $data, ?User $actor = null): Ingredient
    {
        return DB::transaction(function () use ($data, $actor): Ingredient {
            $ingredient = Ingredient::create([
                'name' => $data['name'],
                'base_unit' => $data['base_unit'],
                'current_stock' => '0.000',
                'minimum_stock' => $data['minimum_stock'],
                'average_cost' => $data['average_cost'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (bccomp($data['current_stock'], '0', 3) > 0) {
                $this->stock->recordOpeningStock($ingredient, $data['current_stock'], $actor);
            }

            return $ingredient->refresh();
        });
    }

    /**
     * @param  array{name: string, base_unit: string, minimum_stock: string, average_cost: string, is_active?: bool}  $data
     */
    public function update(Ingredient $ingredient, array $data): Ingredient
    {
        $ingredient->fill([
            'name' => $data['name'],
            'base_unit' => $data['base_unit'],
            'minimum_stock' => $data['minimum_stock'],
            'average_cost' => $data['average_cost'],
            'is_active' => $data['is_active'] ?? $ingredient->is_active,
        ])->save();

        return $ingredient;
    }

    public function setActive(Ingredient $ingredient, bool $isActive): Ingredient
    {
        $ingredient->is_active = $isActive;
        $ingredient->save();

        return $ingredient;
    }
}
