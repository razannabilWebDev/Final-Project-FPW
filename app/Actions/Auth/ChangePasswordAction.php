<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;

class ChangePasswordAction
{
    public function execute(User $user, string $newPassword, Request $request): void
    {
        $user->update(['password' => $newPassword]);
        $request->session()->regenerate();
    }
}
