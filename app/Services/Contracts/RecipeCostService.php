<?php

namespace App\Services\Contracts;

interface RecipeCostService
{
    /**
     * HPP per porsi untuk sebuah menu (decimal string).
     * Implementasi sementara menghitung dari recipe_items × ingredients.average_cost.
     */
    public function costPerPortion(int $menuId): string;

    /**
     * Kebutuhan bahan per porsi: [['ingredient_id' => int, 'quantity' => string], ...].
     *
     * @return list<array{ingredient_id: int, quantity: string}>
     */
    public function ingredientsPerPortion(int $menuId): array;
}
