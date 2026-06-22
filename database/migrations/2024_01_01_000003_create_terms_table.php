<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->enum('name', ['First Term', 'Second Term', 'Third Term']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('mid_term_break_start')->nullable();
            $table->date('mid_term_break_end')->nullable();
            $table->date('next_resumption_date')->nullable();
            $table->unsignedSmallInteger('total_school_days')->default(0);
            $table->boolean('is_current')->default(false);
            $table->boolean('results_published')->default(false);
            $table->timestamps();

            $table->unique(['session_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
