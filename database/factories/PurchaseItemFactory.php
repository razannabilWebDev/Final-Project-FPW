<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'ingredient_id' => Ingredient::factory(),
            'quantity' => '2.000',
            'unit' => 'kg',
            'unit_cost' => '10000.00',
            'subtotal' => '20000.00',
        ];
    }
}
