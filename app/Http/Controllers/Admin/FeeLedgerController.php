<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class FeeLedgerController extends Controller
{
    public function __construct(private FeeService $feeService) {}

    public function index(Request $request): View
{
    $termId = $request->input('term_id', Term::getCurrent()->id ?? null);
    $armId  = $request->input('class_arm_id');
    $search = $request->input('search');

    $sessions  = AcademicSession::orderByDesc('start_year')->pluck('name', 'id');
    $terms     = Term::with('session')->orderByDesc('start_date')->get();
    $classArms = ClassArm::with('classLevel')->get();

    $summaries = collect();

    if ($termId) {

        $studentsQuery = Student::with([
                'user',
                'currentEnrollment.classArm.classLevel'
            ])
            ->whereHas('feeLedger', function ($q) use ($termId) {
                $q->where('term_id', $termId);
            });

        if ($armId) {
            $studentsQuery->whereHas('enrollments', function ($q) use ($armId) {
                $q->where('class_arm_id', $armId)
                  ->where('is_active', true);
            });
        }

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // PAGINATE STUDENTS
        $summaries = $studentsQuery
            ->paginate(20)
            ->withQueryString()
            ->through(function (Student $student) use ($termId) {

                $ledger = StudentFeeLedger::where('student_id', $student->id)
                    ->where('term_id', $termId)
                    ->get();

                $totalDue  = $ledger->sum('net_amount');
                $totalPaid = $ledger->sum('amount_paid');

                return [
                    'student'    => $student,
                    'total_due'  => $totalDue,
                    'total_paid' => $totalPaid,
                    'balance'    => $totalDue - $totalPaid,
                    'has_unpaid' => $ledger->where('status', 'Unpaid')->isNotEmpty(),
                ];
            });
    }

    return view('admin.fees.ledger', compact(
        'sessions',
        'terms',
        'classArms',
        'termId',
        'armId',
        'search',
        'summaries'
    ));
}

    public function student(Request $request, Student $student): View
    {
        $termId = $request->input('term_id', Term::getCurrent()->id ?? null);
        $terms  = Term::with('session')->orderByDesc('start_date')->get();

        $ledger = [];
        $totals = ['due' => 0, 'discount' => 0, 'net' => 0, 'paid' => 0, 'balance' => 0];

        if ($termId) {
            $ledger = StudentFeeLedger::where('student_id', $student->id)
                ->where('term_id', $termId)
                ->with(['feeStructure.feeCategory', 'feeStructure.classLevel'])
                ->orderBy('id')
                ->get();

            $totals = [
                'due'      => $ledger->sum('original_amount'),
                'discount' => $ledger->sum('discount_amount'),
                'net'      => $ledger->sum('net_amount'),
                'paid'     => $ledger->sum('amount_paid'),
                'balance'  => $ledger->sum('net_amount') - $ledger->sum('amount_paid'),
            ];
        }

        $student->load(['user', 'currentEnrollment.classArm.classLevel']);

        return view('admin.fees.ledger_student', compact('student', 'terms', 'termId', 'ledger', 'totals'));
    }

    public function generateForArm(Request $request): RedirectResponse
    {
        $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'term_id'      => 'required|exists:terms,id',
        ]);

        $arm  = ClassArm::findOrFail($request->class_arm_id);
        $term = Term::findOrFail($request->term_id);

        $result = $this->feeService->generateLedgerForArm($arm, $term);

        Alert::success('Done', "{$result['created']} new ledger entries created, {$result['skipped']} skipped (already exist or no enrollment).");
        return redirect()->route('admin.fees.ledger', ['term_id' => $term->id, 'class_arm_id' => $arm->id]);
    }

    public function applyDiscount(Request $request, StudentFeeLedger $ledger): RedirectResponse
    {
        $request->validate([
            'discount_amount' => 'required|numeric|min:0',
            'discount_reason' => 'required|string|max:500',
        ]);

        $this->feeService->applyDiscount(
            $ledger->id,
            (float) $request->discount_amount,
            $request->discount_reason
        );

        Alert::success('Discount Applied', 'Fee discount has been recorded.');
        return redirect()->back();
    }

    public function removeDiscount(StudentFeeLedger $ledger): RedirectResponse
    {
        $this->feeService->removeDiscount($ledger->id);

        Alert::success('Removed', 'Discount removed from this fee item.');
        return redirect()->back();
    }
}
