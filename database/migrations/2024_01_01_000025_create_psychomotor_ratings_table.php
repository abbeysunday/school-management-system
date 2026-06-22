<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychomotor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->enum('domain', ['Psychomotor', 'Affective']);
            $table->string('trait_name', 100);
            $table->enum('rating', ['5', '4', '3', '2', '1']);
            $table->foreignId('rated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'term_id', 'domain', 'trait_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychomotor_ratings');
    }
};
