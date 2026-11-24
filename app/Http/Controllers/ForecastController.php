<?php

namespace App\Http\Controllers;

use App\Actions\Forecast\AdjustForecastAction;
use App\Actions\Forecast\GenerateForecastAction;
use App\Http\Requests\AdjustForecastRequest;
use App\Http\Requests\GenerateForecastRequest;
use App\Models\ProductionForecast;
use App\Models\ProductionForecastItem;
use App\Models\User;
use App\Services\ForecastIngredientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ForecastController extends Controller
{
    public function index(ForecastIngredientService $ingredients): View
    {
        $forecast = ProductionForecast::query()
            ->with('items')
            ->orderByDesc('forecast_date')
            ->first();

        $menuNames = $forecast !== null
            ? DB::table('menus')
                ->whereIn('id', $forecast->items->pluck('menu_id')->filter()->all())
                ->pluck('name', 'id')
                ->all()
            : [];

        $estimate = $forecast !== null
            ? $ingredients->estimateFor($forecast)
            : ['items' => [], 'missing_recipes' => []];

        return view('forecast.index', [
            'forecast' => $forecast,
            'menuNames' => $menuNames,
            'estimate' => $estimate,
        ]);
    }

    public function generate(GenerateForecastRequest $request, GenerateForecastAction $action): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $date = $request->date('forecast_date') ?? now();

        $action->execute($date, $actor);

        return redirect()->route('forecast.index')->with('success', 'Prediksi produksi berhasil dibuat.');
    }

    public function adjust(
        AdjustForecastRequest $request,
        ProductionForecastItem $item,
        AdjustForecastAction $action,
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();
        $data = $request->validated();

        $action->execute(
            $item,
            $actor,
            (string) $data['adjusted_quantity'],
            $data['adjustment_note'] ?? null,
        );

        return redirect()->route('forecast.index')->with('success', 'Penyesuaian prediksi tersimpan.');
    }
}
