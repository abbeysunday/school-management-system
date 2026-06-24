<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\TermiiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $recipients,
        public string $template,
        public int $sentByUserId,
    ) {}

    public function handle(TermiiService $termii): void
    {
        $total = count($this->recipients);
        $sent = 0;
        $failed = 0;

        foreach (array_chunk($this->recipients, 100) as $chunk) {
            foreach ($chunk as $recipient) {
                try {
                    $response = $termii->sendSms(
                        phone: $recipient['phone'],
                        message: $recipient['message'],
                        purpose: 'fee_reminder'
                    );

                    SmsLog::create([
                        'sent_by'            => $this->sentByUserId,
                        'recipient_phone'    => $recipient['phone'],
                        'recipient_user_id'  => $recipient['user_id'] ?? null,
                        'message'            => $recipient['message'],
                        'termii_message_id'  => $response['message_id'] ?? null,
                        'status'             => $response['status'] === 'sent' ? 'Sent' : 'Pending',
                        'purpose'            => 'fee_reminder',
                        'cost'               => $response['cost'] ?? 0,
                        'sent_at'            => now(),
                    ]);

                    $sent++;

                } catch (\Exception $e) {
                    Log::error('SMS send failed', [
                        'phone' => $recipient['phone'],
                        'error' => $e->getMessage(),
                    ]);

                    SmsLog::create([
                        'sent_by'            => $this->sentByUserId,
                        'recipient_phone'    => $recipient['phone'],
                        'recipient_user_id'  => $recipient['user_id'] ?? null,
                        'message'            => $recipient['message'],
                        'status'             => 'Failed',
                        'purpose'            => 'fee_reminder',
                        'sent_at'            => now(),
                    ]);

                    $failed++;
                }

                // Rate limit: max 5 SMS/second
                usleep(200000); // 200ms
            }
        }

        Log::info('Bulk SMS job completed', [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }
}
