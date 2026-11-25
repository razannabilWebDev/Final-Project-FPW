<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('roleViewProvider')]
    public function test_each_role_sees_its_own_dashboard(string $role, string $expectedText): void
    {
        $user = User::factory()->state(['role' => UserRole::from($role)])->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($expectedText);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function roleViewProvider(): array
    {
        return [
            'admin' => ['admin', 'Ringkasan Usaha'],
            'pengelola' => ['pengelola', 'Dashboard Pengelola'],
            'staf' => ['staf', 'Dashboard Staf'],
        ];
    }

    public function test_all_roles_see_the_same_greeting(): void
    {
        foreach (UserRole::cases() as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk()
                ->assertSee('Selamat datang,')
                ->assertDontSee('Halo,')
                ->assertDontSee('Selamat pagi,');
        }
    }

    public function test_pengelola_and_staf_cannot_see_financial_summary(): void
    {
        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk()
                ->assertDontSee('Laba Bersih')
                ->assertDontSee('Total HPP')
                ->assertDontSee('Biaya Operasional');
        }
    }

    public function test_admin_can_see_financial_summary(): void
    {
        $admin = User::factory()->state(['role' => UserRole::Admin])->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Laba Bersih')
            ->assertSee('Total HPP')
            ->assertSee('Biaya Operasional');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_pengelola_dashboard_renders_purchase_dates(): void
    {
        $pengelola = User::factory()->state(['role' => UserRole::Pengelola])->create();

        DB::table('purchases')->insert([
            'purchase_code' => 'PUR-001',
            'purchase_date' => '2026-11-23',
            'supplier_name' => 'Toko Berkah',
            'status' => 'pending',
            'total' => 150000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($pengelola)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('PUR-001')
            ->assertSee('23 Nov 2026');
    }
}
