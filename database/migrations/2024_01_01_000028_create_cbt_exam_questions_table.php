<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('question_order')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['cbt_exam_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_questions');
    }
};
