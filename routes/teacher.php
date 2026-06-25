<?php

use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\CaScoreController;
use App\Http\Controllers\Teacher\ExamScoreController;
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


// 225. Score Entry Index
Route::get('/scores', [CaScoreController::class, 'index'])->name('ca-scores.index');

// 226. CA Score Entry Form
Route::get('/ca-scores/{armSubject}', [CaScoreController::class, 'showForm'])->name('ca-scores.form');
// 229. CA Score Save
Route::post('/ca-scores/{armSubject}', [CaScoreController::class, 'store'])->name('ca-scores.store');

// 235. Exam Score Entry Form
Route::get('/exam-scores/{armSubject}', [ExamScoreController::class, 'showForm'])->name('exam-scores.form');
// 237. Exam Score Save
Route::post('/exam-scores/{armSubject}', [ExamScoreController::class, 'store'])->name('exam-scores.store');
// 240. Submit for Review
Route::post('/exam-scores/{armSubject}/submit', [ExamScoreController::class, 'submitForReview'])->name('exam-scores.submit');








});


    Route::get('/questions', [QuestionController::class, 'index'])->name('teacher.questions.index');
Route::get('/questions/create', [QuestionController::class, 'create'])->name('teacher.questions.create');

