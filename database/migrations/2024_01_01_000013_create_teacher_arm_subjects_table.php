<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_arm_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('arm_subject_id')->constrained('arm_subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'arm_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_arm_subjects');
    }
};
