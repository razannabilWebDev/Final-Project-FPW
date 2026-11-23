<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Ringkasan data untuk dashboard sesuai role (PRD §8.3).
     *
     * @return array<string, mixed>
     */
    private function summary(): array
    {
        $table = fn (string $name): bool => Schema::hasTable($name);

        $salesCompleted = $table('sales')
            ? (float) DB::table('sales')->where('status', 'completed')->sum('total')
            : 0.0;
        $salesCount = $table('sales') ? DB::table('sales')->count() : 0;
        $salesPending = $table('sales')
            ? DB::table('sales')->whereIn('status', ['pending', 'processing'])->count()
            : 0;
        $salesProcessing = $table('sales')
            ? DB::table('sales')->where('status', 'processing')->count()
            : 0;

        $expensesTotal = $table('expenses') ? (float) DB::table('expenses')->sum('amount') : 0.0;

        $hppTotal = ($table('sale_items') && $table('sales'))
            ? (float) DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->sum('sale_items.hpp_snapshot')
            : 0.0;

        $menusCount = $table('menus') ? DB::table('menus')->where('is_active', true)->count() : 0;
        $menus = $table('menus')
            ? DB::table('menus')->where('is_active', true)->orderBy('name')->limit(6)->get()
            : collect();

        $lowStock = $table('ingredients')
            ? DB::table('ingredients')
                ->where('is_active', true)
                ->whereColumn('current_stock', '<=', 'minimum_stock')
                ->orderBy('name')
                ->limit(5)
                ->get()
            : collect();

        $purchasesPending = $table('purchases')
            ? DB::table('purchases')->where('status', 'pending')->count()
            : 0;
        $purchasesVerification = $table('purchases')
            ? DB::table('purchases')
                ->whereIn('status', ['pending', 'approved'])
                ->limit(5)
                ->get()
                ->map(function (object $purchase): object {
                    $purchase->purchase_date_label = $purchase->purchase_date
                        ? Carbon::parse($purchase->purchase_date)
                            ->timezone('Asia/Jakarta')
                            ->translatedFormat('d M Y')
                        : '-';

                    return $purchase;
                })
            : collect();

        $topMenus = ($table('sale_items') && $table('sales'))
            ? DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->select('sale_items.menu_name_snapshot as name', DB::raw('SUM(sale_items.quantity) as qty'))
                ->groupBy('sale_items.menu_name_snapshot')
                ->orderByDesc('qty')
                ->limit(5)
                ->get()
            : collect();

        $forecast = $table('production_forecasts')
            ? DB::table('production_forecasts')->orderByDesc('id')->first()
            : null;

        $forecastPortions = ($forecast !== null && $table('production_forecast_items'))
            ? (float) DB::table('production_forecast_items')
                ->where('forecast_id', $forecast->id)
                ->selectRaw('SUM(COALESCE(adjusted_quantity, predicted_quantity)) as total')
                ->value('total')
            : 0.0;

        return [
            'revenue' => $salesCompleted,
            'hpp' => $hppTotal,
            'expenses' => $expensesTotal,
            'profit' => $salesCompleted - $hppTotal - $expensesTotal,
            'salesCount' => $salesCount,
            'salesPending' => $salesPending,
            'salesProcessing' => $salesProcessing,
            'menusCount' => $menusCount,
            'menus' => $menus,
            'lowStock' => $lowStock,
            'purchasesPending' => $purchasesPending,
            'purchasesVerification' => $purchasesVerification,
            'topMenus' => $topMenus,
            'forecast' => $forecast,
            'forecast_portions' => $forecastPortions,
        ];
    }

    public function __invoke(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $view = match ($user->role->value) {
            'admin' => 'dashboard.admin',
            'pengelola' => 'dashboard.pengelola',
            default => 'dashboard.staf',
        };

        return view($view, [
            'user' => $user,
            'summary' => $this->summary(),
        ]);
    }
}
