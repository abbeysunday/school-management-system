<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 30);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('period_order');
            $table->enum('period_type', ['Teaching','Break','Assembly','Games','Closing'])->default('Teaching');
            $table->timestamps();

            $table->unique('period_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_periods');
    }
};
