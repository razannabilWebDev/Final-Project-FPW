<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_is_available_for_guests(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa kata sandi?')
            ->assertSee(route('password.email'));
    }

    public function test_reset_link_is_sent_to_an_active_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('success', 'Jika email terdaftar, tautan penggantian kata sandi telah dikirim.');

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use ($user): bool {
                $mail = $notification->toMail($user);
                $html = $mail->render();
                $expectedPath = route('password.reset', [
                    'token' => $notification->token,
                    'email' => $user->email,
                ], false);

                return $mail->subject === 'Atur Ulang Kata Sandi SmartFood Home'
                    && str_contains($mail->actionUrl ?? '', $expectedPath)
                    && ! str_contains($html, 'notification-logo')
                    && str_contains($html, 'SmartFood Home');
            },
        );
    }

    public function test_unknown_email_gets_a_generic_response_without_sending_notification(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'tidak.terdaftar@example.com'])
            ->assertSessionHas('success', 'Jika email terdaftar, tautan penggantian kata sandi telah dikirim.');

        Notification::assertNothingSent();
    }

    public function test_inactive_account_cannot_request_a_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->inactive()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('success', 'Jika email terdaftar, tautan penggantian kata sandi telah dikirim.');

        Notification::assertNothingSent();
    }

    public function test_reset_page_shows_token_and_email(): void
    {
        $this->get(route('password.reset', ['token' => 'token-contoh', 'email' => 'user@example.com']))
            ->assertOk()
            ->assertSee('Atur Ulang Kata Sandi')
            ->assertSee('token-contoh', false)
            ->assertSee('user@example.com', false);
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.reset.store'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Kata sandi berhasil diganti. Silakan masuk dengan kata sandi baru.');

        $this->assertTrue(Hash::check('kata-sandi-baru-123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_reset_token_cannot_be_used_twice(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'kata-sandi-baru-123',
            'password_confirmation' => 'kata-sandi-baru-123',
        ];

        $this->post(route('password.reset.store'), $payload)
            ->assertRedirect(route('login'));

        $this->post(route('password.reset.store'), $payload)
            ->assertSessionHas('error', 'Tautan penggantian kata sandi tidak valid atau sudah kedaluwarsa.');
    }

    public function test_invalid_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.reset.store'), [
            'token' => 'token-salah',
            'email' => $user->email,
            'password' => 'kata-sandi-baru-123',
            'password_confirmation' => 'kata-sandi-baru-123',
        ])->assertSessionHas('error', 'Tautan penggantian kata sandi tidak valid atau sudah kedaluwarsa.');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_inactive_account_cannot_reset_with_a_token(): void
    {
        $user = User::factory()->inactive()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.reset.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'kata-sandi-baru-123',
            'password_confirmation' => 'kata-sandi-baru-123',
        ])->assertSessionHas('error', 'Tautan penggantian kata sandi tidak valid atau sudah kedaluwarsa.');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_reset_requires_confirmation_and_minimum_length(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.reset.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'pendek',
            'password_confirmation' => 'bedasekali',
        ])->assertSessionHasErrors(['password']);
    }
}
