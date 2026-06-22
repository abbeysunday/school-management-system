<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->string('arm', 15);
            $table->unsignedTinyInteger('capacity')->default(40);
            $table->timestamps();

            $table->unique(['class_level_id', 'arm']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_arms');
    }
};
