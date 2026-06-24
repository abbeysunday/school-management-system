
@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Timetable Builder</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.timetable.periods') }}">
                            <i class="fe fe-clock me-2"></i>Periods
                        </a>
                    </li>
                    @if($selectedArm)
                        <li>
                            <a class="btn btn-success" href="{{ route('admin.timetable.print', ['arm_id' => $armId]) }}" target="_blank">
                                <i class="fe fe-printer me-2"></i>Print PDF
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Class Arm Selector --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.timetable.builder') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Class Arm <span class="text-danger">*</span></label>
                        <select name="arm_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">— Select Class Arm —</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $armId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($selectedArm)
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0 py-2">
                                <i class="fe fe-info me-1"></i>
                                Building for <strong>{{ $selectedArm->full_name }}</strong> — {{ $session->name }}
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#copyModal">
                                <i class="fe fe-copy me-1"></i> Copy From Another Class
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if($selectedArm)
            {{-- Alerts --}}
            <div id="conflictAlert" class="alert alert-danger d-none">
                <i class="fe fe-alert-triangle me-1"></i>
                <span id="conflictMessage"></span>
            </div>
            <div id="successAlert" class="alert alert-success d-none">
                <i class="fe fe-check-circle me-1"></i>
                <span id="successMessage"></span>
            </div>

            {{-- Timetable Grid --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered timetable-grid mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width:120px;background:#1a5f2a;color:#fff;">Period / Day</th>
                                    @foreach($days as $day)
                                        <th class="text-center" style="min-width:160px;background:#1a5f2a;color:#fff;">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periods as $period)
                                    <tr>
                                        <td class="fw-semibold" style="background:#f8f9fa;white-space:nowrap;">
                                            <div>{{ $period->period_name }}</div>
                                            <div class="text-muted" style="font-size:10px;">{{ $period->start_time }} - {{ $period->end_time }}</div>
                                            @if(!$period->isTeaching())
                                                <span class="badge bg-{{ $period->period_type == 'Break' ? 'warning' : ($period->period_type == 'Assembly' ? 'info' : ($period->period_type == 'Games' ? 'success' : 'secondary')) }}" style="font-size:9px;">
                                                    {{ $period->period_type }}
                                                </span>
                                            @endif
                                        </td>
                                        @foreach($days as $day)
                                            @php
                                                $entry = $timetableGrid[$day][$period->id] ?? null;
                                                $cellId = "cell-{$day}-{$period->id}";
                                            @endphp
                                            <td class="timetable-cell p-2" style="min-width:160px;vertical-align:top;cursor:pointer;"
                                                onclick="openEditModal('{{ $day }}', {{ $period->id }}, '{{ $period->period_name }}', '{{ $period->isTeaching() ? '1' : '0' }}', {{ $entry?->id ?? 'null' }}, {{ $entry ? json_encode(['subject_id' => $entry->subject_id, 'teacher_id' => $entry->teacher_id, 'room' => $entry->room]) : 'null' }})"
                                                id="{{ $cellId }}">
                                                @if($entry)
                                                    <div class="timetable-entry bg-light border rounded p-2">
                                                        <div class="fw-bold text-primary" style="font-size:12px;">{{ $entry->subject?->name ?? '—' }}</div>
                                                        @if($entry->teacher)
                                                            <div class="text-muted" style="font-size:11px;">
                                                                <i class="fe fe-user me-1"></i>{{ $entry->teacher->user->full_name }}
                                                            </div>
                                                        @endif
                                                        @if($entry->room)
                                                            <div class="text-muted" style="font-size:10px;">
                                                                <i class="fe fe-map-pin me-1"></i>{{ $entry->room }}
                                                            </div>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-1" style="font-size:10px;"
                                                            onclick="event.stopPropagation(); deleteEntry({{ $entry->id }})">
                                                            <i class="fe fe-x"></i> Remove
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="text-center text-muted py-3">
                                                        <i class="fe fe-plus-circle" style="font-size:18px;"></i>
                                                        <div style="font-size:10px;">Click to add</div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        <span><i class="fe fe-plus-circle me-1 text-muted"></i> Click cell to add/edit</span>
                        <span><i class="fe fe-x me-1 text-danger"></i> Click remove to delete</span>
                        <span class="badge bg-primary">Teaching</span>
                        <span class="badge bg-warning">Break</span>
                        <span class="badge bg-info">Assembly</span>
                        <span class="badge bg-success">Games</span>
                        <span class="badge bg-secondary">Closing</span>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fe fe-grid text-muted" style="font-size:48px;"></i>
                    <h5 class="mt-3">Select a Class Arm</h5>
                    <p class="text-muted">Choose a class arm above to start building the timetable.</p>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Edit/Add Modal --}}
