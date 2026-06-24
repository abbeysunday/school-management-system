<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\DefaultersExport;
use App\Models\ClassArm;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class FeeReportController extends Controller
{
    public function defaulters(Request $request)
    {
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return redirect()->route('admin.dashboard');
        }

        $classArmId = $request->query('class_arm_id');
        $minBalance = $request->query('min_balance', 1);

        $query = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
            ->where('status', 'Active')
            ->whereHas('feeLedger', function ($q) use ($term, $minBalance) {
                $q->where('term_id', $term->id)
                  ->whereRaw('net_amount - amount_paid >= ?', [$minBalance]);
            });

        if ($classArmId) {
            $query->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $classArmId)->where('is_active', true));
        }

        $students = $query->orderBy('admission_number')->get();

        $defaulters = [];
        $grandTotal = 0; $grandPaid = 0; $grandOwed = 0;

        foreach ($students as $student) {
            $ledgers = StudentFeeLedger::with('feeStructure.feeCategory')
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->get();

            $totalDue = $ledgers->sum('net_amount');
            $totalPaid = $ledgers->sum('amount_paid');
            $balance = $totalDue - $totalPaid;

            if ($balance < $minBalance) continue;

            $lastPayment = $student->payments()
                ->where('status', 'Verified')
                ->latest('paid_at')
                ->first();

            $defaulters[] = [
                'id'              => $student->id,
                'name'            => $student->user->full_name,
                'admission_no'    => $student->admission_number,
                'class'           => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                'class_arm_id'    => $student->currentEnrollment?->class_arm_id,
                'phone'           => $student->user->phone,
                'email'           => $student->user->email,
                'parent_phone'    => $student->primaryParent()?->phone,
                'parent_email'    => $student->primaryParent()?->email,
                'total_due'       => $totalDue,
                'total_paid'      => $totalPaid,
                'balance'         => $balance,
                'last_payment'    => $lastPayment?->paid_at?->format('d M Y') ?? 'Never',
                'last_payment_amt'=> $lastPayment?->amount ?? 0,
            ];

            $grandTotal += $totalDue;
            $grandPaid += $totalPaid;
            $grandOwed += $balance;
        }

        $byClassArm = collect($defaulters)->groupBy('class');
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        $summary = [
            'total_students' => count($defaulters),
            'total_due'      => $grandTotal,
            'total_paid'     => $grandPaid,
            'total_owed'     => $grandOwed,
        ];

        return view('admin.fees.reports.defaulters', compact(
            'defaulters', 'byClassArm', 'classArms', 'term', 'summary', 'classArmId', 'minBalance'
        ));
    }

    public function exportDefaulters(Request $request)
    {
        $term = Term::getCurrent();
        $classArmId = $request->query('class_arm_id');
        $minBalance = $request->query('min_balance', 1);
        $filename = 'defaulters_' . ($term?->name ?? 'term') . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new DefaultersExport($term, $classArmId, $minBalance), $filename);
    }

    public function financialSummary(Request $request)
    {
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return redirect()->route('admin.dashboard');
        }

        $overall = DB::table('student_fee_ledger')
            ->where('term_id', $term->id)
            ->selectRaw('SUM(net_amount) as total_billed, SUM(amount_paid) as total_collected, SUM(net_amount - amount_paid) as total_outstanding, COUNT(*) as total_items, SUM(CASE WHEN status = "Paid" THEN 1 ELSE 0 END) as paid_count, SUM(CASE WHEN status = "Partial" THEN 1 ELSE 0 END) as partial_count, SUM(CASE WHEN status = "Unpaid" THEN 1 ELSE 0 END) as unpaid_count')
            ->first();

        $byCategory = DB::table('student_fee_ledger')
            ->join('fee_structures', 'student_fee_ledger.fee_structure_id', '=', 'fee_structures.id')
            ->join('fee_categories', 'fee_structures.fee_category_id', '=', 'fee_categories.id')
            ->where('student_fee_ledger.term_id', $term->id)
            ->select('fee_categories.name as category', 'fee_categories.is_compulsory', DB::raw('SUM(student_fee_ledger.net_amount) as total_billed'), DB::raw('SUM(student_fee_ledger.amount_paid) as total_collected'), DB::raw('SUM(student_fee_ledger.net_amount - student_fee_ledger.amount_paid) as total_outstanding'), DB::raw('COUNT(*) as total_items'))
            ->groupBy('fee_categories.id', 'fee_categories.name', 'fee_categories.is_compulsory')
            ->orderBy('fee_categories.display_order')
            ->get();

        $byClassLevel = DB::table('student_fee_ledger')
            ->join('students', 'student_fee_ledger.student_id', '=', 'students.id')
            ->join('student_enrollments', function ($join) { $join->on('students.id', '=', 'student_enrollments.student_id')->where('student_enrollments.is_active', true); })
            ->join('class_arms', 'student_enrollments.class_arm_id', '=', 'class_arms.id')
            ->join('class_levels', 'class_arms.class_level_id', '=', 'class_levels.id')
            ->where('student_fee_ledger.term_id', $term->id)
            ->select('class_levels.name as class_level', 'class_levels.category', DB::raw('SUM(student_fee_ledger.net_amount) as total_billed'), DB::raw('SUM(student_fee_ledger.amount_paid) as total_collected'), DB::raw('SUM(student_fee_ledger.net_amount - student_fee_ledger.amount_paid) as total_outstanding'), DB::raw('COUNT(DISTINCT students.id) as student_count'))
            ->groupBy('class_levels.id', 'class_levels.name', 'class_levels.category')
            ->orderBy('class_levels.level_order')
            ->get();

        $byClassArm = DB::table('student_fee_ledger')
            ->join('students', 'student_fee_ledger.student_id', '=', 'students.id')
            ->join('student_enrollments', function ($join) { $join->on('students.id', '=', 'student_enrollments.student_id')->where('student_enrollments.is_active', true); })
            ->join('class_arms', 'student_enrollments.class_arm_id', '=', 'class_arms.id')
            ->join('class_levels', 'class_arms.class_level_id', '=', 'class_levels.id')
            ->where('student_fee_ledger.term_id', $term->id)
            ->select(DB::raw("CONCAT(class_levels.name, class_arms.arm) as class_arm"), DB::raw('SUM(student_fee_ledger.net_amount) as total_billed'), DB::raw('SUM(student_fee_ledger.amount_paid) as total_collected'), DB::raw('SUM(student_fee_ledger.net_amount - student_fee_ledger.amount_paid) as total_outstanding'), DB::raw('COUNT(DISTINCT students.id) as student_count'))
            ->groupBy('class_arms.id', 'class_levels.name', 'class_arms.arm')
            ->orderBy('class_levels.level_order')
            ->orderBy('class_arms.arm')
            ->get();

        $byMethod = DB::table('payments')
            ->where('term_id', $term->id)
            ->where('status', 'Verified')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $dailyTrend = DB::table('payments')
            ->where('term_id', $term->id)
            ->where('status', 'Verified')
            ->whereDate('paid_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupByRaw('DATE(paid_at)')
            ->orderBy('date')
            ->get();

        $school = SchoolProfile::first();

        return view('admin.fees.reports.financial-summary', compact(
            'term', 'overall', 'byCategory', 'byClassLevel', 'byClassArm', 'byMethod', 'dailyTrend', 'school'
        ));
    }

    public function exportFinancialSummaryPdf(Request $request)
    {
        $term = Term::getCurrent();
        if (!$term) { Alert::error('Error', 'No active term found.'); return back(); }

        $overall = DB::table('student_fee_ledger')->where('term_id', $term->id)->selectRaw('SUM(net_amount) as total_billed, SUM(amount_paid) as total_collected, SUM(net_amount - amount_paid) as total_outstanding')->first();

        $byCategory = DB::table('student_fee_ledger')->join('fee_structures', 'student_fee_ledger.fee_structure_id', '=', 'fee_structures.id')->join('fee_categories', 'fee_structures.fee_category_id', '=', 'fee_categories.id')->where('student_fee_ledger.term_id', $term->id)->select('fee_categories.name as category', DB::raw('SUM(student_fee_ledger.net_amount) as total_billed'), DB::raw('SUM(student_fee_ledger.amount_paid) as total_collected'), DB::raw('SUM(student_fee_ledger.net_amount - student_fee_ledger.amount_paid) as total_outstanding'))->groupBy('fee_categories.id', 'fee_categories.name')->orderBy('fee_categories.display_order')->get();

        $byClassLevel = DB::table('student_fee_ledger')->join('students', 'student_fee_ledger.student_id', '=', 'students.id')->join('student_enrollments', function ($join) { $join->on('students.id', '=', 'student_enrollments.student_id')->where('student_enrollments.is_active', true); })->join('class_arms', 'student_enrollments.class_arm_id', '=', 'class_arms.id')->join('class_levels', 'class_arms.class_level_id', '=', 'class_levels.id')->where('student_fee_ledger.term_id', $term->id)->select('class_levels.name as class_level', DB::raw('SUM(student_fee_ledger.net_amount) as total_billed'), DB::raw('SUM(student_fee_ledger.amount_paid) as total_collected'), DB::raw('SUM(student_fee_ledger.net_amount - student_fee_ledger.amount_paid) as total_outstanding'))->groupBy('class_levels.id', 'class_levels.name')->orderBy('class_levels.level_order')->get();

        $byMethod = DB::table('payments')->where('term_id', $term->id)->where('status', 'Verified')->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))->groupBy('payment_method')->get();

        $school = SchoolProfile::first();
        $pdf = Pdf::loadView('pdf.financial-summary', compact('term', 'overall', 'byCategory', 'byClassLevel', 'byMethod', 'school'))->setPaper('a4', 'landscape');
        $safeTerm = str_replace(['/', '\\'], '_', $term->name);
        return $pdf->stream("financial-summary-{$safeTerm}.pdf");
    }
}
