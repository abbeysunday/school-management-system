<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private string $baseUrl = 'https://api.paystack.co';
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Initialize a Paystack transaction
     */
    public function initializeTransaction(string $email, int $amountKobo, string $reference, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $email,
                'amount'    => $amountKobo,
                'reference' => $reference,
                'callback_url' => route('parent.fees.callback'),
                'metadata'  => $metadata,
            ]);

        if (!$response->successful()) {
            Log::error('Paystack initialize failed', [
                'response' => $response->json(),
                'reference' => $reference,
            ]);
            throw new \Exception('Payment initialization failed: ' . ($response->json()['message'] ?? 'Unknown error'));
        }

        return $response->json()['data'];
    }

    /**
     * Verify a Paystack transaction
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (!$response->successful()) {
            Log::error('Paystack verification failed', [
                'response' => $response->json(),
                'reference' => $reference,
            ]);
            throw new \Exception('Payment verification failed');
        }

        return $response->json()['data'];
    }

    /**
     * Verify Paystack webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }
}