<div class="modal fade" id="timetableModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Edit Timetable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="timetableForm">
                    @csrf
                    <input type="hidden" id="modalDay" name="day_of_week">
                    <input type="hidden" id="modalPeriodId" name="period_id">
                    <input type="hidden" name="class_arm_id" value="{{ $armId }}">
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    {{-- Hidden fields for disabled inputs to ensure they submit --}}
                    <input type="hidden" id="hiddenSubjectId" name="subject_id" value="">
                    <input type="hidden" id="hiddenTeacherId" name="teacher_id" value="">
                    <input type="hidden" id="hiddenRoom" name="room" value="">

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select id="modalSubject" class="form-select">
                            <option value="">— Select Subject —</option>
                            @foreach($armSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <select id="modalTeacher" class="form-select">
                            <option value="">— Select Teacher —</option>
                            @foreach($armTeachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <input type="text" id="modalRoom" class="form-control" placeholder="e.g. Lab 1, Room 204">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEntry()">
                    <i class="fe fe-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Copy Modal --}}
<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.timetable.copy') }}" method="POST">
                @csrf
                <input type="hidden" name="from_class_arm_id" value="{{ $armId }}">
                <input type="hidden" name="session_id" value="{{ $session->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Timetable</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Copy current timetable to another class arm.</p>
                    <div class="mb-3">
                        <label class="form-label">Target Class Arm</label>
                        <select name="to_class_arm_id" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach($classArms as $arm)
                                @if($arm->id != $armId)
                                    <option value="{{ $arm->id }}">{{ $arm->classLevel->name }}{{ $arm->arm }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let timetableModal;
let currentDay, currentPeriodId, currentEntryId;

// Initialize modal after Bootstrap JS loads
document.addEventListener('DOMContentLoaded', function() {
    timetableModal = new bootstrap.Modal(document.getElementById('timetableModal'));
});

// Keep these functions GLOBAL so onclick can find them
function openEditModal(day, periodId, periodName, isTeaching, entryId, entryData) {
    currentDay = day;
    currentPeriodId = periodId;
    currentEntryId = entryId;
    document.getElementById('modalTitle').textContent = day + ' — ' + periodName;
    document.getElementById('modalDay').value = day;
    document.getElementById('modalPeriodId').value = periodId;

    const subjectSelect = document.getElementById('modalSubject');
    const teacherSelect = document.getElementById('modalTeacher');
    const roomInput = document.getElementById('modalRoom');
    const hiddenSubject = document.getElementById('hiddenSubjectId');
    const hiddenTeacher = document.getElementById('hiddenTeacherId');
    const hiddenRoom = document.getElementById('hiddenRoom');

    // Reset all fields
    subjectSelect.value = '';
    teacherSelect.value = '';
    roomInput.value = '';
    hiddenSubject.value = '';
    hiddenTeacher.value = '';
    hiddenRoom.value = '';

    // Populate if editing existing entry
    if (entryData) {
        subjectSelect.value = entryData.subject_id || '';
        teacherSelect.value = entryData.teacher_id || '';
        roomInput.value = entryData.room || '';
        hiddenSubject.value = entryData.subject_id || '';
        hiddenTeacher.value = entryData.teacher_id || '';
        hiddenRoom.value = entryData.room || '';
    }

    // Enable/disable based on period type — ALWAYS re-enable first
    subjectSelect.disabled = false;
    teacherSelect.disabled = false;
    roomInput.disabled = false;

    if (isTeaching !== '1') {
        subjectSelect.disabled = true;
        teacherSelect.disabled = true;
        roomInput.disabled = true;
    }

    timetableModal.show();
}

function saveEntry() {
    const subjectSelect = document.getElementById('modalSubject');
    const teacherSelect = document.getElementById('modalTeacher');
    const roomInput = document.getElementById('modalRoom');
    const hiddenSubject = document.getElementById('hiddenSubjectId');
    const hiddenTeacher = document.getElementById('hiddenTeacherId');
    const hiddenRoom = document.getElementById('hiddenRoom');

    // Sync visible inputs to hidden fields before submit
    // (disabled inputs don't submit, but hidden inputs do)
    hiddenSubject.value = subjectSelect.value;
    hiddenTeacher.value = teacherSelect.value;
    hiddenRoom.value = roomInput.value;

    const form = document.getElementById('timetableForm');
    const formData = new FormData(form);
    formData.append('day_of_week', currentDay);
    formData.append('period_id', currentPeriodId);

    fetch('{{ route('admin.timetable.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            timetableModal.hide();
            setTimeout(() => location.reload(), 500);
        } else {
            showConflict(data.message || 'Unknown error');
        }
    })
    .catch(err => {
        showConflict('Network error. Please try again.');
    });
}

function deleteEntry(entryId) {
    if (!confirm('Remove this timetable entry?')) return;

    fetch('/admin/timetable/' + entryId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => location.reload(), 500);
        }
    });
}

function showConflict(msg) {
    const el = document.getElementById('conflictAlert');
    document.getElementById('conflictMessage').textContent = msg;
    el.classList.remove('d-none');
    document.getElementById('successAlert').classList.add('d-none');
}

function showSuccess(msg) {
    const el = document.getElementById('successAlert');
    document.getElementById('successMessage').textContent = msg;
    el.classList.remove('d-none');
    document.getElementById('conflictAlert').classList.add('d-none');
}
</script>

<style>
.timetable-grid { border-collapse: separate; border-spacing: 0; }
.timetable-grid th, .timetable-grid td { border: 1px solid #dee2e6; }
.timetable-cell:hover { background: #e8f4f8; }
.timetable-entry { transition: all 0.2s; }
.timetable-entry:hover { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>

@endsection
