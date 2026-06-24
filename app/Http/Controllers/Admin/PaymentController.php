<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\Payment;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Services\FeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentController extends Controller
{
    public function __construct(
        private FeeService $feeService,
    ) {}

    /* ── List all payments ──────────────────────────────── */
    public function index(Request $request)
    {
        $query = Payment::with(['student.user', 'student.currentEnrollment.classArm.classLevel', 'term', 'paidBy', 'verifiedBy'])
            ->latest('paid_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('class_arm_id')) {
            $query->whereHas('student.currentEnrollment', fn($q) => $q->where('class_arm_id', $request->class_arm_id)->where('is_active', true));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', fn($uq) => $uq->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
            });
        }

        $payments = $query->paginate(20)->withQueryString();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        $stats = [
            'total'      => Payment::count(),
            'verified'   => Payment::where('status', 'Verified')->count(),
            'pending'    => Payment::where('status', 'Pending')->count(),
            'failed'     => Payment::where('status', 'Failed')->count(),
            'today'      => Payment::whereDate('paid_at', today())->sum('amount'),
            'this_term'  => Payment::where('term_id', Term::getCurrent()?->id)->where('status', 'Verified')->sum('amount'),
        ];

        return view('admin.fees.payments.index', compact('payments', 'classArms', 'stats'));
    }

    /* ── Create manual payment form ─────────────────────── */
    public function create(Request $request)
    {
        $student = null;
        $ledgers = collect();
        $term = Term::getCurrent();

        if ($request->filled('student_id')) {
            $student = Student::with(['user', 'currentEnrollment.classArm.classLevel'])->find($request->student_id);
            if ($student && $term) {
                $ledgers = StudentFeeLedger::with('feeStructure.feeCategory')
                    ->where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->where('status', '!=', 'Paid')
                    ->whereRaw('net_amount - amount_paid > 0')
                    ->get()
                    ->map(fn($l) => [
                        'id'       => $l->id,
                        'category' => $l->feeStructure->feeCategory->name,
                        'balance'  => $l->net_amount - $l->amount_paid,
                        'original' => $l->net_amount,
                        'paid'     => $l->amount_paid,
                    ]);
            }
        }

        $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
            ->where('status', 'Active')
            ->orderBy('admission_number')
            ->get();

        $methods = ['Cash', 'Bank Transfer', 'Cheque', 'POS'];

        return view('admin.fees.payments.create', compact('student', 'ledgers', 'term', 'students', 'methods'));
    }

    /* ── Store manual payment ───────────────────────────── */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'ledger_ids'      => 'required|array|min:1',
            'ledger_ids.*'    => 'exists:student_fee_ledger,id',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'required|in:Cash,Bank Transfer,Cheque,POS',
            'paid_at'         => 'required|date',
            'notes'           => 'nullable|string|max:500',
        ]);

        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found. Please set a current term first.');
            return back()->withInput();
        }

        $student = Student::find($validated['student_id']);
        $bursar = auth()->user();

        $ledgers = StudentFeeLedger::whereIn('id', $validated['ledger_ids'])
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->get();

        if ($ledgers->count() !== count($validated['ledger_ids'])) {
            Alert::error('Error', 'Some selected fee items are invalid or do not belong to this student.');
            return back()->withInput();
        }

        $actualTotal = $ledgers->sum(fn($l) => $l->net_amount - $l->amount_paid);
        if (abs($actualTotal - $validated['amount']) > 1) {
            Alert::error('Error', 'Amount does not match the selected fee balances. Please recalculate.');
            return back()->withInput();
        }

        try {
            DB::beginTransaction();

            $reference = 'MAN-' . strtoupper(Str::random(12));
            $receiptNumber = $this->generateReceiptNumber();

            $payment = Payment::create([
                'student_id'         => $student->id,
                'term_id'            => $term->id,
                'payment_reference'  => $reference,
                'amount'             => $validated['amount'],
                'payment_method'     => $validated['payment_method'],
                'status'             => 'Verified',
                'paid_by_user_id'    => $bursar->id,
                'verified_by_user_id'=> $bursar->id,
                'receipt_number'     => $receiptNumber,
                'notes'              => $validated['notes'],
                'paid_at'            => $validated['paid_at'],
                'verified_at'        => now(),
            ]);

            $this->feeService->allocatePayment($payment);

            DB::commit();

            Alert::success('Payment Recorded', "₦" . number_format($payment->amount, 2) . " recorded for {$student->user->full_name}. Receipt: {$receiptNumber}");
            return redirect()->route('admin.payments.receipt', $payment->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Payment recording failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /* ── Show payment details ───────────────────────────── */
    public function show(Payment $payment)
    {
        $payment->load(['student.user', 'student.currentEnrollment.classArm.classLevel', 'term', 'allocations.ledger.feeStructure.feeCategory', 'paidBy', 'verifiedBy']);
        return view('admin.fees.payments.show', compact('payment'));
    }

    /* ── PDF Receipt ────────────────────────────────────── */
    public function receipt(Payment $payment)
    {
        $payment->load(['student.user', 'student.currentEnrollment.classArm.classLevel', 'term', 'allocations.ledger.feeStructure.feeCategory', 'paidBy', 'verifiedBy']);
        $school = SchoolProfile::first();

        $pdf = Pdf::loadView('pdf.fee-receipt', compact('payment', 'school'))
            ->setPaper('a4', 'portrait');

        // Sanitize filename — receipt numbers contain slashes (RCP/2024/0001)
        $safeFilename = str_replace(['/', '\\'], '_', $payment->receipt_number);

        return $pdf->stream("receipt-{$safeFilename}.pdf");
    }

    /* ── Receipt Number Generator ───────────────────────── */
    private function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "RCP/{$year}/";

        $latest = Payment::where('receipt_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $latest ? ((int) explode('/', $latest->receipt_number)[2]) + 1 : 1;

        do {
            $receiptNo = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Payment::where('receipt_number', $receiptNo)->exists());

        return $receiptNo;
    }
}
