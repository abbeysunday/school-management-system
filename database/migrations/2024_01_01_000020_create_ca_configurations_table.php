<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ca_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('component_name', 50);
            $table->decimal('max_score', 5, 2);
            $table->unsignedTinyInteger('order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ca_configurations');
    }
};
