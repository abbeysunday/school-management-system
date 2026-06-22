<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Support single or multiple roles: role:admin || role:admin,teacher
        $allowed = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map(fn($r) => trim($r))
            ->unique()
            ->toArray();

        if (!in_array($user->role, $allowed)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
