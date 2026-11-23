<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $ingredientId = $request->integer('ingredient_id');

        $movements = StockMovement::query()
            ->with(['ingredient:id,name', 'creator:id,name'])
            ->when($ingredientId > 0, fn ($query) => $query->where('ingredient_id', $ingredientId))
            ->latest('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('stock.movements', [
            'movements' => $movements,
            'ingredients' => Ingredient::query()->orderBy('name')->orderBy('id')->get(['id', 'name']),
            'selectedIngredient' => $ingredientId,
        ]);
    }
}
