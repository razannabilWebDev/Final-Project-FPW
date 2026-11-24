<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Recipe> */
class RecipeFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'notes' => fake()->optional()->sentence(),
            'version' => 1,
        ];
    }
}
