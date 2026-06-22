<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->year('start_year');
            $table->year('end_year');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_closed')->default(false); // ← added

            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_sessions');
    }
};
