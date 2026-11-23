<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfitReportService
{
    /**
     * Ringkasan keuntungan untuk satu periode.
     *
     * @return array<string, mixed>
     */
    public function summarize(?Carbon $start, ?Carbon $end): array
    {
        $salesQuery = Sale::query()->where('status', 'completed');
        $expensesQuery = Expense::query();

        if ($start !== null && $end !== null) {
            $salesQuery->whereBetween('completed_at', [$start, $end]);
            $expensesQuery->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()]);
        }

        $sales = $salesQuery->with('items')->get();

        $menuSales = $sales->where('type', 'menu');
        $manualSales = $sales->where('type', 'manual');

        $menuRevenue = (float) $menuSales->sum('total');
        $manualRevenue = (float) $manualSales->sum('total');
        $revenue = $menuRevenue + $manualRevenue;

        $hpp = 0.0;
        foreach ($menuSales as $sale) {
            foreach ($sale->items as $item) {
                $hpp += (float) $item->hpp_snapshot * (float) $item->quantity;
            }
        }

        $expenses = (float) (clone $expensesQuery)->sum('amount');
        $grossProfit = $revenue - $hpp;
        $netProfit = $grossProfit - $expenses;

        return [
            'start' => $start,
            'end' => $end,
            'revenue' => $revenue,
            'menuRevenue' => $menuRevenue,
            'manualRevenue' => $manualRevenue,
            'hpp' => $hpp,
            'hppAvailable' => $menuSales->isNotEmpty(),
            'expenses' => $expenses,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'transactionCount' => $sales->count(),
            'menuCount' => $menuSales->count(),
            'manualCount' => $manualSales->count(),
        ];
    }

    /**
     * Ringkasan per hari untuk grafik tren.
     *
     * @return Collection<int, object>
     */
    public function dailyTrend(?Carbon $start, ?Carbon $end): Collection
    {
        if ($start === null || $end === null) {
            return collect();
        }

        return DB::table('sales')
            ->select(
                DB::raw('DATE(completed_at) as day'),
                DB::raw("SUM(CASE WHEN type = 'menu' THEN total ELSE 0 END) as menu_revenue"),
                DB::raw("SUM(CASE WHEN type = 'manual' THEN total ELSE 0 END) as manual_revenue"),
                DB::raw('COUNT(*) as transactions'),
            )
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->groupByRaw('DATE(completed_at)')
            ->orderBy('day')
            ->get();
    }

    /**
     * Menu terlaris pada periode (hanya penjualan berbasis menu).
     *
     * @return Collection<int, object>
     */
    public function topMenus(?Carbon $start, ?Carbon $end, int $limit = 5): Collection
    {
        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.type', 'menu')
            ->select(
                'sale_items.menu_name_snapshot as name',
                DB::raw('SUM(sale_items.quantity) as qty'),
                DB::raw('SUM(sale_items.subtotal) as revenue'),
            )
            ->groupBy('sale_items.menu_name_snapshot')
            ->orderByDesc('qty')
            ->limit($limit);

        if ($start !== null && $end !== null) {
            $query->whereBetween('sales.completed_at', [$start, $end]);
        }

        return $query->get();
    }

    /**
     * Rincian transaksi & biaya untuk tabel laporan.
     *
     * @return array{sales: LengthAwarePaginator, expenses: LengthAwarePaginator}
     */
    public function details(?Carbon $start, ?Carbon $end): array
    {
        $salesQuery = Sale::query()->where('status', 'completed')->with('items')->latest('completed_at');
        $expensesQuery = Expense::query()->with('category')->latest('expense_date');

        if ($start !== null && $end !== null) {
            $salesQuery->whereBetween('completed_at', [$start, $end]);
            $expensesQuery->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()]);
        }

        return [
            'sales' => $salesQuery->paginate(10, ['*'], 'sales_page')->withQueryString(),
            'expenses' => $expensesQuery->paginate(10, ['*'], 'expenses_page')->withQueryString(),
        ];
    }
}
