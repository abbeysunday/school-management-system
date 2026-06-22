<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-redirect if someone hits /dashboard directly
        $user = $request->user();

        $route = match ($user->role) {
            'admin', 'principal', 'bursar' => 'admin.dashboard',
            'teacher'                      => 'teacher.dashboard',
            'parent'                       => 'parent.dashboard',
            'student'                      => 'student.dashboard',
            default                        => null,
        };

        if ($route) {
            return redirect()->route($route);
        }

        return view('dashboard');
    }

    public function admin()   { return view('admin.dashboard'); }
    public function teacher() { return view('teacher.dashboard'); }
    public function parent()  { return view('parent.dashboard'); }
    public function student() { return view('student.dashboard'); }
}
