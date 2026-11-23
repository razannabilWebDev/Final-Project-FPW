<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $allowed = array_map(
            fn (string $role): ?UserRole => UserRole::tryFrom($role),
            $roles,
        );

        if (! in_array($user->role, array_filter($allowed), true)) {
            abort(403);
        }

        return $next($request);
    }
}
