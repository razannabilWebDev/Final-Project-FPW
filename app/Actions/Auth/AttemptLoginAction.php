<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttemptLoginAction
{
    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{success: bool, message: ?string, redirect: ?string}
     */
    public function execute(array $credentials, bool $remember, Request $request): array
    {
        if (Auth::attempt([...$credentials, 'is_active' => true], $remember)) {
            $request->session()->regenerate();

            return [
                'success' => true,
                'message' => null,
                'redirect' => route('dashboard'),
            ];
        }

        $user = User::where('email', $credentials['email'])->first();
        $message = $user !== null && ! $user->is_active
            ? 'Akun Anda telah dinonaktifkan. Hubungi Admin.'
            : 'Email atau kata sandi tidak sesuai.';

        return [
            'success' => false,
            'message' => $message,
            'redirect' => null,
        ];
    }
}
