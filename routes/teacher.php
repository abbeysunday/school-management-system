<?php

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
    });


    Route::get('/questions', [QuestionController::class, 'index'])->name('teacher.questions.index');
Route::get('/questions/create', [QuestionController::class, 'create'])->name('teacher.questions.create');
