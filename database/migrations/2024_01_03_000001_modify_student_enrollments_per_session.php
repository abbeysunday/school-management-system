<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Change enrollment model from per-term to per-session.
 *
 * A student stays in the same class arm for the entire session (all 3 terms).
 * Only fees are per-term. This migration:
 *   1. Drops the old UNIQUE(student_id, term_id) constraint
 *   2. Drops the foreign key on term_id
 *   3. Makes term_id nullable (no longer required on enrollment rows)
 *   4. Adds a new UNIQUE(student_id, session_id) constraint
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            // 1. Drop old FK and unique that reference term_id
            $table->dropForeign(['term_id']);
            $table->dropUnique('student_enrollments_student_id_term_id_unique');

            // 2. Make term_id nullable — enrollment is now per-session, not per-term
            $table->unsignedBigInteger('term_id')->nullable()->change();

            // 3. Re-add the FK as nullable (nullOnDelete so orphaned rows survive)
            $table->foreign('term_id')->references('id')->on('terms')->nullOnDelete();

            // 4. New unique constraint: one class assignment per student per session
            $table->unique(['student_id', 'session_id'], 'student_enrollments_student_session_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropUnique('student_enrollments_student_session_unique');

            $table->unsignedBigInteger('term_id')->nullable(false)->change();
            $table->foreign('term_id')->references('id')->on('terms')->cascadeOnDelete();

            $table->unique(['student_id', 'term_id'], 'student_enrollments_student_id_term_id_unique');
        });
    }
};
