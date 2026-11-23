<?php

namespace App\Providers;

use App\Services\Adapters\Member3StockServiceAdapter;
use App\Services\Contracts\RecipeCostService;
use App\Services\Contracts\StockService;
use App\Services\DefaultRecipeCostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // HPP: implementasi final Anggota 4 (perlu koordinasi Anggota 2).
        $this->app->bind(RecipeCostService::class, DefaultRecipeCostService::class);

        // Stok: adapter ke service terpusat Anggota 3 (idempoten, ledger, lock).
        $this->app->bind(StockService::class, Member3StockServiceAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
