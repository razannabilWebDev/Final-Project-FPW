<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_change_password_page_requires_authentication(): void
    {
        $this->get(route('password.edit'))->assertRedirect(route('login'));
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('password.update'), [
                'current_password' => 'password',
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Kata sandi berhasil diubah.');

        $this->assertTrue(Hash::check('kata-sandi-baru-123', $user->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('password.update'), [
                'current_password' => 'salah',
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_password_must_be_confirmed_and_long_enough(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('password.update'), [
                'current_password' => 'password',
                'password' => 'pendek',
                'password_confirmation' => 'berbeda',
            ])
            ->assertSessionHasErrors(['password']);
    }
}
