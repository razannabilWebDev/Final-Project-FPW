<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            'Pembelian Bahan Baku',
            'Listrik/Air',
            'Gas',
            'Gaji',
            'Sewa',
            'Transportasi',
            'Perawatan',
            'Lainnya',
        ];

        foreach ($categories as $category) {
            if (DB::table('expense_categories')->where('name', $category)->exists()) {
                continue;
            }

            DB::table('expense_categories')->insert([
                'name' => $category,
                'description' => 'Kategori biaya operasional '.$category,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $menus = [
            ['name' => 'Nasi Ayam Geprek', 'description' => 'Nasi dengan ayam geprek dan sambal', 'selling_price' => 22000],
            ['name' => 'Ayam Bakar Madu', 'description' => 'Ayam bakar dengan bumbu madu', 'selling_price' => 28000],
            ['name' => 'Soto Ayam Kampung', 'description' => 'Soto ayam kuah bening', 'selling_price' => 20000],
            ['name' => 'Rendang Ayam', 'description' => 'Rendang ayam bumbu rendang', 'selling_price' => 32000],
            ['name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan telur', 'selling_price' => 25000],
            ['name' => 'Ayam Kecap', 'description' => 'Ayam ungkep kecap manis', 'selling_price' => 26000],
        ];

        foreach ($menus as $menu) {
            if (DB::table('menus')->where('name', $menu['name'])->exists()) {
                continue;
            }

            DB::table('menus')->insert([
                'name' => $menu['name'],
                'description' => $menu['description'],
                'selling_price' => $menu['selling_price'],
                'image_path' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $ingredients = [
            ['name' => 'Ayam', 'base_unit' => 'gram', 'current_stock' => 5000, 'minimum_stock' => 1000, 'average_cost' => 0.04],
            ['name' => 'Beras', 'base_unit' => 'gram', 'current_stock' => 10000, 'minimum_stock' => 2000, 'average_cost' => 0.01],
            ['name' => 'Minyak Goreng', 'base_unit' => 'ml', 'current_stock' => 4000, 'minimum_stock' => 1000, 'average_cost' => 0.02],
            ['name' => 'Bawang Merah', 'base_unit' => 'gram', 'current_stock' => 2000, 'minimum_stock' => 500, 'average_cost' => 0.02],
            ['name' => 'Bawang Putih', 'base_unit' => 'gram', 'current_stock' => 1500, 'minimum_stock' => 400, 'average_cost' => 0.03],
            ['name' => 'Cabai', 'base_unit' => 'gram', 'current_stock' => 1200, 'minimum_stock' => 300, 'average_cost' => 0.05],
            ['name' => 'Kecap Manis', 'base_unit' => 'ml', 'current_stock' => 2000, 'minimum_stock' => 500, 'average_cost' => 0.02],
            ['name' => 'Telur', 'base_unit' => 'butir', 'current_stock' => 200, 'minimum_stock' => 50, 'average_cost' => 2500],
        ];

        foreach ($ingredients as $ingredient) {
            if (DB::table('ingredients')->where('name', $ingredient['name'])->exists()) {
                continue;
            }

            DB::table('ingredients')->insert([
                'name' => $ingredient['name'],
                'base_unit' => $ingredient['base_unit'],
                'current_stock' => $ingredient['current_stock'],
                'minimum_stock' => $ingredient['minimum_stock'],
                'average_cost' => $ingredient['average_cost'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $settings = [
            'business_name' => 'SmartFood Home',
            'app_timezone' => 'Asia/Jakarta',
            'forecast_window_days' => '28',
            'forecast_min_days' => '7',
        ];

        foreach ($settings as $key => $value) {
            if (DB::table('settings')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
