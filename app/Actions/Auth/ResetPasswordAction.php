<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction
{
    /**
     * @param  array{token: string, email: string, password: string}  $credentials
     */
    public function execute(array $credentials): string
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user === null || ! $user->is_active) {
            return Password::INVALID_USER;
        }

        return Password::broker()->reset($credentials, function (User $user, string $password): void {
            $user->password = Hash::make($password);
            $user->save();
        });
    }
}
