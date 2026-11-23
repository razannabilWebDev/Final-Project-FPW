<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteController;
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
