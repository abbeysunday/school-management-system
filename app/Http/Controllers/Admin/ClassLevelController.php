<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassLevelController extends Controller
{
    public function index(): View
    {
        $classLevels = ClassLevel::with(['classArms' => fn ($q) => $q->withCount('enrollments')])
            ->orderBy('level_order')
            ->get();

        return view('admin.classes.arms', compact('classLevels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:10|unique:class_levels,name',
            'level_order' => 'required|integer|min:1|max:20|unique:class_levels,level_order',
            'category'    => 'required|in:Junior,Senior',
        ]);

        ClassLevel::create($validated);

        alert()->success('Success', 'Class level created.');
        return redirect()->route('admin.classes.levels');
    }

    public function update(Request $request, ClassLevel $classLevel): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:10|unique:class_levels,name,' . $classLevel->id,
            'category' => 'required|in:Junior,Senior',
        ]);

        $classLevel->update($validated);

        alert()->success('Success', 'Class level updated.');
        return redirect()->route('admin.classes.levels');
    }

    public function destroy(ClassLevel $classLevel): RedirectResponse
    {
        $hasStudents = $classLevel->classArms()->whereHas('enrollments')->exists();

        if ($hasStudents) {
            alert()->error('Error', 'Cannot delete class level with enrolled students.');
            return redirect()->route('admin.classes.levels');
        }

        $classLevel->delete();
        alert()->success('Success', 'Class level deleted.');
        return redirect()->route('admin.classes.levels');
    }
}
