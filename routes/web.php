<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->name('password.email')
        ->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
        ->name('password.reset.store')
        ->middleware('throttle:5,1');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store')
    ->middleware('throttle:5,1');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/ganti-password', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::post('/ganti-password', [ChangePasswordController::class, 'update'])->name('password.update');

    Route::prefix('menus')->name('menus.')->group(function (): void {
        Route::get('/', [MenuController::class, 'index'])->name('index');

        Route::middleware('can:manage-menus')->group(function (): void {
            Route::get('/create', [MenuController::class, 'create'])->name('create');
            Route::post('/', [MenuController::class, 'store'])->name('store');
            Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
            Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
            Route::patch('/{menu}/status', [MenuController::class, 'setActive'])->name('status');
        });

        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
    });

    Route::prefix('resep')->name('recipes.')->group(function (): void {
        Route::middleware('can:view-recipes')->group(function (): void {
            Route::get('/', [RecipeController::class, 'index'])->name('index');
        });

        Route::middleware('can:manage-recipes')->group(function (): void {
            Route::get('/tambah', [RecipeController::class, 'create'])->name('create');
            Route::post('/', [RecipeController::class, 'store'])->name('store');
        });

        Route::middleware('can:view-recipes')->group(function (): void {
            Route::get('/{recipe}', [RecipeController::class, 'show'])->name('show');
        });

        Route::middleware('can:manage-recipes')->group(function (): void {
            Route::get('/{recipe}/ubah', [RecipeController::class, 'edit'])->name('edit');
            Route::put('/{recipe}', [RecipeController::class, 'update'])->name('update');
            Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
        });
    });

    Route::middleware('can:manage-users')->prefix('users')->name('users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/status', [UserController::class, 'setActive'])->name('status');
    });

    Route::get('/bahan-baku', [IngredientController::class, 'index'])->name('ingredients.index');

    Route::middleware('can:manage-ingredients')->prefix('bahan-baku')->name('ingredients.')->group(function (): void {
        Route::get('/create', [IngredientController::class, 'create'])->name('create');
        Route::post('/', [IngredientController::class, 'store'])->name('store');
        Route::get('/{ingredient}/edit', [IngredientController::class, 'edit'])->name('edit');
        Route::put('/{ingredient}', [IngredientController::class, 'update'])->name('update');
        Route::patch('/{ingredient}/status', [IngredientController::class, 'setActive'])->name('status');
    });

    Route::middleware('can:manage-stock')->prefix('stok')->name('stock.')->group(function (): void {
        Route::get('/pergerakan', [StockMovementController::class, 'index'])->name('movements');
    });

    Route::middleware('can:update-stock-physical')->prefix('stok')->name('stock.')->group(function (): void {
        Route::get('/penyesuaian', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('/penyesuaian', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    });

    Route::middleware('can:record-waste')->prefix('stok')->name('stock.')->group(function (): void {
        Route::get('/susut', [WasteController::class, 'create'])->name('waste.create');
        Route::post('/susut', [WasteController::class, 'store'])->name('waste.store');
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

Route::middleware(['auth', 'active', 'can:export-reports'])->group(function (): void {
    Route::get('/reports/profit/export/excel', [ProfitReportController::class, 'exportExcel'])->name('reports.profit.excel');
    Route::get('/reports/profit/export/pdf', [ProfitReportController::class, 'exportPdf'])->name('reports.profit.pdf');
});
