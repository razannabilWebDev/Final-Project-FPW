<?php

namespace App\Providers;

use App\Models\User;
use App\UserRole;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('manage-users', $this->allow(UserRole::Admin));
        Gate::define('manage-menus', $this->allow(UserRole::Admin));
        Gate::define('manage-recipes', $this->allow(UserRole::Admin));
        Gate::define('manage-recipe-costs', $this->allow(UserRole::Admin, UserRole::Pengelola));
        Gate::define('view-recipes', $this->allow(UserRole::Admin, UserRole::Pengelola));
        Gate::define('manage-ingredients', $this->allow(UserRole::Admin));
        Gate::define('manage-stock', $this->allow(UserRole::Admin, UserRole::Pengelola));
        Gate::define('update-stock-physical', $this->allow(...UserRole::cases()));
        Gate::define('record-waste', $this->allow(UserRole::Admin, UserRole::Staf));
        Gate::define('create-purchases', $this->allow(UserRole::Admin, UserRole::Staf));
        Gate::define('verify-purchases', $this->allow(UserRole::Admin, UserRole::Pengelola));
        Gate::define('view-sales-summary', $this->allow(UserRole::Admin, UserRole::Pengelola));
        Gate::define('create-sales', $this->allow(...UserRole::cases()));
        Gate::define('update-sale-status', $this->allow(...UserRole::cases()));
        Gate::define('manage-expenses', $this->allow(UserRole::Admin));
        Gate::define('view-reports', $this->allow(UserRole::Admin));
        Gate::define('export-reports', $this->allow(UserRole::Admin));
        Gate::define('manage-forecast', $this->allow(UserRole::Admin));
        Gate::define('view-forecast', $this->allow(...UserRole::cases()));
    }

    private function allow(UserRole ...$roles): Closure
    {
        return fn (User $user): bool => $user->is_active && in_array($user->role, $roles, true);
    }
}
