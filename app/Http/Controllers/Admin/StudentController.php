<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesFeeLedger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RealRashid\SweetAlert\Facades\Alert;

class StudentController extends Controller
{
    use GeneratesFeeLedger;

    public function index(Request $request)
    {
        $query = Student::with(['user', 'currentEnrollment.classArm.classLevel']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_arm_id')) {
            $query->whereHas('currentEnrollment', fn ($q) => $q->where('class_arm_id', $request->class_arm_id)->where('is_active', true));
        }

        $students = $query->latest('admission_date')->paginate(15)->withQueryString();
        $classArms = ClassArm::with('classLevel')->get();

        $stats = [
            'total'     => Student::count(),
            'active'    => Student::where('status', 'Active')->count(),
            'graduated' => Student::where('status', 'Graduated')->count(),
            'others'    => Student::whereNotIn('status', ['Active', 'Graduated'])->count(),
        ];

        return view('admin.students.index', compact('students', 'classArms', 'stats'));
    }

    public function create()
    {
        $classArms = ClassArm::with('classLevel')->get();
        return view('admin.students.create', compact('classArms'));
    }

    public function store(StoreStudentRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $photoPath = $this->handlePhotoUpload($request);
            $session = AcademicSession::getCurrent();
            $term = Term::getCurrent();

            $user = User::create([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email'       => $validated['email'] ?? null,
                'phone'       => $validated['phone'] ?? null,
                'password'    => Hash::make(strtolower($validated['last_name']) . '123'),
                'role'        => 'student',
                'photo'       => $photoPath,
                'is_active'   => true,
            ]);

            $student = $user->student()->create([
                'admission_number'   => $this->generateAdmissionNumber(),
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'gender'             => $validated['gender'],
                'religion'           => $validated['religion'] ?? null,
                'state_of_origin'    => $validated['state_of_origin'] ?? null,
                'lga'                => $validated['lga'] ?? null,
                'home_address'       => $validated['home_address'] ?? null,
                'blood_group'        => $validated['blood_group'] ?? null,
                'genotype'           => $validated['genotype'] ?? null,
                'medical_conditions' => $validated['medical_conditions'] ?? null,
                'previous_school'    => $validated['previous_school'] ?? null,
                'admission_date'     => $validated['admission_date'] ?? now(),
                'status'             => 'Active',
            ]);

            if (!empty($validated['class_arm_id'])) {
                $classArm = ClassArm::with('classLevel')->find($validated['class_arm_id']);

                StudentEnrollment::create([
                    'student_id'      => $student->id,
                    'session_id'      => $session->id,
                    'class_arm_id'    => $validated['class_arm_id'],
                    'enrollment_date' => now(),
                    'is_active'       => true,
                ]);

                if ($classArm && $term) {
                    $this->generateFeeLedger($student->id, $classArm, $term);
                }
            }

            DB::commit();
            Alert::success('Success', 'Student registered successfully!');
            return redirect()->route('admin.students.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Registration failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'enrollments.classArm.classLevel',
            'enrollments.session',
            'enrollments.term',
            'payments',
            'results.subject',
            'termSummaries.term',
            'parentStudents.parentUser',
        ]);

        $linkedParentIds = $student->parentStudents->pluck('parent_user_id');
        $availableParents = User::where('role', 'parent')
                                ->whereNotIn('id', $linkedParentIds)
                                ->orderBy('last_name')
                                ->get();

