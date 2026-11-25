<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RecipeItem> */
class RecipeItemFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $ingredient = Ingredient::factory()->create();

        return [
            'recipe_id' => Recipe::factory(),
            'ingredient_id' => $ingredient->id,
            'quantity' => fake()->randomFloat(3, 1, 200),
            'unit' => $ingredient->base_unit,
        ];
    }
}
