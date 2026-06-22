<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassArmController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'arm'            => 'required|string|max:15|unique:class_arms,arm,NULL,id,class_level_id,' . $request->class_level_id,
            'capacity'       => 'required|integer|min:1|max:200',
        ]);

        ClassArm::create($validated);

        alert()->success('Success', 'Class arm created.');
        return redirect()->route('admin.classes.levels');
    }

    public function update(Request $request, ClassArm $classArm): RedirectResponse
    {
        $validated = $request->validate([
            'class_level_id' => 'required|exists:class_levels,id',
            'arm'            => 'required|string|max:15|unique:class_arms,arm,' . $classArm->id . ',id,class_level_id,' . $request->class_level_id,
            'capacity'       => 'required|integer|min:1|max:200',
        ]);

        $classArm->update($validated);

        alert()->success('Success', 'Class arm updated.');
        return redirect()->route('admin.classes.levels');
    }

    public function destroy(ClassArm $classArm): RedirectResponse
    {
        if ($classArm->enrollments()->exists()) {
            alert()->error('Error', 'Cannot delete class arm with enrolled students.');
            return redirect()->route('admin.classes.levels');
        }

        $classArm->delete();
        alert()->success('Success', 'Class arm deleted.');
        return redirect()->route('admin.classes.levels');
    }
}
