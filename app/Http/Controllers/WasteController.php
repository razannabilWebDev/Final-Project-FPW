<?php

namespace App\Http\Controllers;

use App\Actions\Stock\RecordWasteAction;
use App\Http\Requests\RecordWasteRequest;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WasteController extends Controller
{
    public function __construct(private readonly RecordWasteAction $waste) {}

    public function create(): View
    {
        return view('stock.waste', [
            'ingredients' => Ingredient::query()->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function store(RecordWasteRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->waste->execute($request->validated(), $actor);

        return redirect()->route('stock.waste.create')->with('success', 'Stok susut/rusak berhasil dicatat.');
    }
}
