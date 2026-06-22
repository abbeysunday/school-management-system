<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_phone', 20);
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->string('termii_message_id', 100)->nullable();
            $table->enum('status', ['Pending','Sent','Delivered','Failed','Rejected'])->default('Pending');
            $table->string('purpose', 100)->nullable();
            $table->decimal('cost', 8, 4)->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
