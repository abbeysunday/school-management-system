@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Daily Attendance</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Attendance</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('teacher.attendance.report') }}" class="btn btn-primary">
                        <i class="ti ti-chart-bar me-1"></i> Monthly Report
                    </a>
                </div>
            </div>
        </div>

        {{-- Class Arm & Date Selector --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.attendance.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Class Arm</label>
                        <select name="class_arm_id" class="form-select" onchange="this.form.submit()">
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $classArm->id == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <div class="alert {{ $isEditMode ? 'alert-info' : 'alert-warning' }} mb-0 py-2">
                            <i class="ti ti-{{ $isEditMode ? 'info-circle' : 'clock' }} me-1"></i>
                            {{ $isEditMode ? 'Editing existing attendance for ' . date('d M Y', strtotime($date)) : 'New attendance for ' . date('d M Y', strtotime($date)) }}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success mb-1">{{ $dateStats['present'] }}</h4>
                        <span class="text-muted">Present</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-danger mb-1">{{ $dateStats['absent'] }}</h4>
                        <span class="text-muted">Absent</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning mb-1">{{ $dateStats['late'] }}</h4>
                        <span class="text-muted">Late</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info mb-1">{{ $dateStats['sick'] }}</h4>
                        <span class="text-muted">Sick</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-secondary mb-1">{{ $dateStats['excused'] }}</h4>
                        <span class="text-muted">Excused</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1">{{ $dateStats['total'] }}</h4>
                        <span class="text-muted">Total</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Form --}}
        <form action="{{ route('teacher.attendance.store') }}" method="POST" id="attendanceForm">
            @csrf
            <input type="hidden" name="class_arm_id" value="{{ $classArm->id }}">
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-users me-2"></i>
                        {{ $classArm->classLevel->name }}{{ $classArm->arm }} — {{ date('d M Y', strtotime($date)) }}
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success" onclick="setAll('Present')">All Present</button>
                        <button type="button" class="btn btn-outline-danger" onclick="setAll('Absent')">All Absent</button>
                        <button type="button" class="btn btn-outline-warning" onclick="setAll('Late')">All Late</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:50px;">S/N</th>
                                    <th>Student</th>
                                    <th>Adm. No</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Late</th>
                                    <th class="text-center">Sick</th>
                                    <th class="text-center">Excused</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceData as $i => $row)
                                    <tr class="attendance-row" data-student="{{ $row['student_id'] }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $row['photo'] }}" class="rounded-circle me-2" style="width:36px;height:36px;object-fit:cover;">
                                                <div>
                                                    <div class="fw-semibold">{{ $row['name'] }}</div>
                                                    @if($row['marked_at'])
                                                        <span class="text-muted" style="font-size:10px;">Marked at {{ $row['marked_at'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-monospace">{{ $row['admission_no'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input type="radio" name="statuses[{{ $row['student_id'] }}]" value="Present" class="form-check-input status-radio" id="present_{{ $row['student_id'] }}" {{ $row['status'] == 'Present' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="present_{{ $row['student_id'] }}"><span class="badge bg-success">P</span></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input type="radio" name="statuses[{{ $row['student_id'] }}]" value="Absent" class="form-check-input status-radio" id="absent_{{ $row['student_id'] }}" {{ $row['status'] == 'Absent' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="absent_{{ $row['student_id'] }}"><span class="badge bg-danger">A</span></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input type="radio" name="statuses[{{ $row['student_id'] }}]" value="Late" class="form-check-input status-radio" id="late_{{ $row['student_id'] }}" {{ $row['status'] == 'Late' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="late_{{ $row['student_id'] }}"><span class="badge bg-warning">L</span></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input type="radio" name="statuses[{{ $row['student_id'] }}]" value="Sick" class="form-check-input status-radio" id="sick_{{ $row['student_id'] }}" {{ $row['status'] == 'Sick' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sick_{{ $row['student_id'] }}"><span class="badge bg-info">S</span></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input type="radio" name="statuses[{{ $row['student_id'] }}]" value="Excused" class="form-check-input status-radio" id="excused_{{ $row['student_id'] }}" {{ $row['status'] == 'Excused' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="excused_{{ $row['student_id'] }}"><span class="badge bg-secondary">E</span></label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="remarks[{{ $row['student_id'] }}]" class="form-control form-control-sm" value="{{ $row['remarks'] }}" placeholder="Optional remark">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ $isEditMode ? 'Update Attendance' : 'Save Attendance' }}
                    </button>
                    <a href="{{ route('teacher.attendance.index', ['class_arm_id' => $classArm->id, 'date' => $date]) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
function setAll(status) {
    document.querySelectorAll('.status-radio[value="' + status + '"]').forEach(radio => {
        radio.checked = true;
    });
    highlightRows();
}

function highlightRows() {
    document.querySelectorAll('.attendance-row').forEach(row => {
        const checked = row.querySelector('.status-radio:checked');
        row.classList.remove('table-success', 'table-danger', 'table-warning', 'table-info', 'table-secondary');
        if (checked) {
            const map = { 'Present': 'table-success', 'Absent': 'table-danger', 'Late': 'table-warning', 'Sick': 'table-info', 'Excused': 'table-secondary' };
            row.classList.add(map[checked.value]);
        }
    });
}

document.querySelectorAll('.status-radio').forEach(radio => {
    radio.addEventListener('change', highlightRows);
});

highlightRows();
</script>

@endsection
