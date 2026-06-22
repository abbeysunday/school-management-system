<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_profile', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('stamp')->nullable();
            $table->string('motto')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('principal_name', 150)->nullable();
            $table->string('waec_centre_number', 30)->nullable();
            $table->string('neco_centre_number', 30)->nullable();
            $table->string('rc_number', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('lga', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedTinyInteger('ca_weight')->default(30);
            $table->unsignedTinyInteger('exam_weight')->default(70);
            $table->string('currency_symbol', 5)->default('₦');
            $table->string('timezone')->default('Africa/Lagos');
            $table->string('paystack_public_key')->nullable();
            $table->string('paystack_secret_key')->nullable();
            $table->string('termii_api_key')->nullable();
            $table->string('termii_sender_id', 20)->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->boolean('sms_on_absence')->default(true);
            $table->boolean('sms_on_payment')->default(true);
            $table->boolean('sms_on_result_publish')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_profile');
    }
};
