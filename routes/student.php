<?php
/**
 * NaijaSchoolMS — Student Portal Routes
 * ─────────────────────────────────────────────────────────────
 * Pure closure routes — no controllers required for UI preview.
 * POST routes redirect back so forms never 500.
 *
 * Register in routes/web.php (bottom):
 *   require __DIR__.'/student.php';
 */

use Illuminate\Support\Facades\Route;

Route::prefix('student')->name('student.')->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', fn () => view('student.dashboard'))
         ->name('dashboard');

    // ── Timetable ──────────────────────────────────────────
    Route::get('/timetable', fn () => view('student.timetable.index'))
         ->name('timetable.index');

    // ── Results ────────────────────────────────────────────
    Route::get('/results', fn () => view('student.results.index'))
         ->name('results.index');

    Route::get('/results/report-card', fn () =>
        back()->with('success', 'Report card download will be available once the backend is wired.'))
         ->name('results.report-card');

    // ── CBT Exams ──────────────────────────────────────────
    Route::get('/exams', fn () => view('student.cbt.index'))
         ->name('exams.index');

    Route::get('/exams/{exam}/lobby', fn ($exam) =>
        view('student.cbt.lobby', ['examId' => $exam]))
         ->name('exams.lobby');

    Route::post('/exams/{exam}/start', fn ($exam) =>
        redirect()->route('student.exams.take', 1))
         ->name('exams.start');

    Route::get('/exams/take/{attempt}', fn ($attempt) =>
        view('student.cbt.exam', ['attemptId' => $attempt]))
         ->name('exams.take');

    Route::post('/exams/save/{attempt}', fn ($attempt) =>
        response()->json(['status' => 'saved']))
         ->name('exams.save');

    Route::post('/exams/{exam}/submit', fn ($exam) =>
        redirect()->route('student.results.index')
            ->with('success', 'Exam submitted successfully. Results will be published after marking.'))
         ->name('exams.submit');

    Route::get('/exams/result/{attempt}', fn ($attempt) =>
        redirect()->route('student.results.index'))
         ->name('exams.result');

    // ── Attendance ─────────────────────────────────────────
    Route::get('/attendance', fn () => view('student.attendance.index'))
         ->name('attendance.index');

    // ── Announcements ──────────────────────────────────────
    Route::get('/announcements', fn () => view('student.announcements.index'))
         ->name('announcements.index');

    Route::post('/announcements/{id}/read', fn ($id) =>
        response()->json(['status' => 'ok']))
         ->name('announcements.read');

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', fn () => view('student.profile.index'))
         ->name('profile.index');

    Route::post('/profile/photo', fn () =>
        back()->with('success', 'Profile photo updated successfully.'))
         ->name('profile.photo');

    Route::post('/profile/password', fn () =>
        back()->with('success', 'Password changed successfully.'))
         ->name('profile.password');

});
