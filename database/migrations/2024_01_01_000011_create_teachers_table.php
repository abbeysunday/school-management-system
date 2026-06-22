<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('staff_id', 30)->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('qualification', 150)->nullable();
            $table->string('specialization', 100)->nullable();
            $table->date('employment_date')->nullable();
            $table->enum('employment_type', ['Full-time','Part-time','Contract','NYSC'])->default('Full-time');
            $table->text('address')->nullable();
            $table->string('next_of_kin_name', 150)->nullable();
            $table->string('next_of_kin_phone', 20)->nullable();
            $table->string('next_of_kin_relationship', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
