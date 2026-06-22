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
                'name'       => $child->user->full_name,
                'class'      => $child->currentEnrollment?->classArm?->full_name ?? 'N/A',
                'categories' => $categories,
            ];
        }

        return view('parent.fees.index', compact('parent', 'children', 'currentTerm', 'feesByChild'));
    }

    /* ── Checkout ── */
    public function checkout(): View
    {
        $parent = auth()->user();
        $children = $parent->children()->with(['user', 'currentEnrollment.classArm.classLevel'])->get();
        $currentTerm = Term::getCurrent();

        $payableItems = [];
        foreach ($children as $child) {
            $ledgers = StudentFeeLedger::with('feeStructure.feeCategory')
                ->where('student_id', $child->id)
                ->where('term_id', $currentTerm->id)
                ->where('status', '!=', 'Paid')
                ->get();

            foreach ($ledgers as $ledger) {
                $balance = $ledger->net_amount - $ledger->amount_paid;
                if ($balance <= 0) continue;

                $payableItems[] = [
                    'id'          => $ledger->id,
                    'child'       => $child->user->full_name,
                    'category'    => $ledger->feeStructure->feeCategory->name,
                    'description' => $ledger->feeStructure->feeCategory->description,
                    'amount'      => $balance,
                    'ledger_id'   => $ledger->id,
                ];
            }
        }

        $parentEmail = $parent->email ?? 'parent@school.edu';
        $paystackKey = config('services.paystack.public_key', 'pk_test_xxxxxxxx');

        return view('parent.fees.checkout', compact('parent', 'children', 'payableItems', 'parentEmail', 'paystackKey', 'currentTerm'));
    }

    /* ── Pay (initiate) ── */
    public function pay(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ledger_ids' => 'required|array|min:1',
            'ledger_ids.*' => 'exists:student_fee_ledger,id',
            'amount' => 'required|numeric|min:100',
        ]);

        $parent = auth()->user();
        $term = Term::getCurrent();

        // Verify ledgers belong to parent's children
        $ledger = StudentFeeLedger::with('student')->find($validated['ledger_ids'][0]);
        if (!$ledger || !$parent->children()->where('students.id', $ledger->student_id)->exists()) {
            Alert::error('Error', 'Unauthorized access.');
            return redirect()->route('parent.fees.index');
        }

        $student = Student::find($ledger->student_id);
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

            return redirect($data['authorization_url']);

        } catch (\Exception $e) {
            $payment->update(['status' => 'Failed']);
            Alert::error('Payment Error', $e->getMessage());
            return redirect()->route('parent.fees.index');
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
        $reference = $request->query('reference', 'NSM_' . strtoupper(Str::random(10)));
        $payment = Payment::with(['student.user', 'allocations.ledger.feeStructure.feeCategory'])
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

        return view('parent.fees.success', compact('reference', 'amountPaid', 'itemsPaid'));
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

        $totalSuccessful = collect($payments)->where('status', 'success')->sum('amount');
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

    private function sendPaymentSMS(Payment $payment): void
    {
        $school = \App\Models\SchoolProfile::first();
        if (!$school?->sms_on_payment) return;
        // TermiiService::send(...);
    }
}
