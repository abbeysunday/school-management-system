<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Role-based redirect
        $route = match ($user->role) {
            'admin', 'principal', 'bursar' => 'admin.dashboard',
            'teacher'                      => 'teacher.dashboard',
            'parent'                       => 'parent.dashboard',
            'student'                      => 'student.dashboard',
            default                        => 'dashboard',
        };

        return redirect()->intended(route($route, absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
