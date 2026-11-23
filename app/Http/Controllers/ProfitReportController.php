<?php

namespace App\Http\Controllers;

use App\Services\ProfitReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfitReportController extends Controller
{
    public function __construct(private readonly ProfitReportService $reports) {}

    public function index(Request $request): View
    {
        [$start, $end, $label] = $this->period($request);

        $summary = $this->reports->summarize($start, $end);
        $trend = $this->reports->dailyTrend($start, $end);
        $topMenus = $this->reports->topMenus($start, $end);
        $details = $this->reports->details($start, $end);

        return view('reports.profit', array_merge($summary, [
            'trend' => $trend,
            'topMenus' => $topMenus,
            'saleDetails' => $details['sales'],
            'expenseDetails' => $details['expenses'],
            'label' => $label,
            'period' => $request->string('period')->toString(),
        ]));
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null, 2: string}
     */
    private function period(Request $request): array
    {
        if ($request->filled('from') && $request->filled('to')) {
            $start = Carbon::parse($request->string('from')->toString())->startOfDay();
            $end = Carbon::parse($request->string('to')->toString())->endOfDay();

            return [$start, $end, $start->translatedFormat('d M Y').' – '.$end->translatedFormat('d M Y')];
        }

        return match ($request->string('period')->toString()) {
            'harian' => [now()->startOfDay(), now()->endOfDay(), now()->translatedFormat('d F Y')],
            'bulanan' => [now()->startOfMonth(), now()->endOfMonth(), now()->translatedFormat('F Y')],
            'tahunan' => [now()->startOfYear(), now()->endOfYear(), now()->translatedFormat('Y')],
            default => [null, null, 'Seluruh periode'],
        };
    }
}
