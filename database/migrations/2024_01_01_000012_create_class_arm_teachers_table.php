<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_arm_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_arm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->enum('role', ['Form Teacher', 'Co-Form Teacher'])->default('Form Teacher');
            $table->timestamps();

            $table->unique(['class_arm_id', 'session_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_arm_teachers');
    }
};
