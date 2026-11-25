<?php

namespace App\Actions\Forecast;

use App\Models\ProductionForecast;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateForecastAction
{
    public function __construct(private readonly CalculateMovingAverageAction $calculator) {}

    public function execute(Carbon $forecastDate, User $actor): ProductionForecast
    {
        $method = (string) config('forecast.method');
        $windowDays = (int) config('forecast.window_days');
        $minDays = (int) config('forecast.min_days');

        $periodStart = $forecastDate->copy()->subDays($windowDays)->startOfDay();
        $periodEnd = $forecastDate->copy()->subDay()->endOfDay();

        return DB::transaction(function () use ($forecastDate, $actor, $method, $windowDays, $minDays, $periodStart, $periodEnd): ProductionForecast {
            $existing = ProductionForecast::query()
                ->whereDate('forecast_date', $forecastDate->toDateString())
                ->where('method', $method)
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            $rows = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.type', 'menu')
                ->where('sales.status', 'completed')
                ->whereNotNull('sales.completed_at')
                ->whereBetween('sales.completed_at', [$periodStart, $periodEnd])
                ->select([
                    'sale_items.menu_id',
                    'sale_items.menu_name_snapshot',
                    DB::raw('DATE(sales.completed_at) as sale_date'),
                    DB::raw('SUM(sale_items.quantity) as daily_quantity'),
                ])
                ->groupBy('sale_items.menu_id', 'sale_items.menu_name_snapshot', DB::raw('DATE(sales.completed_at)'))
                ->get();

            $perMenu = [];
            $allDates = [];

            foreach ($rows as $row) {
                if ($row->menu_id === null) {
                    continue;
                }

                $menuId = (int) $row->menu_id;
                $date = (string) $row->sale_date;
                $perMenu[$menuId][] = ['date' => $date, 'quantity' => (string) $row->daily_quantity];
                $allDates[$date] = true;
            }

            $forecast = ProductionForecast::create([
                'forecast_date' => $forecastDate->toDateString(),
                'method' => $method,
                'data_window' => $windowDays,
                'min_days' => $minDays,
                'data_status' => count($allDates) >= $minDays ? 'cukup' : 'terbatas',
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'generated_by' => $actor->id,
                'generated_at' => now(),
            ]);

            foreach ($perMenu as $menuId => $dailySales) {
                $result = $this->calculator->execute($dailySales, $windowDays, $minDays);

                if (bccomp($result['average'], '0', 3) <= 0) {
                    continue;
                }

                $forecast->items()->create([
                    'menu_id' => $menuId,
                    'predicted_quantity' => $result['average'],
                ]);
            }

            return $forecast;
        });
    }
}
