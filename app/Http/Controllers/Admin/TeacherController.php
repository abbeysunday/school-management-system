<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\User;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => Teacher::count(),
            'active'    => Teacher::where('is_active', true)->count(),
            'full_time' => Teacher::where('employment_type', 'Full-Time')->where('is_active', true)->count(),
        ];

        return view('admin.teachers.index', compact('teachers', 'stats'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $photoPath = $this->handlePhotoUpload($request);

            $plainPassword = strtolower($validated['last_name']) . '@' . date('Y');

            $user = User::create([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email'       => $validated['email'],
                'phone'       => $validated['phone'],
                'password'    => Hash::make($plainPassword),
                'role'        => 'teacher',
                'photo'       => $photoPath,
                'is_active'   => true,
            ]);

            $teacher = $user->teacher()->create([
                'staff_id'                   => $this->generateStaffId(),
                'date_of_birth'              => $validated['date_of_birth'] ?? null,
                'gender'                     => $validated['gender'],
                'qualification'              => $validated['qualification'],
                'specialization'             => $validated['specialization'] ?? null,
                'employment_date'            => $validated['employment_date'],
                'employment_type'            => $validated['employment_type'],
                'address'                    => $validated['address'],
                'next_of_kin_name'           => $validated['next_of_kin_name'] ?? null,
                'next_of_kin_phone'          => $validated['next_of_kin_phone'] ?? null,
                'next_of_kin_relationship'   => $validated['next_of_kin_relationship'] ?? null,
                'is_active'                  => true,
            ]);

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', "Teacher registered successfully. Staff ID: {$teacher->staff_id}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function show(Teacher $teacher)
    {
        $session = AcademicSession::getCurrent();
        $teacher->load([
            'user',
            'armSubjectAssignments' => function ($query) use ($session) {
                $query->whereHas('armSubject', fn($q) => $q->where('session_id', $session->id))
                      ->with('armSubject.subject', 'armSubject.classArm');
            },
            'classArmTeachers' => function ($query) use ($session) {
                $query->where('session_id', $session->id)->with('classArm');
            }
        ]);

        return view('admin.teachers.show', compact('teacher', 'session'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $user = $teacher->user;
            $photoPath = $user->photo;

            if ($request->hasFile('photo')) {
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $this->handlePhotoUpload($request);
            }

            $user->update([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email'       => $validated['email'],
                'phone'       => $validated['phone'],
                'photo'       => $photoPath,
                'is_active'   => $validated['is_active'],
            ]);

            $teacher->update([
                'date_of_birth'              => $validated['date_of_birth'] ?? null,
                'gender'                     => $validated['gender'],
                'qualification'              => $validated['qualification'],
                'specialization'             => $validated['specialization'] ?? null,
                'employment_date'            => $validated['employment_date'],
                'employment_type'            => $validated['employment_type'],
                'address'                    => $validated['address'],
                'next_of_kin_name'           => $validated['next_of_kin_name'] ?? null,
                'next_of_kin_phone'          => $validated['next_of_kin_phone'] ?? null,
                'next_of_kin_relationship'   => $validated['next_of_kin_relationship'] ?? null,
                'is_active'                  => $validated['is_active'],
            ]);

            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'Teacher profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            if ($teacher->user->photo && Storage::disk('public')->exists($teacher->user->photo)) {
                Storage::disk('public')->delete($teacher->user->photo);
            }
            $teacher->user()->delete();
            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }

    private function handlePhotoUpload(Request $request): ?string
    {
        if (!$request->hasFile('photo')) return null;

        $image = $request->file('photo');
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = 'teachers/photos/' . $filename;
        Storage::disk('public')->makeDirectory('teachers/photos');

        $manager = new ImageManager(new Driver());
        $manager->read($image->getRealPath())->cover(300, 300)->save(storage_path('app/public/' . $path));

        return $path;
    }

    private function generateStaffId(): string
    {
        $school = SchoolProfile::current();
        $prefix = ($school->short_name ?? 'SCH') . '/STF/';

        $latest = Teacher::where('staff_id', 'like', $prefix . '%')
                         ->lockForUpdate()
                         ->orderBy('id', 'desc')
                         ->first();

        $sequence = $latest ? ((int) explode('/', $latest->staff_id)[2]) + 1 : 1;

        do {
            $staffId = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Teacher::where('staff_id', $staffId)->exists());

        return $staffId;
    }
}
