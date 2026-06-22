<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('from_class_arm_id')->constrained('class_arms');
            $table->foreignId('to_class_arm_id')->nullable()->constrained('class_arms')->nullOnDelete();
            $table->enum('promotion_status', ['Promoted','Repeated','Graduated','Withdrawn']);
            $table->enum('decision_type', ['Auto','Manual Override'])->default('Auto');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_reason')->nullable();
            $table->timestamp('promoted_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_records');
    }
};
