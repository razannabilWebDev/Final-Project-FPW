<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ingredient_id' => Ingredient::factory(),
            'movement_type' => 'adjustment',
            'quantity_in' => '10.000',
            'quantity_out' => '0.000',
            'balance_before' => '0.000',
            'balance_after' => '10.000',
            'reference_type' => null,
            'reference_id' => null,
            'note' => 'Pergerakan stok dummy',
            'created_by' => null,
            'created_at' => now(),
        ];
    }
}
