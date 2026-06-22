<?php

use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\ArmSubjectController;
use App\Http\Controllers\Admin\ClassArmController;
use App\Http\Controllers\Admin\ClassLevelController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\SchoolSetupController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentEnrollmentController;
use App\Http\Controllers\Admin\CbtExamController;
use App\Http\Controllers\Admin\FeeCategoryController;
use App\Http\Controllers\Admin\FeeLedgerController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherAssignmentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TermController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin,principal,bursar'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // School Profile Setup
        Route::get('/setup/school', [SchoolSetupController::class, 'index'])
            ->name('setup.school');
        Route::post('/setup/school', [SchoolSetupController::class, 'update'])
            ->name('setup.school.update');



            // ── Academic Sessions ──
Route::get('/sessions', [AcademicSessionController::class, 'index'])->name('sessions.index');
Route::get('/sessions/create', [AcademicSessionController::class, 'create'])->name('sessions.create');
Route::post('/sessions', [AcademicSessionController::class, 'store'])->name('sessions.store');
Route::get('/sessions/{session}/edit', [AcademicSessionController::class, 'edit'])->name('sessions.edit');
Route::put('/sessions/{session}', [AcademicSessionController::class, 'update'])->name('sessions.update');
Route::delete('/sessions/{session}', [AcademicSessionController::class, 'destroy'])->name('sessions.destroy');
Route::post('/sessions/{session}/set-current', [AcademicSessionController::class, 'setAsCurrent'])->name('sessions.set-current');

// ── Terms ──
Route::get('/terms', [TermController::class, 'index'])->name('terms.index');
Route::get('/terms/create', [TermController::class, 'create'])->name('terms.create');
Route::post('/terms', [TermController::class, 'store'])->name('terms.store');
Route::get('/terms/{term}/edit', [TermController::class, 'edit'])->name('terms.edit');
Route::put('/terms/{term}', [TermController::class, 'update'])->name('terms.update');
Route::delete('/terms/{term}', [TermController::class, 'destroy'])->name('terms.destroy');
Route::post('/terms/{term}/set-current', [TermController::class, 'setAsCurrent'])->name('terms.set-current');
Route::post('/terms/{term}/school-days', [TermController::class, 'updateSchoolDays'])->name('terms.school-days');



/* ── Class Levels & Arms (Combined) ── */
Route::get('/classes/levels', [ClassLevelController::class, 'index'])->name('classes.levels');
Route::post('/classes/levels', [ClassLevelController::class, 'store'])->name('classes.levels.store');
Route::put('/classes/levels/{classLevel}', [ClassLevelController::class, 'update'])->name('classes.levels.update');
Route::delete('/classes/levels/{classLevel}', [ClassLevelController::class, 'destroy'])->name('classes.levels.destroy');

Route::post('/classes/arms', [ClassArmController::class, 'store'])->name('classes.arms.store');
Route::put('/classes/arms/{classArm}', [ClassArmController::class, 'update'])->name('classes.arms.update');
Route::delete('/classes/arms/{classArm}', [ClassArmController::class, 'destroy'])->name('classes.arms.destroy');

/* ── Subjects ── */
Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

/* ── Subject Assignments ── */
Route::get('/subjects/assignments', [ArmSubjectController::class, 'index'])->name('subjects.assignments');
Route::post('/subjects/assignments', [ArmSubjectController::class, 'store'])->name('subjects.assignments.store');
Route::delete('/subjects/assignments/{armSubject}', [ArmSubjectController::class, 'destroy'])->name('subjects.assignments.destroy');




/* ── Grading Scale ── */
Route::get('/settings/grading', [SettingController::class, 'grading'])->name('settings.grading');
Route::post('/settings/grading', [SettingController::class, 'updateGrading'])->name('settings.grading.update');

/* ── CA Configuration ── */
Route::get('/settings/ca-config', [SettingController::class, 'caConfig'])->name('settings.ca-config');
Route::post('/settings/ca-config', [SettingController::class, 'updateCaConfig'])->name('settings.ca-config.update');

/* ── School Calendar ── */
Route::get('/settings/calendar', [SettingController::class, 'calendar'])->name('settings.calendar');
Route::post('/settings/calendar', [SettingController::class, 'updateCalendar'])->name('settings.calendar.update');




// ── Student Enrollment ──
/* ── Enrollment ── */
Route::get('/students/enrollment', [StudentEnrollmentController::class, 'index'])->name('students.enrollment');
Route::post('/students/enrollment', [StudentEnrollmentController::class, 'store'])->name('students.enrollment.store');
Route::post('/students/{student}/transfer', [StudentEnrollmentController::class, 'transfer'])->name('students.transfer');
Route::delete('/students/enrollment/{enrollment}', [StudentEnrollmentController::class, 'destroy'])->name('students.enrollment.destroy');
// Bulk Import Routes
    /* ── Import ── */
Route::get('/students/import/form', [StudentController::class, 'showImportForm'])->name('students.import.form');
Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
Route::get('/students/template/download', [StudentController::class, 'downloadTemplate'])->name('students.template');


