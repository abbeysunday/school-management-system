<?php
/**
 * NaijaSchoolMS — Student Portal Routes
 * File: routes/student.php
 *
 * Register in routes/web.php:
 *   require __DIR__.'/student.php';
 *
 * Or in bootstrap/app.php:
 *   ->withRouting(web: __DIR__.'/../routes/web.php', ...)
 *   and add at bottom of web.php: require base_path('routes/student.php');
 */

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\TimetableController;
use App\Http\Controllers\Student\ResultsController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\AnnouncementController;
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])
     ->prefix('student')
     ->name('student.')
     ->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // ── Timetable ──────────────────────────────────────────
    Route::get('/timetable', [TimetableController::class, 'index'])
         ->name('timetable.index');

    // ── Results ────────────────────────────────────────────
    Route::get('/results', [ResultsController::class, 'index'])
         ->name('results.index');
    Route::get('/results/report-card', [ResultsController::class, 'downloadReportCard'])
         ->name('results.report-card');

    // ── CBT Exams ──────────────────────────────────────────
    Route::get('/exams', [ExamController::class, 'index'])
         ->name('exams.index');

    Route::get('/exams/{exam}/lobby', [ExamController::class, 'lobby'])
         ->name('exams.lobby');

    Route::post('/exams/{exam}/start', [ExamController::class, 'start'])
         ->name('exams.start');

    Route::get('/exams/take/{attempt}', [ExamController::class, 'take'])
         ->name('exams.take');

    // Auto-save answers (AJAX, called every 30s by student.js)
    Route::post('/exams/save/{attempt}', [ExamController::class, 'saveProgress'])
         ->name('exams.save');

    Route::post('/exams/{exam}/submit', [ExamController::class, 'submit'])
         ->name('exams.submit');

    Route::get('/exams/result/{attempt}', [ExamController::class, 'result'])
         ->name('exams.result');

    // ── Attendance ─────────────────────────────────────────
    Route::get('/attendance', [AttendanceController::class, 'index'])
         ->name('attendance.index');

    // ── Announcements ──────────────────────────────────────
    Route::get('/announcements', [AnnouncementController::class, 'index'])
         ->name('announcements.index');

    Route::post('/announcements/{announcement}/read', [AnnouncementController::class, 'markRead'])
         ->name('announcements.read');

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'index'])
         ->name('profile.index');

    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])
         ->name('profile.photo');

    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
         ->name('profile.password');

});
