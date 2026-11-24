<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $compositions = [
            'Nasi Ayam Geprek' => [
                ['Beras', 150], ['Ayam', 100], ['Minyak Goreng', 20],
                ['Bawang Putih', 5], ['Cabai', 10], ['Kecap Manis', 5],
            ],
            'Ayam Bakar Madu' => [
                ['Ayam', 150], ['Kecap Manis', 15], ['Bawang Merah', 10], ['Minyak Goreng', 10],
            ],
            'Soto Ayam Kampung' => [
                ['Ayam', 100], ['Beras', 120], ['Bawang Putih', 5], ['Bawang Merah', 5],
            ],
            'Rendang Ayam' => [
                ['Ayam', 120], ['Bawang Merah', 15], ['Bawang Putih', 10], ['Cabai', 15], ['Minyak Goreng', 25],
            ],
            'Nasi Goreng Spesial' => [
                ['Beras', 150], ['Telur', 1], ['Bawang Merah', 10], ['Kecap Manis', 10], ['Minyak Goreng', 15],
            ],
            'Ayam Kecap' => [
                ['Ayam', 140], ['Kecap Manis', 20], ['Bawang Merah', 10], ['Bawang Putih', 5],
            ],
        ];

        foreach ($compositions as $menuName => $items) {
            $menuId = DB::table('menus')->where('name', $menuName)->value('id');

            if ($menuId === null) {
                continue;
            }

            $recipeId = DB::table('recipes')
                ->where('menu_id', $menuId)
                ->where('version', 1)
                ->value('id');

            if ($recipeId === null) {
                $recipeId = DB::table('recipes')->insertGetId([
                    'menu_id' => $menuId,
                    'notes' => 'Komposisi standar versi 1 sesuai takaran resep.',
                    'version' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('recipes')->where('id', $recipeId)
                    ->where('notes', 'like', '%dummy%')
                    ->update(['notes' => 'Komposisi standar versi 1 sesuai takaran resep.', 'updated_at' => $now]);
            }

            foreach ($items as [$ingredientName, $quantity]) {
                $ingredient = DB::table('ingredients')->where('name', $ingredientName)->first();

                if ($ingredient === null) {
                    continue;
                }

                if (DB::table('recipe_items')
                    ->where('recipe_id', $recipeId)
                    ->where('ingredient_id', $ingredient->id)
                    ->exists()) {
                    continue;
                }

                DB::table('recipe_items')->insert([
                    'recipe_id' => $recipeId,
                    'ingredient_id' => $ingredient->id,
                    'quantity' => $quantity,
                    'unit' => $ingredient->base_unit,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
