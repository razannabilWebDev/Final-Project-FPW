<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'))->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store')
    ->middleware('throttle:5,1');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/ganti-password', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::post('/ganti-password', [ChangePasswordController::class, 'update'])->name('password.update');

    Route::middleware('can:manage-users')->prefix('users')->name('users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/status', [UserController::class, 'setActive'])->name('status');
    });
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])
        ->name('sales.create')->middleware('can:create-sales');
    Route::post('/sales', [SaleController::class, 'store'])
        ->name('sales.store')->middleware('can:create-sales');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
        ->name('sales.update-status')->middleware('can:update-sale-status');
});

Route::middleware(['auth', 'active', 'can:manage-expenses'])->group(function (): void {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::post('/expense-categories', [ExpenseController::class, 'storeCategory'])->name('expense-categories.store');
    Route::put('/expense-categories/{expense_category}', [ExpenseController::class, 'updateCategory'])->name('expense-categories.update');
    Route::delete('/expense-categories/{expense_category}', [ExpenseController::class, 'destroyCategory'])->name('expense-categories.destroy');
});

Route::middleware(['auth', 'active', 'can:view-reports'])->group(function (): void {
    Route::get('/reports/profit', [ProfitReportController::class, 'index'])->name('reports.profit');
});
