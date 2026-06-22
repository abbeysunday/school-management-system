<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paystack_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 50);
            $table->string('reference', 100)->nullable()->index();
            $table->json('payload');
            $table->string('signature', 200)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paystack_webhook_logs');
    }
};
