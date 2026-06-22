<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TermController extends Controller
{
    public function index(): View
    {
        $sessions = AcademicSession::with(['terms' => fn ($q) => $q->orderBy('start_date')])
            ->orderByDesc('start_year')
            ->get();

        return view('admin.terms.index', compact('sessions'));
    }

    public function create(): View
    {
        $sessions = AcademicSession::pluck('name', 'id');
        return view('admin.terms.create', compact('sessions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id'             => 'required|exists:academic_sessions,id',
            'name'                   => 'required|in:First Term,Second Term,Third Term',
            'start_date'             => 'required|date',
            'end_date'               => 'required|date|after_or_equal:start_date',
            'mid_term_break_start'   => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
            'mid_term_break_end'     => 'nullable|date|after_or_equal:mid_term_break_start|before_or_equal:end_date',
            'next_resumption_date'   => 'nullable|date|after:end_date',
            'total_school_days'      => 'nullable|integer|min:0|max:366',
        ]);

        // Prevent creating a fourth term
        if (Term::where('session_id', $validated['session_id'])->count() >= 3) {
            alert()->error('Error', 'A session cannot have more than 3 terms.');
            return redirect()->back()->withInput();
        }

        // Prevent duplicate term name in same session
        if (Term::where('session_id', $validated['session_id'])->where('name', $validated['name'])->exists()) {
            alert()->error('Error', 'This term already exists for the selected session.');
            return redirect()->back()->withInput();
        }

        Term::create($validated);

        alert()->success('Success', 'Term created successfully.');
        return redirect()->route('admin.terms.index');
    }

    public function edit(Term $term): View
    {
        $sessions = AcademicSession::pluck('name', 'id');
        return view('admin.terms.edit', compact('term', 'sessions'));
    }

    public function update(Request $request, Term $term): RedirectResponse
    {
        $validated = $request->validate([
            'session_id'             => 'required|exists:academic_sessions,id',
            'name'                   => 'required|in:First Term,Second Term,Third Term',
            'start_date'             => 'required|date',
            'end_date'               => 'required|date|after_or_equal:start_date',
            'mid_term_break_start'   => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
            'mid_term_break_end'     => 'nullable|date|after_or_equal:mid_term_break_start|before_or_equal:end_date',
            'next_resumption_date'   => 'nullable|date|after:end_date',
            'total_school_days'      => 'nullable|integer|min:0|max:366',
        ]);

        $exists = Term::where('session_id', $validated['session_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $term->id)
            ->exists();

        if ($exists) {
            alert()->error('Error', 'This term already exists for the selected session.');
            return redirect()->back()->withInput();
        }

        $term->update($validated);

        alert()->success('Success', 'Term updated successfully.');
        return redirect()->route('admin.terms.index');
    }

    public function destroy(Term $term): RedirectResponse
    {
        if ($term->is_current) {
            alert()->error('Error', 'Cannot delete the currently active term.');
            return redirect()->route('admin.terms.index');
        }

        $term->delete();
        alert()->success('Success', 'Term deleted.');
        return redirect()->route('admin.terms.index');
    }

    public function setAsCurrent(Term $term): RedirectResponse
    {
        DB::transaction(function () use ($term) {
            Term::where('is_current', true)->update(['is_current' => false]);
            $term->update(['is_current' => true]);
        });

        alert()->success('Success', 'Term set as current.');
        return redirect()->route('admin.terms.index');
    }

    public function updateSchoolDays(Request $request, Term $term): RedirectResponse
    {
        $validated = $request->validate([
            'total_school_days' => 'required|integer|min:1|max:366',
        ]);

        $term->update(['total_school_days' => $validated['total_school_days']]);

        alert()->success('Success', 'School days updated.');
        return redirect()->route('admin.terms.index');
    }
}
