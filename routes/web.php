<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});













// Public
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// Authenticated users only
Route::middleware(['auth', 'verified'])->group(function () {

    // Generic fallback dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ── Admin / Principal / Bursar ── */
    Route::middleware(['role:admin,principal,bursar'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    });

    /* ── Teacher ── */
    Route::middleware(['role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    });

    /* ── Parent ── */
    Route::middleware(['role:parent'])->prefix('parent')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'parent'])->name('parent.dashboard');
    });

    /* ── Student ── */
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
    });

    // Shared profile (all roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__.'/auth.php';
require __DIR__.'/parent.php';
