<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ForecastSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $adminId = (int) DB::table('users')->where('email', 'admin@smartfood.test')->value('id');

        $this->seedForecast($now->copy()->subDay(), 'terbatas', 3, 3, false, $adminId);
        $this->seedForecast($now, 'cukup', 28, 7, true, $adminId);
    }

    private function seedForecast(
        Carbon $date,
        string $dataStatus,
        int $window,
        int $minDays,
        bool $withAdjustment,
        int $adminId,
    ): void {
        $forecastDate = $date->toDateString();

        if (DB::table('production_forecasts')
            ->where('forecast_date', $forecastDate)
            ->where('method', 'moving_average')
            ->where('data_status', $dataStatus)
            ->exists()) {
            return;
        }

        $periodStart = $date->copy()->subDays($window)->toDateString();
        $periodEnd = $date->copy()->subDay()->toDateString();

        $forecastId = DB::table('production_forecasts')->insertGetId([
            'forecast_date' => $forecastDate,
            'method' => 'moving_average',
            'data_window' => $window,
            'min_days' => $minDays,
            'data_status' => $dataStatus,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'generated_by' => $adminId,
            'generated_at' => $date,
            'adjusted_by' => $withAdjustment ? $adminId : null,
            'adjusted_at' => $withAdjustment ? $date : null,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $predictions = [
            'Nasi Ayam Geprek' => 8,
            'Ayam Bakar Madu' => 6,
            'Soto Ayam Kampung' => 5,
            'Rendang Ayam' => 4,
            'Nasi Goreng Spesial' => 7,
            'Ayam Kecap' => 6,
        ];

        foreach ($predictions as $menuName => $predicted) {
            $menuId = DB::table('menus')->where('name', $menuName)->value('id');

            if ($menuId === null) {
                continue;
            }

            $adjustmentNote = null;
            $adjustedQuantity = null;

            if ($withAdjustment && $menuName === 'Nasi Ayam Geprek') {
                $adjustedQuantity = 10;
                $adjustmentNote = 'Menjelang akhir pekan, permintaan diperkirakan naik.';
            }

            if ($withAdjustment && $menuName === 'Ayam Kecap') {
                $adjustedQuantity = 4;
                $adjustmentNote = 'Stok bahan terbatas, produksi dikurangi.';
            }

            DB::table('production_forecast_items')->insert([
                'forecast_id' => $forecastId,
                'menu_id' => $menuId,
                'predicted_quantity' => $predicted,
                'adjusted_quantity' => $adjustedQuantity,
                'adjustment_note' => $adjustmentNote,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
