<?php

namespace App\Services;

use App\Exceptions\CategoryInUseException;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * @param  array{name: string, description?: ?string}  $data
     */
    public function createCategory(array $data): ExpenseCategory
    {
        return DB::transaction(fn (): ExpenseCategory => ExpenseCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]));
    }

    /**
     * @param  array{name: string, description?: ?string, is_active?: bool}  $data
     */
    public function updateCategory(ExpenseCategory $category, array $data): ExpenseCategory
    {
        $category->update(array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? null,
        ], fn ($value) => $value !== null));

        return $category->refresh();
    }

    /**
     * Kategori yang sudah dipakai transaksi tidak boleh dihapus (PRD §6.8).
     * Gunakan deaktivasi sebagai gantinya.
     */
    public function deleteCategory(ExpenseCategory $category): void
    {
        if ($category->expenses()->exists()) {
            throw new CategoryInUseException('Kategori sudah digunakan transaksi dan tidak dapat dihapus. Nonaktifkan saja kategori ini.');
        }

        DB::transaction(fn () => $category->delete());
    }

    /**
     * @param  array{expense_category_id: int, expense_date: string, amount: numeric-string|float|int, description?: ?string}  $data
     */
    public function createExpense(array $data, User $user): Expense
    {
        return DB::transaction(fn (): Expense => Expense::create([
            'expense_category_id' => (int) $data['expense_category_id'],
            'expense_date' => $data['expense_date'],
            'amount' => number_format((float) $data['amount'], 2, '.', ''),
            'description' => $data['description'] ?? null,
            'created_by' => $user->id,
        ]));
    }

    /**
     * @param  array{expense_category_id: int, expense_date: string, amount: numeric-string|float|int, description?: ?string}  $data
     */
    public function updateExpense(Expense $expense, array $data): Expense
    {
        $expense->update([
            'expense_category_id' => (int) $data['expense_category_id'],
            'expense_date' => $data['expense_date'],
            'amount' => number_format((float) $data['amount'], 2, '.', ''),
            'description' => $data['description'] ?? null,
        ]);

        return $expense->refresh();
    }
}
