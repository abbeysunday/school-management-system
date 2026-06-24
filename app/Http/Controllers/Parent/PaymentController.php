<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Services\FeeService;
use App\Services\PaystackService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentController extends Controller
{
    public function __construct(
        private PaystackService $paystack,
        private FeeService $feeService,
    ) {}

    /* ── Fee Overview ── */
    public function fees(): View
    {
        $parent = auth()->user();
        $children = $parent->children()->with(['user', 'currentEnrollment.classArm.classLevel'])->get();
        $currentTerm = Term::getCurrent();

        $feesByChild = [];
        foreach ($children as $child) {
            $ledgers = StudentFeeLedger::with('feeStructure.feeCategory')
                ->where('student_id', $child->id)
                ->where('term_id', $currentTerm->id)
                ->get();

            $categories = $ledgers->map(fn ($l) => [
                'id'          => $l->id,
                'category'    => $l->feeStructure->feeCategory->name,
                'description' => $l->feeStructure->feeCategory->description,
                'amount'      => (float) $l->net_amount,
                'paid'        => (float) $l->amount_paid,
                'due_date'    => $l->feeStructure->due_date?->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d'),
            ])->toArray();

            $feesByChild[$child->id] = [
                'id'         => $child->id,
                'name'       => $child->user->full_name,
                'first_name' => $child->user->first_name,
                'class'      => $child->currentEnrollment?->classArm?->full_name ?? 'N/A',
                'categories' => $categories,
            ];
        }

        return view('parent.fees.index', compact('parent', 'children', 'currentTerm', 'feesByChild'));
    }

    /* ── Checkout ── */
    public function checkout(Request $request): View
    {
        $parent = auth()->user();
        $children = $parent->children()->with(['user', 'currentEnrollment.classArm.classLevel'])->get();
        $currentTerm = Term::getCurrent();
        $selectedChildId = $request->query('child');

        $payableByChild = [];
        $payableItems = [];

        foreach ($children as $child) {
            $ledgers = StudentFeeLedger::with('feeStructure.feeCategory')
                ->where('student_id', $child->id)
                ->where('term_id', $currentTerm->id)
                ->where('status', '!=', 'Paid')
                ->get();

            $childItems = [];
            foreach ($ledgers as $ledger) {
                $balance = $ledger->net_amount - $ledger->amount_paid;
                if ($balance <= 0) continue;

                $item = [
                    'id'          => $ledger->id,
                    'child_id'    => $child->id,
                    'child'       => $child->user->full_name,
                    'child_first' => $child->user->first_name,
                    'category'    => $ledger->feeStructure->feeCategory->name,
                    'description' => $ledger->feeStructure->feeCategory->description,
                    'amount'      => $balance,
                    'ledger_id'   => $ledger->id,
                ];

                $childItems[] = $item;
                $payableItems[] = $item;
            }

            $payableByChild[$child->id] = [
                'id'         => $child->id,
                'name'       => $child->user->full_name,
                'first_name' => $child->user->first_name,
                'class'      => $child->currentEnrollment?->classArm?->full_name ?? 'N/A',
                'items'      => $childItems,
                'subtotal'   => collect($childItems)->sum('amount'),
            ];
        }

        $parentEmail = $parent->email ?? 'parent@school.edu';
        $paystackKey = config('services.paystack.public_key', 'pk_test_xxxxxxxx');

        return view('parent.fees.checkout', compact(
            'parent', 'children', 'payableByChild', 'payableItems',
            'selectedChildId', 'parentEmail', 'paystackKey', 'currentTerm'
        ));
    }

    /* ── Pay (initiate) ── */
    public function pay(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'ledger_ids' => 'required|array|min:1',
            'ledger_ids.*' => 'exists:student_fee_ledger,id',
            'amount' => 'required|numeric|min:100',
        ]);

        $parent = auth()->user();
        $term = Term::getCurrent();

        // Fetch all ledgers and verify they exist
        $ledgers = StudentFeeLedger::with('student.user')
            ->whereIn('id', $validated['ledger_ids'])
            ->get();

        if ($ledgers->count() !== count($validated['ledger_ids'])) {
            return $this->paymentError('Some fee items were not found. Please refresh and try again.');
        }

        // Verify ALL ledgers belong to parent's children
        $childIds = $parent->children()->pluck('students.id')->toArray();
        foreach ($ledgers as $ledger) {
            if (!in_array($ledger->student_id, $childIds)) {
                return $this->paymentError('Unauthorized access to fee items.');
            }
        }

        // All ledgers must belong to the same student (Payment model has single student_id)
        $studentIds = $ledgers->pluck('student_id')->unique();
        if ($studentIds->count() > 1) {
            return $this->paymentError('Please pay for one child at a time. Select fees for a single student.');
        }

        $student = $ledgers->first()->student;

        // Verify amount matches actual sum of balances
        $actualTotal = $ledgers->sum(fn($l) => $l->net_amount - $l->amount_paid);
        if (abs($actualTotal - $validated['amount']) > 1) {
            return $this->paymentError('Amount mismatch detected. Please refresh and try again.');
        }

        $reference = 'PAY-' . Str::uuid();

        $payment = Payment::create([
            'student_id'        => $student->id,
            'term_id'           => $term->id,
            'payment_reference' => $reference,
            'amount'            => $validated['amount'],
            'payment_method'    => 'Paystack',
            'status'            => 'Pending',
            'paid_by_user_id'   => $parent->id,
        ]);

        try {
            $data = $this->paystack->initializeTransaction(
                email: $parent->email ?? $student->user->email ?? 'parent@school.edu',
                amountKobo: (int) ($validated['amount'] * 100),
                reference: $reference,
                metadata: [
                    'student_id'   => $student->id,
                    'term_id'      => $term->id,
                    'payment_id'   => $payment->id,
                    'parent_name'  => $parent->full_name,
                    'student_name' => $student->user->full_name,
                    'ledger_ids'   => $validated['ledger_ids'],
                ]
            );

            // If AJAX/JSON request, return inline JS data
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'reference' => $reference,
                        'amount' => (int) ($validated['amount'] * 100),
                        'amount_ngn' => $validated['amount'],
                        'email' => $parent->email ?? $student->user->email ?? 'parent@school.edu',
                        'public_key' => config('services.paystack.public_key'),
                        'authorization_url' => $data['authorization_url'],
                    ]
                ]);
            }

            // Fallback: server-side redirect
            return redirect($data['authorization_url']);

        } catch (\Exception $e) {
            $payment->update(['status' => 'Failed']);
            return $this->paymentError('Payment initialization failed: ' . $e->getMessage());
        }
    }

    /* ── Callback ── */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (!$reference) {
            Alert::error('Error', 'No reference provided.');
            return redirect()->route('parent.fees.index');
        }

        $payment = Payment::where('payment_reference', $reference)->first();

        if (!$payment) {
            Alert::error('Error', 'Payment record not found.');
            return redirect()->route('parent.fees.index');
        }

        if ($payment->status === 'Verified') {
            Alert::success('Already Paid', 'This payment was already verified.');
            return redirect()->route('parent.fees.success', ['reference' => $reference]);
        }

        try {
            $data = $this->paystack->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $payment->update([
                    'status'             => 'Verified',
                    'paystack_reference' => $data['reference'],
                    'paid_at'            => now(),
                    'verified_at'        => now(),
                    'receipt_number'     => 'RCP-' . strtoupper(Str::random(8)),
                ]);

                $this->feeService->allocatePayment($payment);
                $this->sendPaymentSMS($payment);

                Alert::success('Payment Successful', '₦' . number_format($payment->amount, 2) . ' paid. Receipt: ' . $payment->receipt_number);
            } else {
                $payment->update(['status' => 'Failed']);
                Alert::error('Payment Failed', 'Transaction was not successful.');
                return redirect()->route('parent.fees.index');
            }

        } catch (\Exception $e) {
            \Log::error('Payment callback error', ['reference' => $reference, 'error' => $e->getMessage()]);
            Alert::error('Error', 'Could not verify payment. Contact support.');
            return redirect()->route('parent.fees.index');
        }

        return redirect()->route('parent.fees.success', ['reference' => $reference]);
    }

    /* ── Success ── */
    public function success(Request $request): View
    {
        $reference = $request->query('reference');

        if (!$reference) {
            Alert::error('Error', 'No payment reference found.');
            return view('parent.fees.success', [
                'reference' => 'N/A',
                'amountPaid' => 0,
                'itemsPaid' => [],
                'payment' => null,
            ]);
        }

        $payment = Payment::with(['student.user', 'allocations.ledger.feeStructure.feeCategory', 'paidBy', 'term'])
            ->where('payment_reference', $reference)
            ->first();

        $amountPaid = $payment?->amount ?? 0;
        $itemsPaid = [];

        if ($payment) {
            foreach ($payment->allocations as $alloc) {
                $itemsPaid[] = [
                    'category' => $alloc->ledger->feeStructure->feeCategory->name,
                    'child'    => $payment->student->user->full_name,
                    'amount'   => (float) $alloc->amount_allocated,
                ];
            }
        }

        return view('parent.fees.success', compact('reference', 'amountPaid', 'itemsPaid', 'payment'));
    }

    /* ── History ── */
    public function history(): View
    {
        $parent = auth()->user();
        $payments = Payment::with(['student.user', 'term'])
            ->whereHas('student', fn ($q) => $q->whereIn('id', $parent->children()->pluck('students.id')))
            ->latest('paid_at')
            ->get()
            ->map(fn ($p) => [
                'ref'     => $p->payment_reference,
                'date'    => $p->paid_at?->format('Y-m-d') ?? $p->created_at->format('Y-m-d'),
                'child'   => $p->student->user->full_name,
                'items'   => $p->allocations->map(fn ($a) => $a->ledger->feeStructure->feeCategory->name)->implode(' + ') ?: 'General Payment',
                'amount'  => (float) $p->amount,
                'status'  => strtolower($p->status),
                'channel' => $p->payment_method,
            ])->toArray();

        $totalSuccessful = collect($payments)->where('status', 'verified')->sum('amount');
        $totalFailed = collect($payments)->where('status', 'failed')->count();

        return view('parent.fees.history', compact('payments', 'totalSuccessful', 'totalFailed'));
    }

    /* ── Receipt PDF ── */
    public function receipt(string $ref)
    {
        $payment = Payment::with(['student.user', 'term', 'allocations.ledger.feeStructure.feeCategory', 'paidBy'])
            ->where('payment_reference', $ref)
            ->where('status', 'Verified')
            ->firstOrFail();

        $school = \App\Models\SchoolProfile::first();

        $pdf = Pdf::loadView('pdf.payment-receipt', compact('payment', 'school'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("receipt-{$ref}.pdf");
    }

    /* ── Helpers ── */

    private function paymentError(string $message): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], 422);
    }

    private function sendPaymentSMS(Payment $payment): void
    {
        $school = \App\Models\SchoolProfile::first();
        if (!$school?->sms_on_payment) return;
        // TermiiService::send(...);
    }
}
