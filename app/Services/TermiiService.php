<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TermiiService
{
    private string $baseUrl = 'https://api.ng.termii.com/api';
    private string $apiKey;
    private string $senderId;

    public function __construct()
    {
        $this->apiKey = config('services.termii.api_key', '');
        $this->senderId = config('services.termii.sender_id', 'School');
    }

    /**
     * Send SMS via Termii
     */
    public function sendSms(string $phone, string $message, string $purpose = 'general'): array
    {
        if (empty($this->apiKey)) {
            Log::warning('Termii API key not configured');
            return ['status' => 'skipped', 'message' => 'API key not configured', 'cost' => 0];
        }

        // Format phone (ensure +234 or 234 prefix)
        $phone = $this->formatPhone($phone);

        $response = Http::post("{$this->baseUrl}/sms/send", [
            'api_key'     => $this->apiKey,
            'to'          => $phone,
            'from'        => $this->senderId,
            'sms'         => $message,
            'type'        => 'plain',
            'channel'     => 'generic',
        ]);

        if (!$response->successful()) {
            Log::error('Termii SMS failed', [
                'phone' => $phone,
                'response' => $response->body(),
            ]);
            throw new \Exception('SMS sending failed: ' . $response->body());
        }

        $data = $response->json();

        Log::info('Termii SMS sent', [
            'phone' => $phone,
            'message_id' => $data['message_id'] ?? null,
        ]);

        return [
            'status'     => 'sent',
            'message_id' => $data['message_id'] ?? null,
            'cost'       => $data['cost'] ?? 0,
            'response'   => $data,
        ];
    }

    /**
     * Check SMS balance
     */
    public function checkBalance(): array
    {
        if (empty($this->apiKey)) {
            return ['status' => 'error', 'balance' => 0, 'currency' => 'NGN'];
        }

        $response = Http::get("{$this->baseUrl}/get-balance", [
            'api_key' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return ['status' => 'error', 'balance' => 0, 'currency' => 'NGN'];
        }

        $data = $response->json();

        return [
            'status'   => 'ok',
            'balance'  => $data['balance'] ?? 0,
            'currency' => $data['currency'] ?? 'NGN',
        ];
    }

    /**
     * Update delivery status for a message
     */
    public function updateDeliveryStatus(string $messageId): array
    {
        if (empty($this->apiKey)) {
            return ['status' => 'error', 'message' => 'API key not configured'];
        }

        $response = Http::get("{$this->baseUrl}/sms/message-id", [
            'api_key'     => $this->apiKey,
            'message_id'  => $messageId,
        ]);

        if (!$response->successful()) {
            return ['status' => 'error', 'message' => 'Failed to fetch status'];
        }

        $data = $response->json();

        return [
            'status'       => $data['status'] ?? 'unknown',
            'message_id'   => $messageId,
            'delivery_status' => $data['delivery_status'] ?? 'unknown',
            'response'     => $data,
        ];
    }

    /**
     * Format Nigerian phone numbers
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}
