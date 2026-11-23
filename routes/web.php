<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleController;
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

// Placeholder agar middleware auth memiliki tujuan redirect (diganti Anggota 1 saat integrasi).
Route::get('/login', fn () => 'Login')->name('login');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])
        ->name('sales.create')->middleware('can:create-sales');
    Route::post('/sales', [SaleController::class, 'store'])
        ->name('sales.store')->middleware('can:create-sales');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
        ->name('sales.update-status')->middleware('can:update-sale-status');
});
