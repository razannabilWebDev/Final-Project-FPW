<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_guest_logout_redirects_to_login(): void
    {
        $this->post(route('logout'))->assertRedirect(route('login'));
    }
}