/* ── Parent Linking ── */
Route::post('/students/link-parent', [StudentController::class, 'linkParent'])->name('parents.link-student');
Route::delete('/students/unlink-parent/{link}', [StudentController::class, 'unlinkParent'])->name('parents.unlink');
    // ID Card Route
    Route::get('students/{id}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');

    // Standard CRUD
    Route::resource('students', StudentController::class);

    // ── Parents ──
    Route::resource('parents', ParentController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::post('parents/{parent}/regenerate-password', [ParentController::class, 'regeneratePassword'])->name('parents.regenerate-password');
    Route::post('parent-students', [ParentController::class, 'linkToStudent'])->name('parents.link-student');
    Route::delete('parent-students/{parentStudent}', [ParentController::class, 'unlink'])->name('parents.unlink');










Route::get('/questions', [QuestionBankController::class, 'index'])->name('questions.index');
Route::get('/questions/create', [QuestionBankController::class, 'create'])->name('questions.create');
Route::post('/questions', [QuestionBankController::class, 'store'])->name('questions.store');
Route::get('/questions/{question}/edit', [QuestionBankController::class, 'edit'])->name('questions.edit');
Route::put('/questions/{question}', [QuestionBankController::class, 'update'])->name('questions.update');
Route::delete('/questions/{question}', [QuestionBankController::class, 'destroy'])->name('questions.destroy');
Route::get('/questions/{question}/preview', [QuestionBankController::class, 'preview'])->name('questions.preview');
Route::post('/questions/import', [QuestionBankController::class, 'import'])->name('questions.import');

/* ── CBT Exams ── */
Route::get('/cbt/exams', [CbtExamController::class, 'index'])->name('cbt.exams.index');
Route::get('/cbt/exams/create', [CbtExamController::class, 'create'])->name('cbt.exams.create');
Route::post('/cbt/exams', [CbtExamController::class, 'store'])->name('cbt.exams.store');
Route::get('/cbt/exams/{exam}', [CbtExamController::class, 'show'])->name('cbt.exams.show');
Route::get('/cbt/exams/{exam}/edit', [CbtExamController::class, 'edit'])->name('cbt.exams.edit');
Route::put('/cbt/exams/{exam}', [CbtExamController::class, 'update'])->name('cbt.exams.update');
Route::delete('/cbt/exams/{exam}', [CbtExamController::class, 'destroy'])->name('cbt.exams.destroy');
Route::post('/cbt/exams/{exam}/questions', [CbtExamController::class, 'attachQuestions'])->name('cbt.exams.questions.attach');
Route::delete('/cbt/exams/{exam}/questions/{question}', [CbtExamController::class, 'detachQuestion'])->name('cbt.exams.questions.detach');
Route::post('/cbt/exams/{exam}/auto-select', [CbtExamController::class, 'autoSelect'])->name('cbt.exams.auto-select');















/* ── Fee Categories ── */
Route::get('/fees/categories', [FeeCategoryController::class, 'index'])->name('fees.categories');
Route::post('/fees/categories', [FeeCategoryController::class, 'store'])->name('fees.categories.store');
Route::get('/fees/categories/{feeCategory}/edit', [FeeCategoryController::class, 'edit'])->name('fees.categories.edit');
Route::put('/fees/categories/{feeCategory}', [FeeCategoryController::class, 'update'])->name('fees.categories.update');
Route::delete('/fees/categories/{feeCategory}', [FeeCategoryController::class, 'destroy'])->name('fees.categories.destroy');

/* ── Fee Structure ── */
Route::get('/fees/structure', [FeeStructureController::class, 'index'])->name('fees.structure');
Route::post('/fees/structure', [FeeStructureController::class, 'bulkStore'])->name('fees.structure.store');
Route::post('/fees/structure/copy', [FeeStructureController::class, 'copyFromLastTerm'])->name('fees.structure.copy');
Route::delete('/fees/structure/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('fees.structure.destroy');

/* ── Fee Ledger & Scholarships ── */
Route::get('/fees/ledger', [FeeLedgerController::class, 'index'])->name('fees.ledger');
Route::get('/fees/ledger/{student}', [FeeLedgerController::class, 'student'])->name('fees.ledger.student');
Route::post('/fees/ledger/generate', [FeeLedgerController::class, 'generateForArm'])->name('fees.ledger.generate');
Route::post('/fees/ledger/{ledger}/discount', [FeeLedgerController::class, 'applyDiscount'])->name('fees.ledger.discount');
Route::delete('/fees/ledger/{ledger}/discount', [FeeLedgerController::class, 'removeDiscount'])->name('fees.ledger.discount.remove');





/* ── Fee Ledger ── */
Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
Route::get('/ledger/{student}', [LedgerController::class, 'show'])->name('ledger.show');
Route::post('/ledger/discount', [LedgerController::class, 'applyDiscount'])->name('ledger.discount');
Route::post('/ledger/discount/remove', [LedgerController::class, 'removeDiscount'])->name('ledger.discount.remove');
Route::post('/ledger/generate-all', [LedgerController::class, 'generateAll'])->name('ledger.generate.all');
Route::post('/ledger/generate-arm', [LedgerController::class, 'generateForArm'])->name('ledger.generate.arm');

// Teacher CRUD
Route::resource('teachers', TeacherController::class);

// Teacher Assignments

    // Teacher CRUD
    Route::resource('teachers', TeacherController::class);

    // Teacher Assignments
    Route::get('teacher-assignments', [TeacherAssignmentController::class, 'index'])->name('teachers.assignments');
    Route::post('teacher-assignments/{teacher}/update', [TeacherAssignmentController::class, 'updateAssignments'])->name('teachers.assignments.update');


});




