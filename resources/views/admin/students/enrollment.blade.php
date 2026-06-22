@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <div>
                <h5 class="mb-0">Student Enrollment</h5>
                <p class="text-muted small mb-0">
                    <span class="badge bg-primary">{{ $session->name }}</span>
                    <span class="badge bg-secondary">{{ $term->name }}</span>
                </p>
            </div>
            <div class="list-btn">
                <ul>
                    <li><a href="{{ route('admin.students.index') }}" class="btn btn-light"><i class="fe fe-users me-2"></i>All Students</a></li>
                </ul>
            </div>
        </div>

        {{-- Arm badges --}}
        @if($classArms->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small fw-semibold">Enrollment by Class:</span>
                    @foreach($classArms->groupBy(fn($a) => $a->classLevel->name) as $levelName => $arms)
                        @foreach($arms as $arm)
                            <a href="{{ route('admin.students.enrollment', ['class_arm_id' => $arm->id]) }}"
                               class="badge {{ $selectedArm?->id == $arm->id ? 'bg-primary' : 'bg-light text-dark border' }} text-decoration-none">
                                {{ $arm->full_name }}
                                <span class="ms-1">({{ $armEnrollmentCounts[$arm->id] ?? 0 }})</span>
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if(!$selectedArm)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fe fe-book-open text-muted" style="font-size:3rem"></i>
                    <h6 class="text-muted mt-3">Select a class arm above to manage enrollment</h6>
                </div>
            </div>
        @else

        <div class="row">
            {{-- Enrolled --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-users me-2 text-success"></i>Enrolled — {{ $selectedArm->full_name }}</h6>
                        <span class="badge bg-success">{{ $enrolledStudents->count() }}</span>
                    </div>
                    @if($enrolledStudents->isEmpty())
                        <div class="card-body text-center text-muted py-5">
                            <i class="fe fe-inbox" style="font-size:2rem"></i>
                            <p class="mt-2">No students enrolled yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead><tr><th>Student</th><th>Adm No.</th><th class="text-end">Action</th></tr></thead>
                                <tbody>
                                    @foreach($enrolledStudents as $student)
                                        @php $enrollment = $student->enrollments->where('term_id', $term->id)->where('is_active', true)->first(); @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $student->user->photo_url }}" class="rounded-circle border" width="30" height="30" style="object-fit:cover;">
                                                    <a href="{{ route('admin.students.show', $student) }}" class="text-dark text-decoration-none small fw-semibold">{{ $student->full_name }}</a>
                                                </div>
                                            </td>
                                            <td class="small"><code>{{ $student->admission_number }}</code></td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transferModal"
                                                        data-student-id="{{ $student->id }}" data-student-name="{{ $student->full_name }}"
                                                        data-current-arm="{{ $selectedArm->full_name }}" title="Transfer">
                                                        <i class="fe fe-shuffle"></i>
                                                    </button>
                                                    @if($enrollment)
                                                    <form action="{{ route('admin.students.enrollment.destroy', $enrollment) }}" method="POST" class="d-inline" onsubmit="return confirm('Unenroll {{ $student->full_name }}?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fe fe-user-minus"></i></button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Unenrolled --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-user-plus me-2 text-primary"></i>Unenrolled Students</h6>
                        <span class="badge bg-warning text-dark">{{ $unenrolledStudents->count() }}</span>
                    </div>
                    <div class="card-body border-bottom py-2">
                        <form action="{{ route('admin.students.enrollment') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="class_arm_id" value="{{ $selectedArm->id }}">
                            <div class="input-group input-group-sm flex-grow-1">
                                <span class="input-group-text bg-white"><i class="fe fe-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search..." value="{{ $search }}">
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Go</button>
                            @if($search)<a href="{{ route('admin.students.enrollment', ['class_arm_id' => $selectedArm->id]) }}" class="btn btn-sm btn-light">Clear</a>@endif
                        </form>
                    </div>

                    @if($unenrolledStudents->isEmpty())
                        <div class="card-body text-center text-muted py-5">
                            <i class="fe fe-check-circle text-success" style="font-size:2rem"></i>
                            <p class="mt-2">All active students are enrolled.</p>
                        </div>
                    @else
                        <form action="{{ route('admin.students.enrollment.store') }}" method="POST" id="bulkEnrollForm">
                            @csrf
                            <input type="hidden" name="class_arm_id" value="{{ $selectedArm->id }}">
                            <div class="table-responsive" style="max-height:460px;overflow-y:auto">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="sticky-top bg-light">
                                        <tr>
                                            <th width="36"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                            <th>Student</th>
                                            <th>Adm No.</th>
                                            <th>Gender</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unenrolledStudents as $student)
                                            <tr>
                                                <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-check"></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $student->user->photo_url }}" class="rounded-circle border" width="28" height="28" style="object-fit:cover;">
                                                        <a href="{{ route('admin.students.show', $student) }}" class="text-dark text-decoration-none small fw-semibold" target="_blank">{{ $student->full_name }}</a>
                                                    </div>
                                                </td>
                                                <td class="small"><code>{{ $student->admission_number }}</code></td>
                                                <td class="small text-muted">{{ $student->gender }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span class="text-muted small" id="selectedCount">0 selected</span>
                                <button type="submit" class="btn btn-primary btn-sm" id="enrollBtn" disabled>
                                    <i class="fe fe-user-plus me-1"></i>Enroll Selected
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Transfer Modal --}}
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Transfer Student</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="transferForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Transfer <strong id="transferStudentName"></strong> from <span class="badge bg-secondary" id="transferCurrentArm"></span></p>
                    <div class="form-group">
                        <label>New Class Arm <span class="text-danger">*</span></label>
                        <select name="new_class_arm_id" class="form-control select" required>
                            <option value="">— Select —</option>
                            @foreach($classArms->groupBy(fn($a) => $a->classLevel->name) as $levelName => $arms)
                                <optgroup label="{{ $levelName }}">
                                    @foreach($arms as $arm)
                                        @if(!$selectedArm || $arm->id !== $selectedArm->id)
                                            <option value="{{ $arm->id }}">{{ $arm->full_name }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-shuffle me-1"></i>Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);
            updateCount();
        });
        document.querySelectorAll('.student-check').forEach(cb => cb.addEventListener('change', updateCount));
    }
    function updateCount() {
        const checked = document.querySelectorAll('.student-check:checked').length;
        const all = document.querySelectorAll('.student-check').length;
        const btn = document.getElementById('enrollBtn');
        const label = document.getElementById('selectedCount');
        if (btn) btn.disabled = checked === 0;
        if (label) label.textContent = checked + ' selected';
        if (checkAll) { checkAll.indeterminate = checked > 0 && checked < all; checkAll.checked = all > 0 && checked === all; }
    }

    const transferModal = document.getElementById('transferModal');
    if (transferModal) {
        transferModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('transferStudentName').textContent = btn.dataset.studentName;
            document.getElementById('transferCurrentArm').textContent = btn.dataset.currentArm;
            document.getElementById('transferForm').action = '{{ url("admin/students") }}/' + btn.dataset.studentId + '/transfer';
        });
    }
});
</script>
@endsection
