<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AttemptLoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request, AttemptLoginAction $action): RedirectResponse
    {
        $result = $action->execute(
            $request->only('email', 'password'),
            $request->boolean('remember'),
            $request,
        );

        if (! $result['success']) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', $result['message']);
        }

        return redirect()->to($result['redirect'])->with('success', 'Selamat datang kembali.');
    }
}
