<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_arm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('timetable_periods')->cascadeOnDelete();
            $table->enum('day_of_week', ['Monday','Tuesday','Wednesday','Thursday','Friday']);
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('room', 50)->nullable();
            $table->timestamps();

            $table->unique(['class_arm_id', 'session_id', 'period_id', 'day_of_week']);
            $table->index(['teacher_id', 'session_id', 'period_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
