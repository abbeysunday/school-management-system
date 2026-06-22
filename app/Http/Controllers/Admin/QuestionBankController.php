<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RealRashid\SweetAlert\Facades\Alert;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $query = Question::with(['subject', 'classLevel', 'creator'])
            ->withCount('cbtExams as usage_count');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('class_level_id')) {
            $query->where('class_level_id', $request->class_level_id);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->latest()->paginate(25)->withQueryString();
        $subjects   = Subject::active()->pluck('name', 'id');
        $levels     = ClassLevel::orderBy('level_order')->pluck('name', 'id');

        return view('admin.questions.index', compact('questions', 'subjects', 'levels'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->pluck('name', 'id');
        $levels   = ClassLevel::orderBy('level_order')->pluck('name', 'id');
        return view('admin.questions.create', compact('subjects', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'subject_id'                   => 'required|exists:subjects,id',
            'class_level_id'               => 'nullable|exists:class_levels,id',
            'questions'                    => 'required|array|min:1',
            'questions.*.question_text'    => 'required|string|max:2000',
            'questions.*.option_a'         => 'required|string|max:500',
            'questions.*.option_b'         => 'required|string|max:500',
            'questions.*.option_c'         => 'required|string|max:500',
            'questions.*.option_d'         => 'required|string|max:500',
            'questions.*.correct_option'   => 'required|in:A,B,C,D',
            'questions.*.explanation'      => 'nullable|string|max:2000',
            'questions.*.difficulty'       => 'required|in:Easy,Medium,Hard',
        ]);

        $count = 0;
        foreach ($request->questions as $q) {
            Question::create([
                'subject_id'     => $request->subject_id,
                'class_level_id' => $request->class_level_id ?: null,
                'created_by'     => auth()->id(),
                'question_text'  => $q['question_text'],
                'option_a'       => $q['option_a'],
                'option_b'       => $q['option_b'],
                'option_c'       => $q['option_c'],
                'option_d'       => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'explanation'    => $q['explanation'] ?? null,
                'difficulty'     => $q['difficulty'],
                'is_active'      => true,
            ]);
            $count++;
        }

        Alert::success('Success', "{$count} question(s) added to bank.");
        return redirect()->route('admin.questions.index');
    }

    public function edit(Question $question): View
    {
        $subjects = Subject::active()->pluck('name', 'id');
        $levels   = ClassLevel::orderBy('level_order')->pluck('name', 'id');
        return view('admin.questions.edit', compact('question', 'subjects', 'levels'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id'      => 'required|exists:subjects,id',
            'class_level_id'  => 'nullable|exists:class_levels,id',
            'question_text'   => 'required|string|max:2000',
            'option_a'        => 'required|string|max:500',
            'option_b'        => 'required|string|max:500',
            'option_c'        => 'required|string|max:500',
            'option_d'        => 'required|string|max:500',
            'correct_option'  => 'required|in:A,B,C,D',
            'explanation'     => 'nullable|string|max:2000',
            'difficulty'      => 'required|in:Easy,Medium,Hard',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($question->image_path) Storage::disk('public')->delete($question->image_path);
            $validated['image_path'] = $this->handleQuestionImage($request);
        }

        $question->update($validated);

        Alert::success('Success', 'Question updated.');
        return redirect()->route('admin.questions.index');
    }

    public function destroy(Question $question): RedirectResponse
    {
        if ($question->cbtExams()->exists()) {
            Alert::error('Error', 'Cannot delete question used in exams.');
            return redirect()->route('admin.questions.index');
        }

        if ($question->image_path) Storage::disk('public')->delete($question->image_path);
        $question->delete();

        Alert::success('Success', 'Question deleted.');
        return redirect()->route('admin.questions.index');
    }

    public function preview(Question $question): View
    {
        return view('admin.questions.preview', compact('question'))->render();
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file'  => 'required|file|mimes:csv,txt|max:5120',
            'subject_id'=> 'required|exists:subjects,id',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $expected = ['question_text','option_a','option_b','option_c','option_d','correct_option','difficulty'];

        if (array_map('strtolower', $header) !== $expected) {
            Alert::error('Invalid CSV', 'Headers must be: ' . implode(', ', $expected));
            return redirect()->back();
        }

        $inserted = 0; $rejected = 0; $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) { $rejected++; continue; }

            [$qText, $optA, $optB, $optC, $optD, $correct, $difficulty] = $row;

            if (empty($qText) || empty($optA) || empty($optB) || empty($optC) || empty($optD)) {
                $rejected++; $errors[] = "Row rejected: empty question or option."; continue;
            }

            if (!in_array(strtoupper($correct), ['A','B','C','D'])) {
                $rejected++; $errors[] = "Row rejected: correct_option '{$correct}' invalid."; continue;
            }

            Question::create([
                'subject_id'     => $request->subject_id,
                'created_by'     => auth()->id(),
                'question_text'  => $qText,
                'option_a'       => $optA,
                'option_b'       => $optB,
                'option_c'       => $optC,
                'option_d'       => $optD,
                'correct_option' => strtoupper($correct),
                'difficulty'     => in_array($difficulty, ['Easy','Medium','Hard']) ? $difficulty : 'Medium',
                'is_active'      => true,
            ]);

            $inserted++;
        }

        fclose($handle);

        if ($rejected > 0) {
            Alert::warning('Import Complete', "{$inserted} inserted, {$rejected} rejected.");
        } else {
            Alert::success('Import Complete', "{$inserted} questions imported.");
        }

        return redirect()->route('admin.questions.index');
    }

    // ── Image handler using your preferred pattern ──
    private function handleQuestionImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) return null;

        $image = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = 'questions/' . $filename;

        Storage::disk('public')->makeDirectory('questions');

        $manager = new ImageManager(new Driver());
        $manager->read($image->getRealPath())
                ->scaleDown(800, 800) // Resize max 800px wide, keep aspect ratio
                ->save(storage_path('app/public/' . $path));

        return $path;
    }
}
