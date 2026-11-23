<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ChangePasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request, ChangePasswordAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $action->execute($user, $request->string('password')->toString(), $request);

        return redirect()->route('dashboard')->with('success', 'Kata sandi berhasil diubah.');
    }
}
