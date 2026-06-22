<?php
/**
 * NaijaSchoolMS — Parent Portal Routes
 * ─────────────────────────────────────────────────────────────
 * Pure closure routes — no controllers required for UI preview.
 *
 * Register in routes/web.php (bottom):
 *   require __DIR__.'/parent.php';
 */

use App\Http\Controllers\Parent\DashboardController;
use App\Http\Controllers\Parent\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('parent')->name('parent.')->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', fn () => view('parent.dashboard'))
         ->name('dashboard');

    // ── Fees ───────────────────────────────────────────────
     // ── Fees ──
    Route::get('/fees', [PaymentController::class, 'fees'])->name('fees.index');
    Route::get('/fees/checkout', [PaymentController::class, 'checkout'])->name('fees.checkout');
    Route::post('/fees/pay', [PaymentController::class, 'pay'])->name('fees.pay');
    Route::get('/fees/callback', [PaymentController::class, 'callback'])->name('fees.callback');
    Route::get('/fees/success', [PaymentController::class, 'success'])->name('fees.success');
    Route::get('/fees/history', [PaymentController::class, 'history'])->name('fees.history');
    Route::get('/fees/receipt/{ref}', [PaymentController::class, 'receipt'])->name('fees.receipt');
    // ── Children ───────────────────────────────────────────
    Route::get('/children/{childId}/results', fn ($childId) =>
        view('parent.children.results', ['childId' => $childId]))
         ->name('children.results');

    Route::get('/children/{childId}/attendance', fn ($childId) =>
        view('parent.children.attendance', ['childId' => $childId]))
         ->name('children.attendance');

    // Report card download — stream PDF in production
    Route::get('/children/{childId}/report-card/{term}', fn ($childId, $term) =>
        back()->with('info', 'Report card PDF generation coming soon.'))
         ->name('children.report-card');

    // ── Announcements ──────────────────────────────────────
    Route::get('/announcements', fn () => view('parent.announcements.index'))
         ->name('announcements.index');

    Route::post('/announcements/{id}/read', fn ($id) =>
        response()->json(['status' => 'ok']))
         ->name('announcements.read');

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', fn () => view('parent.profile'))
         ->name('profile');

    Route::post('/profile', fn () =>
        back()->with('success', 'Profile updated successfully.'))
         ->name('profile.update');

});



Route::middleware(['auth', 'verified', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    // ── Dashboard ──


    // ── Fees ──

});

Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');


