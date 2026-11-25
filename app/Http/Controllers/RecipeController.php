<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function __construct(private readonly RecipeService $recipes) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $activeRecipeMenuIds = Recipe::query()
            ->where('is_active', true)
            ->select('menu_id')
            ->distinct();

        $menus = Menu::query()
            ->whereIn('id', $activeRecipeMenuIds)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $latestRecipes = Recipe::query()
            ->whereIn('menu_id', $menus->pluck('id'))
            ->where('is_active', true)
            ->with('items.ingredient')
            ->get()
            ->sortByDesc('version')
            ->groupBy('menu_id')
            ->map(fn ($group) => $group->first());

        $allCosts = [];
        foreach ($latestRecipes as $menuId => $recipe) {
            $allCosts[$menuId] = (float) $this->recipes->costPerPortion($recipe);
        }

        $stats = [
            'total' => (int) Recipe::query()->where('is_active', true)->select('menu_id')->distinct()->count('menu_id'),
            'average_cost' => $allCosts === [] ? 0.0 : array_sum($allCosts) / count($allCosts),
            'margin' => 0.0,
        ];

        $margins = [];
        foreach ($menus as $menu) {
            $price = (float) $menu->selling_price;
            if ($price <= 0 || ! isset($allCosts[$menu->id])) {
                continue;
            }
            $margins[] = ($price - $allCosts[$menu->id]) / $price * 100;
        }
        $stats['margin'] = $margins === [] ? 0.0 : array_sum($margins) / count($margins);

        $formMenus = Menu::query()->where('is_active', true)->orderBy('name')->get();
        $formIngredients = Ingredient::query()->where('is_active', true)->orderBy('name')->get();

        return view('recipes.index', compact(
            'menus',
            'latestRecipes',
            'stats',
            'search',
            'allCosts',
            'formMenus',
            'formIngredients',
        ) + ['units' => $this->availableUnits()]);
    }

    public function create(Request $request): View
    {
        $menus = Menu::query()->where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::query()->where('is_active', true)->orderBy('name')->get();
        $data = compact('menus', 'ingredients') + ['units' => $this->availableUnits()];

        if ($request->boolean('partial')) {
            return view('recipes.partials.form', $data);
        }

        return view('recipes.create', $data);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $menu = Menu::findOrFail($request->integer('menu_id'));

        $this->recipes->create(
            $menu,
            $request->safe()->only(['notes']),
            $request->validated()['items'],
        );

        return redirect()->route('recipes.index')->with('success', 'Resep berhasil ditambahkan.');
    }

    public function show(Request $request, Recipe $recipe): View
    {
        $recipe->load('menu', 'items.ingredient');
        $detailedItems = $this->recipes->detailedItems($recipe);
        $cost = $this->recipes->costPerPortion($recipe);

        if ($request->boolean('partial')) {
            return view('recipes.partials.detail', compact('recipe', 'detailedItems', 'cost'));
        }

        return view('recipes.show', compact('recipe', 'detailedItems', 'cost'));
    }

    public function edit(Request $request, Recipe $recipe): View
    {
        $recipe->load('items.ingredient');
        $menus = Menu::query()->where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::query()->where('is_active', true)->orderBy('name')->get();
        $data = compact('recipe', 'menus', 'ingredients') + [
            'isEdit' => true,
            'units' => $this->availableUnits(),
        ];

        if ($request->boolean('partial')) {
            return view('recipes.partials.form', $data);
        }

        return view('recipes.edit', $data);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $this->recipes->update(
            $recipe,
            $request->safe()->only(['notes']),
            $request->validated()['items'],
        );

        return redirect()->route('recipes.index')->with('success', 'Resep berhasil diperbarui.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $this->recipes->deactivateMenuRecipes($recipe->menu_id);

        return redirect()->route('recipes.index')->with('success', 'Resep berhasil dihapus dan dinonaktifkan.');
    }

    /**
     * @return list<string>
     */
    private function availableUnits(): array
    {
        return Ingredient::query()
            ->where('is_active', true)
            ->orderBy('base_unit')
            ->distinct()
            ->pluck('base_unit')
            ->all();
    }
}
