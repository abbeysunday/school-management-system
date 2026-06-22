<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassLevel;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class FeeStructureController extends Controller
{
    public function index(Request $request): View
    {
        $sessionId = $request->input('session_id', AcademicSession::getCurrent()->id ?? null);
        $termId    = $request->input('term_id', Term::getCurrent()->id ?? null);

        $sessions     = AcademicSession::orderByDesc('start_year')->pluck('name', 'id');
        $terms        = Term::orderByDesc('start_date')->pluck('name', 'id');
        $categories   = FeeCategory::active()->get();
        $classLevels  = ClassLevel::orderBy('level_order')->get();

        // Build grid: [category_id][class_level_id] => amount
        $grid = [];
        $dueDates = [];

        if ($sessionId && $termId) {
            $structures = FeeStructure::where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->get();

            foreach ($structures as $s) {
                $grid[$s->fee_category_id][$s->class_level_id] = $s->amount;
                $dueDates[$s->fee_category_id][$s->class_level_id] = $s->due_date?->format('Y-m-d');
            }
        }

        return view('admin.fees.structure', compact(
            'sessions', 'terms', 'categories', 'classLevels',
            'sessionId', 'termId', 'grid', 'dueDates'
        ));
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id'    => 'required|exists:terms,id',
            'amounts'    => 'required|array',
            'amounts.*.*'=> 'nullable|numeric|min:0',
            'due_dates'  => 'nullable|array',
            'due_dates.*.*'=> 'nullable|date',
        ]);

        $sessionId = $validated['session_id'];
        $termId    = $validated['term_id'];
        $amounts   = $validated['amounts'];
        $dueDates  = $validated['due_dates'] ?? [];

        $inserted = 0;
        $updated  = 0;

        DB::transaction(function () use ($sessionId, $termId, $amounts, $dueDates, &$inserted, &$updated) {
            foreach ($amounts as $categoryId => $levels) {
                foreach ($levels as $levelId => $amount) {
                    if ($amount === null || $amount === '' || $amount < 0) continue;

                    $dueDate = $dueDates[$categoryId][$levelId] ?? null;

                    $structure = FeeStructure::updateOrCreate(
                        [
                            'fee_category_id' => $categoryId,
                            'class_level_id'  => $levelId,
                            'term_id'         => $termId,
                        ],
                        [
                            'session_id' => $sessionId,
                            'amount'     => $amount,
                            'due_date'   => $dueDate,
                        ]
                    );

                    $structure->wasRecentlyCreated ? $inserted++ : $updated++;
                }
            }
        });

        Alert::success('Saved', "{$inserted} new, {$updated} updated.");
        return redirect()->route('admin.fees.structure', ['session_id' => $sessionId, 'term_id' => $termId]);
    }

    public function copyFromLastTerm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_session_id' => 'required|exists:academic_sessions,id',
            'target_term_id'    => 'required|exists:terms,id',
        ]);

        $targetSessionId = $validated['target_session_id'];
        $targetTermId    = $validated['target_term_id'];

        // Find the most recent term before the target
        $targetTerm = Term::findOrFail($targetTermId);
        $lastTerm = Term::where('end_date', '<', $targetTerm->start_date)
            ->orderByDesc('end_date')
            ->first();

        if (!$lastTerm) {
            Alert::error('Error', 'No previous term found to copy from.');
            return redirect()->back();
        }

        $sourceStructures = FeeStructure::where('term_id', $lastTerm->id)->get();

        if ($sourceStructures->isEmpty()) {
            Alert::error('Error', 'No fee structure found in previous term.');
            return redirect()->back();
        }

        $copied = 0;
        DB::transaction(function () use ($sourceStructures, $targetSessionId, $targetTermId, &$copied) {
            foreach ($sourceStructures as $source) {
                FeeStructure::updateOrCreate(
                    [
                        'fee_category_id' => $source->fee_category_id,
                        'class_level_id'  => $source->class_level_id,
                        'term_id'         => $targetTermId,
                    ],
                    [
                        'session_id' => $targetSessionId,
                        'amount'     => $source->amount,
                        'due_date'   => $source->due_date,
                    ]
                );
                $copied++;
            }
        });

        Alert::success('Copied', "{$copied} fee items copied from {$lastTerm->name} ({$lastTerm->session->name}).");
        return redirect()->route('admin.fees.structure', [
            'session_id' => $targetSessionId,
            'term_id'    => $targetTermId,
        ]);
    }

    public function destroy(FeeStructure $feeStructure): RedirectResponse
    {
        if ($feeStructure->ledgerEntries()->exists()) {
            Alert::error('Error', 'Cannot delete fee structure with student payments.');
            return redirect()->back();
        }

        $feeStructure->delete();
        Alert::success('Success', 'Fee structure deleted.');
        return redirect()->back();
    }
}
