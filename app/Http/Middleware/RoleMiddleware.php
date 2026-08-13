<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}