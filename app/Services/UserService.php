<?php

namespace App\Services;

use App\Actions\Audit\LogActivityAction;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(private readonly LogActivityAction $logActivity) {}

    /**
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data, User $actor): User
    {
        return DB::transaction(function () use ($data, $actor): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->role = UserRole::from($data['role']);
            $user->save();

            $this->logActivity->execute(
                $actor,
                'user.created',
                User::class,
                $user->id,
                ['role' => $user->role->value],
            );

            return $user;
        });
    }

    /**
     * @param  array{name: string, email: string, role: string, password?: ?string}  $data
     */
    public function update(User $user, User $actor, array $data): User
    {
        $role = UserRole::from($data['role']);

        if ($user->is($actor) && $role !== $actor->role) {
            abort(403, 'Anda tidak dapat mengubah role akun sendiri.');
        }

        $previousRole = $user->role->value;

        DB::transaction(function () use ($user, $actor, $data, $role, $previousRole): void {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $role;

            if (! empty($data['password'] ?? null)) {
                $user->password = $data['password'];
            }

            $user->save();

            if ($previousRole !== $role->value) {
                $this->logActivity->execute(
                    $actor,
                    'user.role_changed',
                    User::class,
                    $user->id,
                    ['old_role' => $previousRole, 'new_role' => $role->value],
                );
            }
        });

        return $user;
    }

    public function setActive(User $user, User $actor, bool $isActive): User
    {
        if ($user->is($actor) && ! $isActive) {
            abort(403, 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $previousState = $user->is_active;

        DB::transaction(function () use ($user, $actor, $isActive, $previousState): void {
            $user->is_active = $isActive;
            $user->save();

            if ($previousState !== $isActive) {
                $this->logActivity->execute(
                    $actor,
                    $isActive ? 'user.activated' : 'user.deactivated',
                    User::class,
                    $user->id,
                    ['old_is_active' => $previousState, 'new_is_active' => $isActive],
                );
            }
        });

        return $user;
    }
}
