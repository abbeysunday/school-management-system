<?php

namespace App\Jobs;

use App\Mail\FeeReminderMail;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public array $recipients, public int $sentByUserId) {}

    public function handle(): void
    {
        $total = count($this->recipients); $sent = 0; $failed = 0;
        foreach (array_chunk($this->recipients, 50) as $chunk) {
            foreach ($chunk as $recipient) {
                try {
                    Mail::to($recipient['email'])->queue(new FeeReminderMail($recipient['subject'], $recipient['body']));
                    EmailLog::create(['student_id' => $recipient['student_id'] ?? null, 'email' => $recipient['email'], 'subject' => $recipient['subject'], 'body' => $recipient['body'], 'status' => 'queued', 'sent_by_user_id' => $this->sentByUserId, 'sent_at' => now()]);
                    $sent++;
                } catch (\Exception $e) {
                    Log::error('Email send failed', ['email' => $recipient['email'], 'error' => $e->getMessage()]);
                    EmailLog::create(['student_id' => $recipient['student_id'] ?? null, 'email' => $recipient['email'], 'subject' => $recipient['subject'], 'body' => $recipient['body'], 'status' => 'failed', 'error_message' => $e->getMessage(), 'sent_by_user_id' => $this->sentByUserId, 'sent_at' => now()]);
                    $failed++;
                }
                usleep(100000);
            }
        }
        Log::info('Bulk email job completed', ['total' => $total, 'sent' => $sent, 'failed' => $failed]);
    }
}
