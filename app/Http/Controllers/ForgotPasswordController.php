<?php

namespace App\Http\Controllers;

use App\Actions\Auth\ResetPasswordAction;
use App\Actions\Auth\SendPasswordResetLinkAction;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.passwords.email');
    }

    public function sendResetLink(ForgotPasswordRequest $request, SendPasswordResetLinkAction $action): RedirectResponse
    {
        $status = $action->execute($request->validated('email'));

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Terlalu banyak permintaan. Silakan coba lagi beberapa saat.');
        }

        return back()->with('success', 'Jika email terdaftar, tautan penggantian kata sandi telah dikirim.');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function reset(ResetPasswordRequest $request, ResetPasswordAction $action): RedirectResponse
    {
        $status = $action->execute($request->only('token', 'email', 'password'));

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diganti. Silakan masuk dengan kata sandi baru.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Tautan penggantian kata sandi tidak valid atau sudah kedaluwarsa.');
    }
}
