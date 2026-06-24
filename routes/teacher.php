<?php

use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\QuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('teacher.dashboard');
        })->name('dashboard');

        // Classes, attendance, results, etc.
        Route::get('/classes', function () {
            return view('teacher.classes');
        })->name('classes');






        /* ── Teacher Attendance ── */
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');


Route::get('/timetable', [\App\Http\Controllers\Teacher\TimetableController::class, 'index'])->name('timetable');











});


    Route::get('/questions', [QuestionController::class, 'index'])->name('teacher.questions.index');
Route::get('/questions/create', [QuestionController::class, 'create'])->name('teacher.questions.create');

