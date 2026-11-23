<?php

use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
