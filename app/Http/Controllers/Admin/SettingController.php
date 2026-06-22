<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaConfiguration;
use App\Models\GradingScale;
use App\Models\SchoolProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    // ── Grading Scale ──
    public function grading(): View
    {
        $scales = GradingScale::orderBy('grade_order')->get();
        return view('admin.settings.grading', compact('scales'));
    }

    public function updateGrading(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scales'              => 'required|array',
            'scales.*.id'         => 'required|exists:grading_scales,id',
            'scales.*.min_score'  => 'required|numeric|min:0|max:100',
            'scales.*.max_score'  => 'required|numeric|min:0|max:100',
            'scales.*.remark'     => 'required|string|max:50',
            'scales.*.is_pass'    => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['scales'] as $scale) {
                GradingScale::where('id', $scale['id'])->update([
                    'min_score' => $scale['min_score'],
                    'max_score' => $scale['max_score'],
                    'remark'    => $scale['remark'],
                    'is_pass'   => $scale['is_pass'] ?? false,
                ]);
            }
        });

        alert()->success('Success', 'Grading scale updated.');
        return redirect()->route('admin.settings.grading');
    }

    // ── CA Configuration ──
    public function caConfig(): View
    {
        $components = CaConfiguration::orderBy('order')->get();
        $caWeight   = SchoolProfile::first()?->ca_weight ?? 30;

        $currentTotal = $components->where('is_active', true)->sum('max_score');

        return view('admin.settings.ca-config', compact('components', 'caWeight', 'currentTotal'));
    }

    public function updateCaConfig(Request $request): RedirectResponse
    {
        $caWeight = SchoolProfile::first()?->ca_weight ?? 30;

        $validated = $request->validate([
            'components'              => 'required|array',
            'components.*.id'         => 'required|exists:ca_configurations,id',
            'components.*.max_score'  => 'required|numeric|min:0|max:100',
            'components.*.order'      => 'required|integer|min:1|max:20',
            'components.*.is_active'  => 'boolean',
        ]);

        // Calculate total of active components
        $activeTotal = collect($validated['components'])
            ->filter(fn ($c) => $c['is_active'] ?? false)
            ->sum('max_score');

        if (abs($activeTotal - $caWeight) > 0.01) {
            alert()->error(
                'Validation Error',
                "Active CA components must sum to exactly {$caWeight}%. Current sum: {$activeTotal}."
            );
            return redirect()->back()->withInput();
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['components'] as $component) {
                CaConfiguration::where('id', $component['id'])->update([
                    'max_score' => $component['max_score'],
                    'order'     => $component['order'],
                    'is_active' => $component['is_active'] ?? false,
                ]);
            }
        });

        alert()->success('Success', 'CA configuration updated.');
        return redirect()->route('admin.settings.ca-config');
    }

    // ── School Calendar ──
    public function calendar(): View
    {
        $terms = \App\Models\Term::with('session')
            ->orderByDesc('start_date')
            ->paginate(10);

        $holidays = \App\Models\SchoolCalendar::orderBy('date')->get();

        return view('admin.settings.school-calendar', compact('terms', 'holidays'));
    }

    public function updateCalendar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'term_id'              => 'required|exists:terms,id',
            'mid_term_break_start' => 'nullable|date',
            'mid_term_break_end'   => 'nullable|date|after_or_equal:mid_term_break_start',
            'next_resumption_date' => 'nullable|date',
        ]);

        $term = \App\Models\Term::findOrFail($validated['term_id']);
        $term->update([
            'mid_term_break_start' => $validated['mid_term_break_start'],
            'mid_term_break_end'   => $validated['mid_term_break_end'],
            'next_resumption_date' => $validated['next_resumption_date'],
        ]);

        alert()->success('Success', 'Calendar updated.');
        return redirect()->route('admin.settings.calendar');
    }
}
