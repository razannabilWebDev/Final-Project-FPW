<?php

namespace App\Providers;

use App\Services\Contracts\RecipeCostService;
use App\Services\Contracts\StockService;
use App\Services\DefaultRecipeCostService;
use App\Services\DefaultStockService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Stub sementara; diganti implementasi Anggota 2 & 3 saat integrasi.
        $this->app->bind(RecipeCostService::class, DefaultRecipeCostService::class);
        $this->app->bind(StockService::class, DefaultStockService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
