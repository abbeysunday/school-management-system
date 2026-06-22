<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaystackWebhookLog;
use App\Services\FeeService;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        private PaystackService $paystack,
        private FeeService $feeService,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Paystack-Signature');
        $event     = $request->input('event');
        $data      = $request->input('data', []);

        // 1. Log FIRST (before any processing)
        $log = PaystackWebhookLog::create([
            'event'     => $event,
            'reference' => $data['reference'] ?? null,
            'payload'   => $request->all(),
            'signature' => $signature,
            'ip_address'=> $request->ip(),
            'processed' => false,
        ]);

        // 2. Verify signature
        if (!$this->paystack->verifyWebhookSignature($payload, $signature ?? '')) {
            $log->update(['error' => 'Invalid signature', 'processed_at' => now()]);
            Log::warning('Paystack webhook: invalid signature', ['reference' => $data['reference'] ?? null]);
            return response()->json(['status' => 'rejected'], 400);
        }

        // 3. Only process charge.success
        if ($event !== 'charge.success') {
            $log->update(['processed' => true, 'processed_at' => now()]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $reference = $data['reference'] ?? null;
        if (!$reference) {
            $log->update(['error' => 'No reference in payload', 'processed_at' => now()]);
            return response()->json(['status' => 'error'], 400);
        }

        // 4. Find payment by reference
        $payment = Payment::where('payment_reference', $reference)
            ->orWhere('paystack_reference', $reference)
            ->first();

        if (!$payment) {
            $log->update(['error' => 'Payment not found: ' . $reference, 'processed_at' => now()]);
            Log::warning('Paystack webhook: payment not found', ['reference' => $reference]);
            return response()->json(['status' => 'not_found'], 404);
        }

        // 5. Idempotency check
        if ($payment->status === 'Verified') {
            $log->update(['processed' => true, 'processed_at' => now(), 'error' => 'Already processed']);
            return response()->json(['status' => 'already_processed'], 200);
        }

        // 6. Verify amount matches
        $amountNaira = $data['amount'] / 100;
        if (abs($amountNaira - $payment->amount) > 0.01) {
            $log->update(['error' => "Amount mismatch: expected {$payment->amount}, got {$amountNaira}", 'processed_at' => now()]);
            Log::warning('Paystack webhook: amount mismatch', [
                'reference' => $reference,
                'expected'  => $payment->amount,
                'received'  => $amountNaira,
            ]);
            return response()->json(['status' => 'amount_mismatch'], 400);
        }

        // 7. Process payment
        try {
            $payment->update([
                'status'             => 'Verified',
                'paystack_reference' => $data['reference'],
                'paid_at'            => now(),
                'verified_at'        => now(),
                'receipt_number'     => 'RCP-' . strtoupper(\Illuminate\Support\Str::random(8)),
            ]);

            $this->feeService->allocatePayment($payment);

            // Trigger SMS
            $school = \App\Models\SchoolProfile::first();
            if ($school?->sms_on_payment) {
                // Termii SMS here
            }

            $log->update(['processed' => true, 'processed_at' => now()]);

            Log::info('Paystack webhook processed', [
                'reference' => $reference,
                'payment_id' => $payment->id,
                'amount'    => $amountNaira,
            ]);

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            $log->update(['error' => $e->getMessage(), 'processed_at' => now()]);
            Log::error('Paystack webhook processing failed', [
                'reference' => $reference,
                'error'     => $e->getMessage(),
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
