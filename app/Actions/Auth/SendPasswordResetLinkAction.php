<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    /**
     * Kirim tautan reset hanya untuk akun aktif. Email tidak dikenal tetap
     * dibalas generik oleh controller agar tidak membocorkan keberadaan akun.
     */
    public function execute(string $email): string
    {
        $user = User::where('email', $email)->first();

        if ($user === null || ! $user->is_active) {
            return Password::INVALID_USER;
        }

        return Password::broker()->sendResetLink(['email' => $email]);
    }
}
