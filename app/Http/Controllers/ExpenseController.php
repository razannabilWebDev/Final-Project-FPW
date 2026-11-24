<?php

namespace App\Http\Controllers;

use App\Exceptions\CategoryInUseException;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenses) {}

    public function index(Request $request): View
    {
        $query = Expense::with('category')->latest('expense_date');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q
                ->where('description', 'like', "%{$search}%")
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%")));
        }

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->integer('category'));
        }

        [$start, $end] = $this->periodBounds($request);
        if ($start !== null && $end !== null) {
            $query->whereDate('expense_date', '>=', $start->toDateString())
                ->whereDate('expense_date', '<=', $end->toDateString());
        }

        $base = (clone $query)->get();
        $total = (float) $base->sum('amount');
        $fixed = (float) $base->filter(fn (Expense $e) => str_contains(strtolower($e->category?->name ?? ''), 'sewa')
            || str_contains(strtolower($e->category?->name ?? ''), 'gaji')
            || str_contains(strtolower($e->category?->name ?? ''), 'listrik'))->sum('amount');

        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $allCategories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.index', compact(
            'expenses', 'categories', 'allCategories', 'total', 'fixed'
        ));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->expenses->createExpense($request->validated(), $request->user());

        return back()->with('success', 'Biaya operasional berhasil dicatat.');
    }

    public function update(StoreExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->expenses->updateExpense($expense, $request->validated());

        return back()->with('success', 'Biaya operasional diperbarui.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Biaya operasional dihapus.');
    }

    public function storeCategory(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $this->expenses->createCategory($request->validated());

        return back()->with('success', 'Kategori biaya ditambahkan.');
    }

    public function updateCategory(StoreExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $this->expenses->updateCategory($expenseCategory, $request->validated());

        return back()->with('success', 'Kategori biaya diperbarui.');
    }

    public function destroyCategory(ExpenseCategory $expenseCategory): RedirectResponse
    {
        try {
            $this->expenses->deleteCategory($expenseCategory);
        } catch (CategoryInUseException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Kategori biaya dihapus.');
    }

    /**
     * Filter periode: date range (from/to) atau period tabs (harian/bulanan/tahunan).
     *
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    private function periodBounds(Request $request): array
    {
        if ($request->filled('from') && $request->filled('to')) {
            return [
                Carbon::parse($request->string('from')->toString())->startOfDay(),
                Carbon::parse($request->string('to')->toString())->endOfDay(),
            ];
        }

        return match ($request->string('period')->toString()) {
            'harian' => [now()->startOfDay(), now()->endOfDay()],
            'bulanan' => [now()->startOfMonth(), now()->endOfMonth()],
            'tahunan' => [now()->startOfYear(), now()->endOfYear()],
            default => [null, null],
        };
    }
}
