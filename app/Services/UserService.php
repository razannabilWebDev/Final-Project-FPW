<?php

namespace App\Services;

use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->role = UserRole::from($data['role']);
            $user->save();

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

        DB::transaction(function () use ($user, $data, $role): void {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $role;

            if (! empty($data['password'] ?? null)) {
                $user->password = $data['password'];
            }

            $user->save();
        });

        return $user;
    }

    public function setActive(User $user, User $actor, bool $isActive): User
    {
        if ($user->is($actor) && ! $isActive) {
            abort(403, 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->is_active = $isActive;
        $user->save();

        return $user;
    }
}
