<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicSessionController extends Controller
{
    public function index(): View
    {
        $sessions = AcademicSession::withCount('terms')->latest('start_year')->get();
        return view('admin.sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        return view('admin.sessions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:academic_sessions,name',
            'start_year' => 'required|integer|min:1990|max:2099',
            'end_year'   => 'required|integer|min:1990|max:2099|gt:start_year',
            'is_closed'  => 'boolean',
        ]);

        AcademicSession::create($validated);

        alert()->success('Success', 'Academic session created.');
        return redirect()->route('admin.sessions.index');
    }

    public function edit(AcademicSession $session): View
    {
        return view('admin.sessions.edit', compact('session'));
    }

    public function update(Request $request, AcademicSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:academic_sessions,name,'.$session->id,
            'start_year' => 'required|integer|min:1990|max:2099',
            'end_year'   => 'required|integer|min:1990|max:2099|gt:start_year',
            'is_closed'  => 'boolean',
        ]);

        $session->update($validated);

        alert()->success('Success', 'Academic session updated.');
        return redirect()->route('admin.sessions.index');
    }

    public function destroy(AcademicSession $session): RedirectResponse
    {
        if ($session->is_current) {
            alert()->error('Error', 'Cannot delete the currently active session.');
            return redirect()->route('admin.sessions.index');
        }

        $session->delete();
        alert()->success('Success', 'Academic session deleted.');
        return redirect()->route('admin.sessions.index');
    }

    public function setAsCurrent(AcademicSession $session): RedirectResponse
    {
        // Prevent setting future session if previous is not closed
        $unclosedPrevious = AcademicSession::where('start_year', '<', $session->start_year)
            ->where('is_closed', false)
            ->exists();

        if ($unclosedPrevious) {
            alert()->error('Error', 'Close the previous session before activating a future one.');
            return redirect()->route('admin.sessions.index');
        }

        DB::transaction(function () use ($session) {
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
            $session->update(['is_current' => true]);
        });

        alert()->success('Success', 'Session set as current.');
        return redirect()->route('admin.sessions.index');
    }
}
