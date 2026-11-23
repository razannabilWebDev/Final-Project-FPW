<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Kompatibilitas dengan branch skeleton: kolom role/is_active (milik A1)
        // dan route login belum ada. Tambahkan hanya di lingkungan test.
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table): void {
                $table->string('role')->default('admin');
                $table->boolean('is_active')->default(true);
            });
        }

        if (! Route::has('login')) {
            Route::get('/login', fn () => 'login')->name('login');
        }

        Gate::define('create-sales', fn (User $user): bool => (bool) ($user->is_active ?? true));
        Gate::define('update-sale-status', fn (User $user): bool => (bool) ($user->is_active ?? true));
    }
}
