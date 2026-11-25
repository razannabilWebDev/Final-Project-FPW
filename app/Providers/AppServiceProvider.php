<?php

namespace App\Providers;

use App\Services\Adapters\Member3StockServiceAdapter;
use App\Services\Contracts\RecipeCostService;
use App\Services\Contracts\StockService;
use App\Services\NotificationService;
use App\Services\RecipeCostService as A2RecipeCostService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // HPP: implementasi final Anggota 2 (versi resep terbaru × average_cost).
        $this->app->bind(RecipeCostService::class, A2RecipeCostService::class);

        // Stok: adapter ke service terpusat Anggota 3 (idempoten, ledger, lock).
        $this->app->bind(StockService::class, Member3StockServiceAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', static function ($view): void {
            $user = auth()->user();

            $view->with(
                'navbarNotifications',
                $user !== null
                    ? app(NotificationService::class)->forUser($user)
                    : collect(),
            );
        });
    }
}
