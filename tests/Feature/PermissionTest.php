<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, list<string>>
     */
    private function gateMatrix(): array
    {
        $all = UserRole::values();

        return [
            'manage-users' => ['admin'],
            'manage-menus' => ['admin'],
            'manage-recipes' => ['admin'],
            'manage-recipe-costs' => ['admin', 'pengelola'],
            'view-recipes' => ['admin', 'pengelola'],
            'manage-ingredients' => ['admin'],
            'manage-stock' => ['admin', 'pengelola'],
            'update-stock-physical' => $all,
            'record-waste' => ['admin', 'staf'],
            'create-purchases' => ['admin', 'staf'],
            'verify-purchases' => ['admin', 'pengelola'],
            'view-sales-summary' => ['admin', 'pengelola'],
            'create-sales' => $all,
            'update-sale-status' => $all,
            'manage-expenses' => ['admin'],
            'view-reports' => ['admin'],
            'export-reports' => ['admin'],
            'manage-forecast' => ['admin'],
            'view-forecast' => $all,
        ];
    }

    public function test_gate_matrix_matches_prd_permission_rules(): void
    {
        foreach ($this->gateMatrix() as $gate => $allowedRoles) {
            foreach (UserRole::cases() as $role) {
                $user = User::factory()->state(['role' => $role])->make();
                $expected = in_array($role->value, $allowedRoles, true);

                $this->assertSame(
                    $expected,
                    Gate::forUser($user)->allows($gate),
                    "Gate {$gate} untuk role {$role->value}",
                );
            }
        }
    }

    public function test_inactive_user_is_denied_all_gates(): void
    {
        $user = User::factory()->admin()->inactive()->make();

        foreach (array_keys($this->gateMatrix()) as $gate) {
            $this->assertFalse(Gate::forUser($user)->allows($gate), "Gate {$gate} untuk akun nonaktif");
        }
    }

    public function test_role_middleware_allows_only_listed_roles(): void
    {
        Route::middleware(['web', 'auth', 'role:admin'])
            ->get('/__role-admin', fn () => response('ok'))
            ->name('__role_admin');

        $this->actingAs(User::factory()->pengelola()->create())
            ->get('/__role-admin')
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/__role-admin')
            ->assertOk();
    }

    public function test_active_middleware_logs_out_deactivated_account(): void
    {
        Route::middleware(['web', 'auth', 'active'])
            ->get('/__active', fn () => response('ok'))
            ->name('__active');

        $this->actingAs(User::factory()->inactive()->create())
            ->get('/__active')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }
}
