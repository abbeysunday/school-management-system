<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParentRequest;
use App\Http\Requests\Admin\UpdateParentRequest;
use App\Models\ParentStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'parent')
                     ->with('parentStudents.student.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $parents = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::where('role', 'parent')->count(),
            'linked'   => User::where('role', 'parent')->whereHas('parentStudents')->count(),
            'unlinked' => User::where('role', 'parent')->whereDoesntHave('parentStudents')->count(),
        ];

        return view('admin.parents.index', compact('parents', 'stats'));
    }

    public function create()
    {
        $students = Student::with('user')
                           ->whereHas('user', fn($q) => $q->where('is_active', true))
                           ->get();
        return view('admin.parents.create', compact('students'));
    }

    public function store(StoreParentRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();

            $plainPassword = Str::random(10);

            $parent = User::create([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'phone'       => $validated['phone'],
                'email'       => $validated['email'] ?? null,
                'password'    => Hash::make($plainPassword),
                'role'        => 'parent',
                'is_active'   => true,
            ]);

            $this->syncStudentLinks($parent->id, $validated['students'] ?? []);

            DB::commit();

            return redirect()->route('admin.parents.index')
                ->with('success', "Parent account created for {$parent->full_name}.")
                ->with('generated_password', $plainPassword)
                ->with('parent_name', $parent->full_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function edit(User $parent)
    {
        abort_if($parent->role !== 'parent', 404);
        $parent->load('parentStudents.student.user');

        $linkedStudentIds = $parent->parentStudents->pluck('student_id');
        $availableStudents = Student::with('user')
                                    ->whereNotIn('id', $linkedStudentIds)
                                    ->whereHas('user', fn($q) => $q->where('is_active', true))
                                    ->get();

        return view('admin.parents.edit', compact('parent', 'availableStudents'));
    }

    public function update(UpdateParentRequest $request, User $parent)
    {
        abort_if($parent->role !== 'parent', 404);

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            $parent->update([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'phone'       => $validated['phone'],
                'email'       => $validated['email'] ?? null,
            ]);

            // Changed to updateOrCreate to allow updating relationship type or primary contact status
            foreach ($validated['students'] ?? [] as $link) {
                if (empty($link['student_id'])) continue;
                ParentStudent::updateOrCreate(
                    ['parent_user_id' => $parent->id, 'student_id' => $link['student_id']],
                    [
                        'relationship'       => $link['relationship'],
                        'is_primary_contact' => isset($link['is_primary_contact']),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.parents.edit', $parent)
                ->with('success', 'Parent updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function regeneratePassword(User $parent)
    {
        abort_if($parent->role !== 'parent', 404);

        $plainPassword = Str::random(10);
        $parent->update(['password' => Hash::make($plainPassword)]);

        return redirect()->route('admin.parents.index')
            ->with('success', "Password regenerated for {$parent->full_name}.")
            ->with('generated_password', $plainPassword)
            ->with('parent_name', $parent->full_name);
    }

    public function linkToStudent(Request $request)
    {
        $request->validate([
            'parent_user_id'     => ['required', 'exists:users,id'],
            'student_id'         => ['required', 'exists:students,id'],
            'relationship'       => ['required', 'in:Father,Mother,Guardian,Uncle,Aunt,Sibling,Others'],
            'is_primary_contact' => ['nullable', 'boolean'],
        ]);

        $exists = ParentStudent::where('parent_user_id', $request->parent_user_id)
                               ->where('student_id', $request->student_id)
                               ->exists();

        if ($exists) {
            return back()->with('error', 'This parent is already linked to that student.');
        }

        ParentStudent::create([
            'parent_user_id'     => $request->parent_user_id,
            'student_id'         => $request->student_id,
            'relationship'       => $request->relationship,
            'is_primary_contact' => $request->boolean('is_primary_contact'),
        ]);

        return back()->with('success', 'Parent linked to student successfully.');
    }

    public function unlink(ParentStudent $parentStudent)
    {
        $parentStudent->delete();
        return back()->with('success', 'Parent unlinked from student.');
    }

    private function syncStudentLinks(int $parentId, array $rows): void
    {
        $seen = [];
        foreach ($rows as $link) {
            if (empty($link['student_id'])) continue;
            $sid = (int) $link['student_id'];
            if (in_array($sid, $seen)) continue;
            $seen[] = $sid;

            ParentStudent::create([
                'parent_user_id'     => $parentId,
                'student_id'         => $sid,
                'relationship'       => $link['relationship'],
                'is_primary_contact' => isset($link['is_primary_contact']),
            ]);
        }
    }
}
