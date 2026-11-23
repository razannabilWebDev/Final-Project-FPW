<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\StockAdjustment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockAdjustment>
 */
class StockAdjustmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ingredient_id' => Ingredient::factory(),
            'system_quantity' => '20.000',
            'physical_quantity' => '18.000',
            'difference' => '-2.000',
            'reason' => 'Pemeriksaan stok fisik',
            'created_by' => null,
        ];
    }
}
