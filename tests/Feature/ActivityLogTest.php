<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_user_writes_an_audit_log(): void
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

        $created = User::where('email', 'budi@example.com')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'user.created',
            'subject_type' => User::class,
            'subject_id' => $created->id,
        ]);
        $this->assertSame(1, DB::table('activity_logs')->count());
        $metadata = json_decode(DB::table('activity_logs')->value('metadata'), true);
        $this->assertSame('pengelola', $metadata['role']);
    }

    public function test_role_change_logs_old_and_new_role(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'role' => 'staf',
            ])
            ->assertRedirect(route('users.index'));

        $log = DB::table('activity_logs')->where('action', 'user.role_changed')->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $metadata = json_decode($log->metadata, true);
        $this->assertSame('pengelola', $metadata['old_role']);
        $this->assertSame('staf', $metadata['new_role']);
    }

    public function test_unchanged_role_does_not_write_a_role_change_log(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'role' => 'pengelola',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('activity_logs', ['action' => 'user.role_changed']);
    }

    public function test_deactivation_and_reactivation_are_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->pengelola()->create();

        $this->actingAs($admin)
            ->patch(route('users.status', $target), ['is_active' => 1])
            ->assertRedirect(route('users.index'));
        $this->actingAs($admin)
            ->patch(route('users.status', $target), ['is_active' => 0])
            ->assertRedirect(route('users.index'));

        $actions = DB::table('activity_logs')->orderBy('id')->pluck('action')->all();
        $this->assertSame(['user.deactivated', 'user.activated'], $actions);
    }

    public function test_self_deactivation_is_forbidden_and_not_logged(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('users.status', $admin), ['is_active' => 1])
            ->assertForbidden();

        $this->assertDatabaseMissing('activity_logs', ['action' => 'user.deactivated']);
    }

    public function test_self_role_change_is_forbidden_and_not_logged(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'staf',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('activity_logs', ['action' => 'user.role_changed']);
    }

    public function test_changing_password_is_logged_without_storing_the_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('password.update'), [
                'current_password' => 'password',
                'password' => 'kata-sandi-baru-123',
                'password_confirmation' => 'kata-sandi-baru-123',
            ])
            ->assertRedirect(route('dashboard'));

        $log = DB::table('activity_logs')->where('action', 'user.password_changed')->first();
        $this->assertNotNull($log);
        $this->assertSame($user->id, $log->user_id);
        $this->assertStringNotContainsString('kata-sandi-baru-123', $log->metadata);
    }

    public function test_non_admin_cannot_trigger_user_management_logs(): void
    {
        $staf = User::factory()->create();

        $this->actingAs($staf)->post(route('users.store'), [])->assertForbidden();
        $this->actingAs($staf)->put(route('users.update', $staf), [])->assertForbidden();
        $this->actingAs($staf)->patch(route('users.status', $staf), [])->assertForbidden();

        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    public function test_activity_logs_are_append_only(): void
    {
        $this->assertFalse(Schema::hasColumn('activity_logs', 'updated_at'));
    }
}
