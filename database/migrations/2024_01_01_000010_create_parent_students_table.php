<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('relationship', ['Father','Mother','Guardian','Uncle','Aunt','Sibling','Others'])->default('Guardian');
            $table->boolean('is_primary_contact')->default(true);
            $table->timestamps();

            $table->unique(['parent_user_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_students');
    }
};
