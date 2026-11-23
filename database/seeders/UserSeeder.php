<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin Utama', 'email' => 'admin@smartfood.test', 'role' => UserRole::Admin, 'is_active' => true],
            ['name' => 'Pengelola Utama', 'email' => 'pengelola@smartfood.test', 'role' => UserRole::Pengelola, 'is_active' => true],
            ['name' => 'Staf Dapur', 'email' => 'staf@smartfood.test', 'role' => UserRole::Staf, 'is_active' => true],
            ['name' => 'Staf Nonaktif', 'email' => 'staf-nonaktif@smartfood.test', 'role' => UserRole::Staf, 'is_active' => false],
        ];

        foreach ($accounts as $account) {
            $user = User::where('email', $account['email'])->first();

            if ($user !== null) {
                continue;
            }

            $user = new User([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => 'password',
            ]);
            $user->role = $account['role'];
            $user->is_active = $account['is_active'];
            $user->save();
        }
    }
}
