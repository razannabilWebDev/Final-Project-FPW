<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'base_unit' => fake()->randomElement(['gram', 'kg', 'ml', 'liter', 'butir']),
            'current_stock' => (string) fake()->randomFloat(3, 0, 1000),
            'minimum_stock' => (string) fake()->randomFloat(3, 0, 50),
            'average_cost' => (string) fake()->randomFloat(2, 0, 100),
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'current_stock' => '5.000',
            'minimum_stock' => '10.000',
        ]);
    }
}
