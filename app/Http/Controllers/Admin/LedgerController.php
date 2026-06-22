<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class LedgerController extends Controller
{
    public function __construct(private FeeService $feeService) {}

    public function index(Request $request): View
    {
        $termId = $request->input('term_id', Term::getCurrent()->id ?? null);
        $term = $termId ? Term::with('session')->find($termId) : null;

        $query = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
            ->active();

        if ($request->filled('class_arm_id')) {
            $query->whereHas('currentEnrollment', fn ($q) => $q->where('class_arm_id', $request->class_arm_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
            });
        }

        $students = $query->latest()->paginate(25)->withQueryString();

        // Attach fee summary to each student
        $students->getCollection()->transform(function ($student) use ($term) {
            $ledger = $student->feeLedger()->where('term_id', $term?->id)->get();
            $student->total_due = $ledger->sum('net_amount');
            $student->total_paid = $ledger->sum('amount_paid');
            $student->balance = $student->total_due - $student->total_paid;
            return $student;
        });

        $terms = Term::with('session')->orderByDesc('start_date')->get();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        return view('admin.ledger.index', compact('students', 'terms', 'term', 'classArms'));
    }

    public function show(Student $student, Request $request): View
    {
        $termId = $request->input('term_id', Term::getCurrent()->id ?? null);
        $term = $termId ? Term::with('session')->find($termId) : null;

        $ledger = StudentFeeLedger::with(['feeStructure.feeCategory', 'allocations.payment'])
            ->where('student_id', $student->id)
            ->where('term_id', $term?->id)
            ->get();

        $totalDue = $ledger->sum('net_amount');
        $totalPaid = $ledger->sum('amount_paid');
        $balance = $totalDue - $totalPaid;

        return view('admin.ledger.show', compact('student', 'term', 'ledger', 'totalDue', 'totalPaid', 'balance'));
    }

    public function applyDiscount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ledger_id'       => 'required|exists:student_fee_ledger,id',
            'discount_amount' => 'required|numeric|min:0.01',
            'discount_reason' => 'required|string|max:255',
        ]);

        try {
            $this->feeService->applyDiscount(
                $validated['ledger_id'],
                $validated['discount_amount'],
                $validated['discount_reason']
            );

            Alert::success('Success', 'Discount applied successfully.');
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
        }

        return redirect()->back();
    }

    public function removeDiscount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ledger_id' => 'required|exists:student_fee_ledger,id',
        ]);

        $this->feeService->removeDiscount($validated['ledger_id']);

        Alert::success('Success', 'Discount removed.');
        return redirect()->back();
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
        ]);

        $term = Term::findOrFail($validated['term_id']);
        $count = $this->feeService->generateLedgerForAllStudents($term);

        Alert::success('Generated', "Fee ledger generated for {$count} students.");
        return redirect()->route('admin.ledger.index', ['term_id' => $term->id]);
    }

    public function generateForArm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'term_id'      => 'required|exists:terms,id',
        ]);

        $arm = ClassArm::findOrFail($validated['class_arm_id']);
        $term = Term::findOrFail($validated['term_id']);
        $count = $this->feeService->generateLedgerForArm($arm, $term);

        Alert::success('Generated', "{$count} new ledger entries created.");
        return redirect()->route('admin.ledger.index', ['term_id' => $term->id]);
    }
}