        return view('admin.students.show', compact('student', 'availableParents'));
    }

    public function edit(Student $student)
    {
        $student->load(['user', 'currentEnrollment']);
        $classArms = ClassArm::with('classLevel')->get();
        return view('admin.students.edit', compact('student', 'classArms'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $user = $student->user;
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
                'email'       => $validated['email'] ?? null,
                'phone'       => $validated['phone'] ?? null,
                'photo'       => $photoPath,
            ]);

            $student->update([
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'gender'             => $validated['gender'],
                'religion'           => $validated['religion'] ?? null,
                'state_of_origin'    => $validated['state_of_origin'] ?? null,
                'lga'                => $validated['lga'] ?? null,
                'home_address'       => $validated['home_address'] ?? null,
                'blood_group'        => $validated['blood_group'] ?? null,
                'genotype'           => $validated['genotype'] ?? null,
                'medical_conditions' => $validated['medical_conditions'] ?? null,
                'previous_school'    => $validated['previous_school'] ?? null,
                'admission_date'     => $validated['admission_date'] ?? null,
                'status'             => $validated['status'],
            ]);

            $newArmId = $validated['class_arm_id'] ?? null;
            if ($newArmId) {
                $term    = Term::getCurrent();
                $session = AcademicSession::getCurrent();
                $newArm  = ClassArm::with('classLevel')->find($newArmId);

                $enrollment = StudentEnrollment::firstOrNew(
                    ['student_id' => $student->id, 'session_id' => $session->id],
                );

                $oldLevelId = $enrollment->exists ? $enrollment->classArm?->class_level_id : null;

                $enrollment->class_arm_id    = $newArmId;
                $enrollment->enrollment_date = now();
                $enrollment->is_active         = true;
                $enrollment->save();

                $newLevelId = $newArm?->class_level_id;

                if ($newArm && $newLevelId !== $oldLevelId) {
                    $existingLedgers = StudentFeeLedger::where('student_id', $student->id)
                        ->where('term_id', $term->id)
                        ->get();

                    $hasPayments = $existingLedgers->where('amount_paid', '>', 0)->isNotEmpty();

                    if ($hasPayments) {
                        throw new \Exception('Cannot change class level automatically. The student has already made fee payments for the current term. Please address the finances manually.');
                    }

                    StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->delete();
                    $this->generateFeeLedger($student->id, $newArm, $term);
                }
            }

            DB::commit();
            Alert::success('Success', 'Student updated successfully!');
            return redirect()->route('admin.students.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Update failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(Student $student)
    {
        if ($student->user->photo && Storage::disk('public')->exists($student->user->photo)) {
            Storage::disk('public')->delete($student->user->photo);
        }
        $student->user()->delete();
        Alert::success('Success', 'Student deleted successfully.');
        return redirect()->route('admin.students.index');
    }

    public function idCard($id)
    {
        $student = Student::with([
            'user',
            'currentEnrollment.classArm.classLevel',
            'currentEnrollment.session',
            'currentEnrollment.term',
        ])->findOrFail($id);

        $school = SchoolProfile::first();

        $pdf = Pdf::loadView('pdf.student-id-card', compact('student', 'school'))
                  ->setPaper([0, 0, 153.07, 243.78], 'portrait');

        $safeAdmissionNumber = str_replace(['/', '\\'], '_', $student->admission_number);
        return $pdf->stream('ID_Card_' . $safeAdmissionNumber . '.pdf');
    }

    /* ── Parent Linking ── */
    public function linkParent(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'parent_user_id'   => 'required|exists:users,id',
            'relationship'     => 'required|string|max:50',
            'is_primary_contact' => 'boolean',
        ]);

        \App\Models\ParentStudent::create([
            'student_id'         => $validated['student_id'],
            'parent_user_id'     => $validated['parent_user_id'],
            'relationship'       => $validated['relationship'],
            'is_primary_contact' => $request->boolean('is_primary_contact', false),
            'can_pickup'         => true,
        ]);

        Alert::success('Success', 'Parent linked successfully.');
        return redirect()->back();
    }

    public function unlinkParent(\App\Models\ParentStudent $link)
    {
        $link->delete();
        Alert::success('Success', 'Parent unlinked.');
        return redirect()->back();
    }

    /* ── Import (stub) ── */
    public function showImportForm()
    {
        return view('admin.students.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        Alert::info('Import', 'Bulk import feature is coming soon.');
        return redirect()->route('admin.students.index');
    }

    public function downloadTemplate()
    {
        $columns = ['first_name','last_name','middle_name','email','phone','gender','date_of_birth','state_of_origin','lga','home_address','blood_group','genotype','medical_conditions','previous_school','admission_date'];
        $sample = ['John','Doe','Middle','john.doe@example.com','08012345678','Male','2005-01-15','Lagos','Ikeja','123 Main Street','O+','AA','None','ABC Secondary School','2024-09-01'];

        $callback = function () use ($columns, $sample) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, $sample);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
        ]);
    }

    /* ── Helpers ── */

    private function handlePhotoUpload(Request $request): ?string
    {
        if (!$request->hasFile('photo')) return null;

        $image = $request->file('photo');
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = 'students/photos/' . $filename;
        Storage::disk('public')->makeDirectory('students/photos');

        $manager = new ImageManager(new Driver());
        $manager->read($image->getRealPath())
                ->cover(300, 300)
                ->save(storage_path('app/public/' . $path));

        return $path;
    }

    private function generateAdmissionNumber(): string
    {
        $school  = SchoolProfile::first();
        $session = AcademicSession::getCurrent();
        $prefix  = ($school?->short_name ?? 'SCH') . '/' . $session->start_year . '/';

        $latest = Student::where('admission_number', 'like', $prefix . '%')
                         ->lockForUpdate()
                         ->orderBy('id', 'desc')
                         ->first();

        $sequence = $latest ? ((int) explode('/', $latest->admission_number)[2]) + 1 : 1;

        do {
            $adminNo = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Student::where('admission_number', $adminNo)->exists());

        return $adminNo;
    }
}
