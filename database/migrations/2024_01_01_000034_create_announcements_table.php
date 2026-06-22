<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->longText('body');
            $table->enum('audience', ['All','Parents','Teachers','Students','Specific Class','Specific Student'])->default('All');
            $table->foreignId('class_arm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('target_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->enum('priority', ['Normal','Important','Emergency'])->default('Normal');
            $table->string('attachment', 255)->nullable();
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_email')->default(false);
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
