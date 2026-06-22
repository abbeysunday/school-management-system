<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arm_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_arm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['class_arm_id', 'subject_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arm_subjects');
    }
};
