<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Question::where('created_by', auth()->id())
            ->with(['subject', 'classLevel'])
            ->withCount('cbtExams as usage_count');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $questions = $query->latest()->paginate(20)->withQueryString();
        $subjects  = Subject::active()->pluck('name', 'id');

        return view('teacher.questions.index', compact('questions', 'subjects'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->pluck('name', 'id');
        $levels   = ClassLevel::orderBy('level_order')->pluck('name', 'id');
        return view('teacher.questions.create', compact('subjects', 'levels'));
    }
}
