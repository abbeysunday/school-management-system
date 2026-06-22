<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_calendars', function (Blueprint $table) {
    $table->id();
    $table->string('title', 100);
    $table->date('date');
    $table->enum('type', ['public_holiday', 'mid_term_break', 'exam', 'event'])->default('event');
    $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
    $table->boolean('is_public_holiday')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};
