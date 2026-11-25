<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Daftar Pengguna');
    }

    public function test_user_index_renders_create_user_modal_on_the_same_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('open-create-user')
            ->assertSee('create-user-modal')
            ->assertSee(route('users.store'))
            ->assertSee('user-create-form');
    }

    public function test_validation_errors_redirect_back_to_the_same_index_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('users.index'))
            ->post(route('users.store'), [
                'name' => '',
                'email' => '',
                'role' => '',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_admin_can_create_pengelola_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Budi Pengelola',
                'email' => 'budi@example.com',
                'role' => 'pengelola',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'pengelola',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_another_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Admin Kedua',
                'email' => 'admin2@example.com',
                'role' => 'admin',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => 'admin2@example.com', 'role' => 'admin']);
    }

    public function test_admin_can_edit_user_data(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $target), [
                'name' => 'Nama Baru',
                'email' => $target->email,
                'role' => 'staf',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertSame('Nama Baru', $target->fresh()->name);
        $this->assertSame(UserRole::Staf, $target->fresh()->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'staf',
            ])
            ->assertForbidden();

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('users.status', $admin), ['is_active' => 1])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_deactivate_and_reactivate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->patch(route('users.status', $target), ['is_active' => 1])
            ->assertRedirect(route('users.index'));
        $this->assertFalse($target->fresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('users.status', $target), ['is_active' => 0])
            ->assertRedirect(route('users.index'));
        $this->assertTrue($target->fresh()->is_active);
    }

    public function test_pengelola_and_staf_cannot_access_user_management(): void
    {
        $target = User::factory()->create();

        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $actor = User::factory()->state(['role' => $role])->create();

            $this->actingAs($actor)->get(route('users.index'))->assertForbidden();
            $this->actingAs($actor)->get(route('users.create'))->assertForbidden();
            $this->actingAs($actor)->post(route('users.store'), [])->assertForbidden();
            $this->actingAs($actor)->get(route('users.edit', $target))->assertForbidden();
            $this->actingAs($actor)->put(route('users.update', $target), [])->assertForbidden();
            $this->actingAs($actor)->patch(route('users.status', $target), [])->assertForbidden();
        }
    }

    public function test_user_creation_validates_input(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => '',
                'email' => 'bukan-email',
                'role' => 'direktur',
                'password' => 'pendek',
                'password_confirmation' => 'berbeda',
            ])
            ->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_user_email_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Duplikat',
                'email' => $existing->email,
                'role' => 'staf',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHasErrors('email');
    }
}
