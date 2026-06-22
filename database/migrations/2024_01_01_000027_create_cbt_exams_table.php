<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_arm_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('exam_type', ['Practice', 'Formal', 'WAEC-Style'])->default('Formal');
            $table->unsignedSmallInteger('total_questions');
            $table->unsignedSmallInteger('duration_minutes');
            $table->decimal('total_marks', 6, 2);
            $table->decimal('marks_per_question', 5, 2)->default(1);
            $table->boolean('negative_marking')->default(false);
            $table->decimal('marks_deducted_per_wrong', 5, 2)->default(0);
            $table->boolean('randomize_questions')->default(true);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_result_immediately')->default(true);
            $table->boolean('allow_retake')->default(false);
            $table->unsignedTinyInteger('max_retakes')->default(0);
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['Draft','Scheduled','Active','Completed','Cancelled'])->default('Draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_exams');
    }
};
