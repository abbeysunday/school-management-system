<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassArm;
use App\Models\Term;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $term = Term::getCurrent();
        $query = AttendanceRecord::with(['student.user', 'classArm.classLevel', 'term', 'markedBy'])
            ->latest('attendance_date');

        if ($request->filled('class_arm_id')) {
            $query->where('class_arm_id', $request->class_arm_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', fn($q) => $q->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
        }

        $records = $query->paginate(30)->withQueryString();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        $stats = [
            'today_present' => AttendanceRecord::whereDate('attendance_date', today())->whereIn('status', ['Present', 'Late'])->count(),
            'today_absent' => AttendanceRecord::whereDate('attendance_date', today())->where('status', 'Absent')->count(),
            'today_total' => AttendanceRecord::whereDate('attendance_date', today())->count(),
        ];

        return view('admin.attendance.index', compact('records', 'classArms', 'stats'));
    }

    public function edit(AttendanceRecord $attendance)
    {
        $attendance->load(['student.user', 'classArm.classLevel', 'term', 'markedBy']);
        return view('admin.attendance.edit', compact('attendance'));
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:Present,Absent,Late,Sick,Excused',
            'remarks' => 'nullable|string|max:255',
            'reason' => 'required|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'marked_by' => auth()->id(),
        ]);

        \Illuminate\Support\Facades\Log::info('Admin attendance override', [
            'attendance_id' => $attendance->id,
            'admin_id' => auth()->id(),
            'reason' => $validated['reason'],
            'new_status' => $validated['status'],
        ]);

        Alert::success('Updated', 'Attendance record has been updated. Reason: ' . $validated['reason']);
        return redirect()->route('admin.attendance.index');
    }
}
