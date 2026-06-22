@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <div>
                <h5 class="mb-0">Register Parent</h5>
                <p class="text-muted small mb-0">A login password will be auto-generated — copy and share with the parent after saving.</p>
            </div>
            <div class="list-btn">
                <a class="btn btn-light btn-sm" href="{{ route('admin.parents.index') }}">
                    <i class="fe fe-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

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

        <form action="{{ route('admin.parents.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                {{-- Personal Details --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fe fe-user me-2 text-muted"></i>Personal Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name') }}" placeholder="First name" required>
                                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name') }}" placeholder="Last name" required>
                                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                                           value="{{ old('middle_name') }}" placeholder="Middle name (optional)">
                                    @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
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
                                               value="{{ old('phone') }}"
                                               placeholder="e.g. 08012345678" required>
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <p class="text-muted small mt-1 mb-0">This number will be used for WhatsApp notifications.</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-muted small">(optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                        <input type="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="parent@example.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Link to Students --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">
                                <i class="fe fe-link me-2 text-muted"></i>Link to Children
                                <span class="text-muted fw-normal small ms-1">(optional — can also be done later from each student's profile)</span>
                            </h6>
                            <button type="button" id="add-student-btn" class="btn btn-sm btn-outline-primary">
                                <i class="fe fe-plus me-1"></i>Add Child
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="students-container">
                                {{-- Rows injected by JS --}}
                            </div>
                            <p id="no-students-msg" class="text-muted small mb-0">
                                No children added yet. Click <strong>Add Child</strong> to link this parent to one or more students.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Password Notice --}}
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-start gap-2 mb-0" role="alert">
                        <i class="fe fe-key mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Auto-generated password:</strong> A random 10-character password will be created on save.
                            It will be displayed <strong>once</strong> on the next screen — copy it immediately and give it to the parent.
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.parents.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-user-plus me-1"></i>Register Parent
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@php
$studentData = $students->map(fn($s) => [
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
            <small class="text-muted fw-semibold">Child #${idx}</small>
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
    noMsg.style.display = container.children.length === 0 ? 'block' : 'none';
}

document.getElementById('add-student-btn').addEventListener('click', function () {
    container.appendChild(createRow());
    noMsg.style.display = 'none';
});

// Restore old values after validation failure, or start empty
window.addEventListener('DOMContentLoaded', function () {
    if (OLD.length) {
        OLD.forEach(s => {
            container.appendChild(createRow(s.student_id, s.relationship, !!s.is_primary_contact));
        });
        noMsg.style.display = 'none';
    }
});
</script>

@endsection
