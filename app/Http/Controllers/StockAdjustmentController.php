<?php

namespace App\Http\Controllers;

use App\Actions\Stock\AdjustPhysicalStockAction;
use App\Http\Requests\AdjustStockRequest;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function __construct(private readonly AdjustPhysicalStockAction $adjust) {}

    public function create(): View
    {
        return view('stock.adjustments', [
            'ingredients' => Ingredient::query()->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function store(AdjustStockRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->adjust->execute($request->validated(), $actor);

        return redirect()->route('stock.adjustments.create')->with('success', 'Penyesuaian stok fisik berhasil dicatat.');
    }
}
