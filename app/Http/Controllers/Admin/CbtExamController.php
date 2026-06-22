<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\CbtExam;
use App\Models\ClassArm;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class CbtExamController extends Controller
{
    public function index(Request $request): View
    {
        $query = CbtExam::with(['subject', 'classArm.classLevel', 'term'])
            ->withCount('questions');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('class_arm_id')) {
            $query->where('class_arm_id', $request->class_arm_id);
        }

        $exams     = $query->latest()->paginate(20)->withQueryString();
        $subjects  = Subject::active()->pluck('name', 'id');
        $classArms = ClassArm::with('classLevel')->get();

        return view('admin.cbt.index', compact('exams', 'subjects', 'classArms'));
    }

    public function create(): View
    {
        $subjects  = Subject::active()->pluck('name', 'id');
        $classArms = ClassArm::with('classLevel')->get();
        $terms     = Term::with('session')->latest()->get();
        $currentTerm = Term::getCurrent();

        return view('admin.cbt.create', compact('subjects', 'classArms', 'terms', 'currentTerm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'subject_id'               => 'required|exists:subjects,id',
            'class_arm_id'             => 'required|exists:class_arms,id',
            'term_id'                  => 'required|exists:terms,id',
            'exam_type'                => 'required|in:Practice,Assessment,Final',
            'duration_minutes'         => 'required|integer|min:5|max:480',
            'total_questions'          => 'required|integer|min:1|max:200',
            'marks_per_question'       => 'required|numeric|min:0.5|max:100',
            'negative_marking'         => 'boolean',
            'marks_deducted_per_wrong' => 'nullable|numeric|min:0',
            'randomize_questions'      => 'boolean',
            'randomize_options'        => 'boolean',
            'show_result_immediately'  => 'boolean',
            'allow_retake'             => 'boolean',
            'max_retakes'              => 'nullable|integer|min:1',
            'start_datetime'           => 'nullable|date',
            'end_datetime'             => 'nullable|date|after_or_equal:start_datetime',
            'instructions'             => 'nullable|string|max:5000',
        ]);

        $validated['created_by']               = auth()->id();
        $validated['status']                   = 'Draft';
        $validated['total_marks']              = $validated['total_questions'] * $validated['marks_per_question'];
        $validated['negative_marking']         = $request->boolean('negative_marking');
        $validated['randomize_questions']      = $request->boolean('randomize_questions');
        $validated['randomize_options']        = $request->boolean('randomize_options');
        $validated['show_result_immediately']  = $request->boolean('show_result_immediately', true);
        $validated['allow_retake']             = $request->boolean('allow_retake');

        $exam = CbtExam::create($validated);

        Alert::success('Exam Created', "'{$exam->title}' created as Draft. Now add questions.");
        return redirect()->route('admin.cbt.exams.show', $exam);
    }

    public function show(CbtExam $exam): View
    {
        $exam->load(['subject', 'classArm.classLevel', 'term', 'questions' => function ($q) {
            $q->orderBy('cbt_exam_questions.question_order');
        }]);

        $attachedIds = $exam->questions->pluck('id');

        // Available questions: same subject, not already attached
        $available = Question::with('classLevel')
            ->where('subject_id', $exam->subject_id)
            ->where('is_active', true)
            ->whereNotIn('id', $attachedIds)
            ->orderBy('difficulty')
            ->get();

        $diffCounts = [
            'Easy'   => $available->where('difficulty', 'Easy')->count(),
            'Medium' => $available->where('difficulty', 'Medium')->count(),
            'Hard'   => $available->where('difficulty', 'Hard')->count(),
        ];

        return view('admin.cbt.show', compact('exam', 'available', 'diffCounts'));
    }

    public function edit(CbtExam $exam): View
    {
        $subjects  = Subject::active()->pluck('name', 'id');
        $classArms = ClassArm::with('classLevel')->get();
        $terms     = Term::with('session')->latest()->get();

        return view('admin.cbt.edit', compact('exam', 'subjects', 'classArms', 'terms'));
    }

    public function update(Request $request, CbtExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'subject_id'               => 'required|exists:subjects,id',
            'class_arm_id'             => 'required|exists:class_arms,id',
            'term_id'                  => 'required|exists:terms,id',
            'exam_type'                => 'required|in:Practice,Assessment,Final',
            'status'                   => 'required|in:Draft,Scheduled,Active,Completed,Cancelled',
            'duration_minutes'         => 'required|integer|min:5|max:480',
            'total_questions'          => 'required|integer|min:1|max:200',
            'marks_per_question'       => 'required|numeric|min:0.5|max:100',
            'negative_marking'         => 'boolean',
            'marks_deducted_per_wrong' => 'nullable|numeric|min:0',
            'randomize_questions'      => 'boolean',
            'randomize_options'        => 'boolean',
            'show_result_immediately'  => 'boolean',
            'allow_retake'             => 'boolean',
            'max_retakes'              => 'nullable|integer|min:1',
            'start_datetime'           => 'nullable|date',
            'end_datetime'             => 'nullable|date|after_or_equal:start_datetime',
            'instructions'             => 'nullable|string|max:5000',
        ]);

        $validated['total_marks']              = $validated['total_questions'] * $validated['marks_per_question'];
        $validated['negative_marking']         = $request->boolean('negative_marking');
        $validated['randomize_questions']      = $request->boolean('randomize_questions');
        $validated['randomize_options']        = $request->boolean('randomize_options');
        $validated['show_result_immediately']  = $request->boolean('show_result_immediately', true);
        $validated['allow_retake']             = $request->boolean('allow_retake');

        $exam->update($validated);

        Alert::success('Success', 'Exam updated.');
        return redirect()->route('admin.cbt.exams.show', $exam);
    }

    public function destroy(CbtExam $exam): RedirectResponse
    {
        if ($exam->attempts()->exists()) {
            Alert::error('Error', 'Cannot delete an exam that has student attempts.');
            return redirect()->route('admin.cbt.exams.index');
        }

        $exam->questions()->detach();
        $exam->delete();

        Alert::success('Deleted', 'Exam deleted successfully.');
        return redirect()->route('admin.cbt.exams.index');
    }

    public function attachQuestions(Request $request, CbtExam $exam): RedirectResponse
    {
        $request->validate([
            'question_ids'   => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $currentMax = $exam->questions()->max('cbt_exam_questions.question_order') ?? 0;
        $order = $currentMax;

        foreach ($request->question_ids as $qid) {
            if (!$exam->questions()->where('questions.id', $qid)->exists()) {
                $order++;
                $exam->questions()->attach($qid, ['question_order' => $order]);
            }
        }

        $count = count($request->question_ids);
        Alert::success('Added', "{$count} question(s) added to the exam.");
        return redirect()->route('admin.cbt.exams.show', $exam);
    }

    public function detachQuestion(CbtExam $exam, Question $question): RedirectResponse
    {
        $exam->questions()->detach($question->id);

        // Re-sequence orders
        $questions = $exam->questions()->orderBy('cbt_exam_questions.question_order')->get();
        foreach ($questions as $i => $q) {
            $exam->questions()->updateExistingPivot($q->id, ['question_order' => $i + 1]);
        }

        Alert::success('Removed', 'Question removed from exam.');
        return redirect()->route('admin.cbt.exams.show', $exam);
    }

    public function autoSelect(Request $request, CbtExam $exam): RedirectResponse
    {
        $request->validate([
            'easy_count'   => 'nullable|integer|min:0',
            'medium_count' => 'nullable|integer|min:0',
            'hard_count'   => 'nullable|integer|min:0',
        ]);

        $attachedIds = $exam->questions()->pluck('questions.id');
        $order = $exam->questions()->max('cbt_exam_questions.question_order') ?? 0;
        $added = 0;

        foreach (['Easy' => 'easy_count', 'Medium' => 'medium_count', 'Hard' => 'hard_count'] as $diff => $field) {
            $count = (int) $request->input($field, 0);
            if ($count <= 0) continue;

            $pool = Question::where('subject_id', $exam->subject_id)
                ->where('difficulty', $diff)
                ->where('is_active', true)
                ->whereNotIn('id', $attachedIds)
                ->inRandomOrder()
                ->limit($count)
                ->get();

            foreach ($pool as $q) {
                $order++;
                $exam->questions()->attach($q->id, ['question_order' => $order]);
                $attachedIds->push($q->id);
                $added++;
            }
        }

        Alert::success('Auto-Selected', "{$added} question(s) auto-selected and added.");
        return redirect()->route('admin.cbt.exams.show', $exam);
    }
}
