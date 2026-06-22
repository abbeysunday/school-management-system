<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\PaystackWebhookController;

Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])
    ->name('webhooks.paystack');
