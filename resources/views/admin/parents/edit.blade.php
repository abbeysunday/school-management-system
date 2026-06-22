@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <div>
                <h5 class="mb-0">Edit Parent</h5>
                <p class="text-muted small mb-0">{{ $parent->full_name }}</p>
            </div>
            <div class="list-btn">
                <a class="btn btn-light btn-sm" href="{{ route('admin.parents.index') }}">
                    <i class="fe fe-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-circle me-2"></i>
                <strong>Please fix the errors below:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">

            {{-- LEFT: Edit form --}}
            <div class="col-lg-8">
                <form action="{{ route('admin.parents.update', $parent) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Personal Details --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fe fe-user me-2 text-muted"></i>Personal Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name', $parent->first_name) }}" required>
                                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name', $parent->last_name) }}" required>
                                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name"
                                           class="form-control @error('middle_name') is-invalid @enderror"
                                           value="{{ old('middle_name', $parent->middle_name) }}"
                                           placeholder="(optional)">
                                    @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Details --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fe fe-phone me-2 text-muted"></i>Contact Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        WhatsApp / Phone Number <span class="text-danger">*</span>
                                        <span class="badge bg-info text-white ms-1" style="font-size:.65rem">Primary Contact</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fe fe-phone"></i></span>
                                        <input type="text" name="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', $parent->phone) }}" required>
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-muted small">(optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                        <input type="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $parent->email) }}"
                                               placeholder="parent@example.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Add New Children --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h6 class="mb-0"><i class="fe fe-link me-2 text-muted"></i>Link Additional Children</h6>
                            <button type="button" id="add-student-btn" class="btn btn-sm btn-outline-primary"
                                    @if($availableStudents->isEmpty()) disabled @endif>
                                <i class="fe fe-plus me-1"></i>Add Child
                            </button>
                        </div>
                        <div class="card-body">
                            @if($availableStudents->isEmpty())
                                <p class="text-muted small mb-0">All active students are already linked to this parent.</p>
                            @else
                                <div id="students-container"></div>
                                <p id="no-students-msg" class="text-muted small mb-0">
                                    Click <strong>Add Child</strong> to link additional students.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.parents.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- RIGHT: Linked students + password --}}
            <div class="col-lg-4">

                {{-- Currently linked children --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-users me-2 text-muted"></i>Linked Children</h6>
                        <span class="badge bg-secondary">{{ $parent->parentStudents->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @forelse($parent->parentStudents as $link)
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <div>
                                    <p class="mb-0 fw-semibold small">
                                        {{ $link->student->user->full_name ?? '—' }}
                                        @if($link->is_primary_contact)
                                            <span class="badge bg-primary ms-1" style="font-size:.6rem">Primary</span>
                                        @endif
                                    </p>
                                    <span class="text-muted" style="font-size:.8rem">
                                        {{ $link->relationship }}
                                        &bull; {{ $link->student->admission_number ?? '' }}
                                    </span>
                                </div>
                                <form action="{{ route('admin.parents.unlink', $link) }}" method="POST"
                                      onsubmit="return confirm('Unlink {{ addslashes($link->student->user->full_name ?? '') }} from this parent?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Unlink">
                                        <i class="fe fe-x"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-muted small px-3 py-2 mb-0">No students linked yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Password management --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="fe fe-key me-2 text-muted"></i>Login Password</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Generate a new password for this parent. The new password will be shown <strong>once</strong> on
                            the parents list page — copy and share it with the parent immediately.
                        </p>
                        <form action="{{ route('admin.parents.regenerate-password', $parent) }}" method="POST"
                              onsubmit="return confirm('Generate a new password for {{ addslashes($parent->full_name) }}? Their current password will stop working.')">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fe fe-refresh-cw me-1"></i>Regenerate Password
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@php
$studentData = $availableStudents->map(fn($s) => [
    'id'   => $s->id,
    'name' => $s->user->full_name . ' (' . $s->admission_number . ')',
]);
$relationships = ['Father','Mother','Guardian','Uncle','Aunt','Sibling','Others'];
$oldStudents   = old('students', []);
@endphp

<script>
const STUDENTS = @json($studentData);
const RELS = @json($relationships);
const OLD = @json($oldStudents);

let rowIndex = 0;
const container = document.getElementById('students-container');
const noMsg     = document.getElementById('no-students-msg');

function buildOptions(list, valueKey, labelKey, selected) {
    return list.map(item => {
        const val = item[valueKey] ?? item;
        const lbl = item[labelKey] ?? item;
        const sel = String(val) === String(selected) ? ' selected' : '';
        return `<option value="${val}"${sel}>${lbl}</option>`;
    }).join('');
}

function createRow(defaultStudentId = '', defaultRel = 'Guardian', defaultPrimary = false) {
    const idx = ++rowIndex;
    const div = document.createElement('div');
    div.className = 'border rounded p-3 mb-2 bg-white student-row';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted fw-semibold">New Child #${idx}</small>
            <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="removeRow(this)">
                <i class="fe fe-trash-2"></i> Remove
            </button>
        </div>
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Student <span class="text-danger">*</span></label>
                <select name="students[${idx}][student_id]" class="form-control" required>
                    <option value="">— Select student —</option>
                    ${buildOptions(STUDENTS, 'id', 'name', defaultStudentId)}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Relationship <span class="text-danger">*</span></label>
                <select name="students[${idx}][relationship]" class="form-control" required>
                    ${buildOptions(RELS, null, null, defaultRel)}
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input type="checkbox" name="students[${idx}][is_primary_contact]"
                           value="1" class="form-check-input" id="primary_${idx}"
                           ${defaultPrimary ? 'checked' : ''}>
                    <label class="form-check-label small" for="primary_${idx}">Primary contact</label>
                </div>
            </div>
        </div>`;
    return div;
}

function removeRow(btn) {
    btn.closest('.student-row').remove();
    if (noMsg) noMsg.style.display = container.children.length === 0 ? 'block' : 'none';
}

const addBtn = document.getElementById('add-student-btn');
if (addBtn) {
    addBtn.addEventListener('click', function () {
        if (!container) return;
        container.appendChild(createRow());
        if (noMsg) noMsg.style.display = 'none';
    });
}

window.addEventListener('DOMContentLoaded', function () {
    if (OLD.length && container) {
        OLD.forEach(s => {
            container.appendChild(createRow(s.student_id, s.relationship, !!s.is_primary_contact));
        });
        if (noMsg) noMsg.style.display = 'none';
    }
});
</script>

@endsection
