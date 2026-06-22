<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::orderBy('category')->orderBy('name')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100|unique:subjects,name',
            'code'            => 'nullable|string|max:10|unique:subjects,code',
            'category'        => 'required|in:General,Science,Arts,Commercial,Technical,Vocational',
            'is_waec_subject' => 'boolean',
            'is_neco_subject' => 'boolean',
            'is_core'         => 'boolean',
            'is_active'       => 'boolean',
        ]);

        $validated['is_waec_subject'] = $request->boolean('is_waec_subject');
        $validated['is_neco_subject'] = $request->boolean('is_neco_subject');
        $validated['is_core']         = $request->boolean('is_core');
        $validated['is_active']       = $request->boolean('is_active', true);

        Subject::create($validated);

        alert()->success('Success', 'Subject created.');
        return redirect()->route('admin.subjects.index');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100|unique:subjects,name,' . $subject->id,
            'code'            => 'nullable|string|max:10|unique:subjects,code,' . $subject->id,
            'category'        => 'required|in:General,Science,Arts,Commercial,Technical,Vocational',
            'is_waec_subject' => 'boolean',
            'is_neco_subject' => 'boolean',
            'is_core'         => 'boolean',
            'is_active'       => 'boolean',
        ]);

        $validated['is_waec_subject'] = $request->boolean('is_waec_subject');
        $validated['is_neco_subject'] = $request->boolean('is_neco_subject');
        $validated['is_core']         = $request->boolean('is_core');
        $validated['is_active']       = $request->boolean('is_active', true);

        $subject->update($validated);

        alert()->success('Success', 'Subject updated.');
        return redirect()->route('admin.subjects.index');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->armSubjects()->exists()) {
            alert()->error('Error', 'Cannot delete subject assigned to class arms.');
            return redirect()->route('admin.subjects.index');
        }

        $subject->delete();
        alert()->success('Success', 'Subject deleted.');
        return redirect()->route('admin.subjects.index');
    }
}
