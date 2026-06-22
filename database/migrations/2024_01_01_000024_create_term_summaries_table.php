<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_arm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_obtainable', 7, 2)->nullable();
            $table->decimal('total_obtained', 7, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedSmallInteger('arm_position')->nullable();
            $table->unsignedSmallInteger('class_position')->nullable();
            $table->unsignedTinyInteger('no_of_subjects')->default(0);
            $table->unsignedTinyInteger('no_passed')->default(0);
            $table->unsignedTinyInteger('no_failed')->default(0);
            $table->unsignedSmallInteger('days_present')->default(0);
            $table->unsignedSmallInteger('days_absent')->default(0);
            $table->unsignedSmallInteger('total_school_days')->default(0);
            $table->text('form_teacher_remark')->nullable();
            $table->text('principal_remark')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_summaries');
    }
};
