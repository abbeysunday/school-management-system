@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <div>
                <h5 class="mb-0">Teacher Profile</h5>
                <p class="text-muted small mb-0">{{ $teacher->staff_id }}</p>
            </div>
            <div class="list-btn">
                <ul class="d-flex gap-2 mb-0">
                    <li>
                        <a class="btn btn-light btn-sm" href="{{ route('admin.teachers.index') }}">
                            <i class="fe fe-arrow-left me-1"></i>Back
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.teachers.assignments.edit', $teacher) }}">
                            <i class="fe fe-book-open me-1"></i>Manage Assignments
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.teachers.edit', $teacher) }}">
                            <i class="fe fe-edit me-1"></i>Edit Profile
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: Bio --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center mb-4">
                    <div class="card-body pb-4">
                        <img src="{{ $teacher->user->photo_url }}" class="rounded-circle mb-3 border" width="120" height="120" style="object-fit:cover; border-width: 3px !important;" alt="Photo">
                        <h5 class="mb-1">{{ $teacher->full_name }}</h5>
                        <p class="text-muted small mb-2">{{ $teacher->qualification }} {{ $teacher->specialization ? '('.$teacher->specialization.')' : '' }}</p>

                        @if($teacher->is_active)
                            <span class="badge bg-success mb-3">Active Staff</span>
                        @else
                            <span class="badge bg-secondary mb-3">Inactive</span>
                        @endif

                        <hr class="my-3">
                        <div class="text-start small">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fe fe-phone me-2"></i>Phone</span>
                                <span class="fw-semibold">{{ $teacher->user->phone }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fe fe-mail me-2"></i>Email</span>
                                <span>{{ $teacher->user->email }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fe fe-calendar me-2"></i>Joined</span>
                                <span>{{ $teacher->employment_date?->format('M d, Y') ?? '—' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted"><i class="fe fe-briefcase me-2"></i>Type</span>
                                <span>{{ $teacher->employment_type }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent"><h6 class="mb-0">Next of Kin</h6></div>
                    <div class="card-body small">
                        @if($teacher->next_of_kin_name)
                            <p class="mb-1 fw-semibold">{{ $teacher->next_of_kin_name }} ({{ $teacher->next_of_kin_relationship }})</p>
                            <p class="mb-0 text-muted"><i class="fe fe-phone me-2"></i>{{ $teacher->next_of_kin_phone }}</p>
                        @else
                            <p class="mb-0 text-muted">No record available.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Academic Data --}}
            <div class="col-lg-8">

                {{-- Form Teacher Assignment Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-star text-warning me-2"></i>Form Teacher Roles</h6>
                        <span class="badge bg-light text-dark border">{{ $session->name }} Session</span>
                    </div>
                    <div class="card-body p-0">
                        @if($teacher->classArmTeachers->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Class Arm</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teacher->classArmTeachers as $role)
                                            <tr>
                                                <td class="fw-semibold">{{ $role->classArm->full_name }}</td>
                                                <td><span class="badge bg-success-light text-success border border-success">{{ $role->role }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted small">
                                Not assigned as a Form Teacher for the current session.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Subject Assignment Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-book-open text-primary me-2"></i>Subject Workload</h6>
                        <span class="badge bg-primary">{{ $teacher->armSubjectAssignments->count() }} Subjects</span>
                    </div>
                    <div class="card-body p-0">
                        @if($teacher->armSubjectAssignments->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Class Arm</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teacher->armSubjectAssignments as $assignment)
                                            <tr>
                                                <td class="fw-semibold">{{ $assignment->armSubject->subject->name }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $assignment->armSubject->classArm->full_name }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-5 text-center text-muted small">
                                <i class="fe fe-book-open d-block mb-3" style="font-size: 2rem;"></i>
                                No subjects assigned for the current session.<br>
                                <a href="{{ route('admin.teachers.assignments.edit', $teacher) }}" class="btn btn-sm btn-outline-primary mt-3">Assign Subjects Now</a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
