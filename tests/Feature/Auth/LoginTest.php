<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke akun Anda');
    }

    #[DataProvider('roleProvider')]
    public function test_each_role_can_login_and_reaches_dashboard(string $role): void
    {
        $user = User::factory()->state(['role' => UserRole::from($role)])->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function roleProvider(): array
    {
        return [
            'admin' => ['admin'],
            'pengelola' => ['pengelola'],
            'staf' => ['staf'],
        ];
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'kata-sandi-salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Email atau kata sandi tidak sesuai.');

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_login(): void
    {
        $user = User::factory()->inactive()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Akun Anda telah dinonaktifkan. Hubungi Admin.');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post(route('login.store'), [])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_is_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->from(route('login'))
                ->post(route('login.store'), [
                    'email' => 'tidak-ada@example.com',
                    'password' => 'password',
                ])
                ->assertRedirect(route('login'));
        }

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'tidak-ada@example.com',
                'password' => 'password',
            ])
            ->assertStatus(429);
    }
}
