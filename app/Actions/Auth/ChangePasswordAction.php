<?php

namespace App\Actions\Auth;

use App\Actions\Audit\LogActivityAction;
use App\Models\User;
use Illuminate\Http\Request;

class ChangePasswordAction
{
    public function __construct(private readonly LogActivityAction $logActivity) {}

    public function execute(User $user, string $newPassword, Request $request): void
    {
        $user->update(['password' => $newPassword]);
        $request->session()->regenerate();

        $this->logActivity->execute(
            $user,
            'user.password_changed',
            User::class,
            $user->id,
        );
    }
}
