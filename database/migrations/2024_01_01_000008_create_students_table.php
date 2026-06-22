<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('admission_number', 30)->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('religion', ['Christianity', 'Islam', 'Others'])->nullable();
            $table->string('state_of_origin', 50)->nullable();
            $table->string('lga', 50)->nullable();
            $table->string('home_address')->nullable();
            $table->enum('blood_group', ['A+','A-','B+','B-','O+','O-','AB+','AB-'])->nullable();
            $table->enum('genotype', ['AA','AS','SS','AC','SC'])->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('previous_school', 150)->nullable();
            $table->date('admission_date')->nullable();
            $table->enum('status', ['Active','Graduated','Withdrawn','Suspended','Transferred'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
