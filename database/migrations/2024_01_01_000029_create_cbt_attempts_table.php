<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->decimal('score', 7, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->unsignedSmallInteger('total_answered')->default(0);
            $table->unsignedSmallInteger('total_correct')->default(0);
            $table->unsignedSmallInteger('total_wrong')->default(0);
            $table->unsignedSmallInteger('total_skipped')->default(0);
            $table->enum('status', ['In Progress','Submitted','Timed Out','Abandoned'])->default('In Progress');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['cbt_exam_id', 'student_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_attempts');
    }
};
