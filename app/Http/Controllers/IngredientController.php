<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Models\Ingredient;
use App\Models\User;
use App\Services\IngredientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $ingredients) {}

    public function index(): View
    {
        return view('ingredients.index', [
            'ingredients' => Ingredient::query()
                ->orderBy('name')
                ->orderBy('id')
                ->paginate(10),
            'lowStockCount' => Ingredient::query()->lowStock()->count(),
        ]);
    }

    public function create(): View
    {
        return view('ingredients.create', ['units' => $this->units()]);
    }

    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->ingredients->create($request->validated(), $actor);

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(Ingredient $ingredient): View
    {
        return view('ingredients.edit', [
            'ingredient' => $ingredient,
            'units' => $this->units(),
        ]);
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->ingredients->update($ingredient, $request->validated());

        return redirect()->route('ingredients.index')->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    public function setActive(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $isActive = ! $request->boolean('is_active');
        $this->ingredients->setActive($ingredient, $isActive);

        $message = $isActive
            ? 'Bahan baku berhasil diaktifkan.'
            : 'Bahan baku berhasil dinonaktifkan.';

        return redirect()->route('ingredients.index')->with('success', $message);
    }

    /**
     * @return Collection<int, string>
     */
    private function units(): Collection
    {
        return Ingredient::query()
            ->distinct()
            ->orderBy('base_unit')
            ->pluck('base_unit');
    }
}
