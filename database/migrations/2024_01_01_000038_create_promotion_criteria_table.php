<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_percentage', 5, 2)->default(40.00);
            $table->unsignedTinyInteger('min_subjects_passed')->default(5);
            $table->decimal('min_ca', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique('class_level_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_criterias');
    }
};
