@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">CA Score Entry</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('teacher.ca-scores.index') }}">Score Entry</a></li>
                        <li class="breadcrumb-item active">CA Entry</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small me-2">
                            <i class="ti ti-school me-1"></i>{{ $armSubject->classArm->full_name }}
                        </span>
                        <span class="text-muted small">
                            <i class="ti ti-book me-1"></i>{{ $armSubject->subject->name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ti ti-info-circle me-2 fs-5"></i>
            <div>
                Enter scores for each student. Maximum score per component is shown in parentheses.
                <strong>Scores auto-save</strong> when you click the Save button.
            </div>
        </div>

        {{-- Success / Error Alerts --}}
        <div id="successAlert" class="alert alert-success d-none">
            <i class="ti ti-check me-1"></i><span id="successMessage"></span>
        </div>
        <div id="errorAlert" class="alert alert-danger d-none">
            <i class="ti ti-alert-triangle me-1"></i><span id="errorMessage"></span>
        </div>

        {{-- Score Entry Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-clipboard-list me-2"></i>Continuous Assessment Scores</h5>
                <div class="d-flex gap-2">
                    <span class="text-muted small">
                        <i class="ti ti-users me-1"></i>{{ count($enrollments) }} Students
                    </span>
                    <span class="text-muted small">
                        <i class="ti ti-calendar me-1"></i>{{ $term->name }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="caScoreTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:50px;">S/N</th>
                                <th style="min-width:200px;">Student</th>
                                <th style="min-width:100px;">Admission No</th>
                                @foreach($caConfigs as $config)
                                    <th class="text-center" style="min-width:100px;">
                                        {{ $config->component_name }}
                                        <span class="d-block text-muted" style="font-size:10px;">(Max: {{ $config->max_score }})</span>
                                    </th>
                                @endforeach
                                <th class="text-center" style="min-width:80px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $i => $enrollment)
                                @php $student = $enrollment->student; @endphp
                                <tr data-student-id="{{ $student->id }}">
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($student->user->photo)
                                                <img src="{{ asset('storage/' . $student->user->photo) }}" class="rounded-circle" width="32" height="32" alt="">
                                            @else
                                                <span class="avatar avatar-sm bg-primary-soft rounded-circle">{{ substr($student->user->first_name, 0, 1) }}</span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $student->user->full_name }}</div>
                                                <small class="text-muted">{{ $student->gender }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $student->admission_number }}</code></td>
                                    @foreach($caConfigs as $config)
                                        @php
                                            $score = $scoreMatrix[$student->id][$config->id] ?? '';
                                            $cellKey = "s{$student->id}_c{$config->id}";
                                        @endphp
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   class="form-control form-control-sm text-center ca-input"
                                                   data-student-id="{{ $student->id }}"
                                                   data-config-id="{{ $config->id }}"
                                                   data-max="{{ $config->max_score }}"
                                                   value="{{ $score !== '' ? number_format($score, 2) : '' }}"
                                                   min="0"
                                                   max="{{ $config->max_score }}"
                                                   step="0.01"
                                                   placeholder="0"
                                                   onchange="validateScore(this)">
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-bold text-primary" id="total-{{ $student->id }}">
                                        {{ number_format(array_sum(array_map(fn($v) => (float) $v, $scoreMatrix[$student->id] ?? [])), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="ti ti-info-circle me-1"></i>
                        All scores are validated against maximum values.
                    </span>
                    <button type="button" class="btn btn-primary" onclick="saveCaScores()" id="saveBtn">
                        <i class="ti ti-device-floppy me-1"></i> Save All Scores
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function validateScore(input) {
    const max = parseFloat(input.dataset.max);
    let val = parseFloat(input.value);
    if (isNaN(val)) val = 0;
    if (val > max) {
        input.value = max;
        val = max;
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 1500);
    }
    if (val < 0) {
        input.value = 0;
        val = 0;
    }
    // Recalculate row total
    const studentId = input.dataset.studentId;
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const inputs = row.querySelectorAll('.ca-input');
    let total = 0;
    inputs.forEach(inp => {
        const v = parseFloat(inp.value);
        if (!isNaN(v)) total += v;
    });
    document.getElementById('total-' + studentId).textContent = total.toFixed(2);
}

function saveCaScores() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2 me-1 spin"></i> Saving...';

    const scores = [];
    document.querySelectorAll('.ca-input').forEach(input => {
        const studentId = input.dataset.studentId;
        const configId = input.dataset.configId;
        const val = input.value.trim();
        if (val !== '') {
            scores.push({
                student_id: parseInt(studentId),
                config_id: parseInt(configId),
                score: parseFloat(val) || 0
            });
        }
    });

    fetch('{{ route("teacher.ca-scores.store", $armSubject->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ scores: scores })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
        } else {
            showError(data.message || 'Save failed.');
        }
    })
    .catch(err => {
        showError('Network error. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Save All Scores';
    });
}

function showSuccess(msg) {
    const el = document.getElementById('successAlert');
    document.getElementById('successMessage').textContent = msg;
    el.classList.remove('d-none');
    document.getElementById('errorAlert').classList.add('d-none');
    setTimeout(() => el.classList.add('d-none'), 4000);
}

function showError(msg) {
    const el = document.getElementById('errorAlert');
    document.getElementById('errorMessage').textContent = msg;
    el.classList.remove('d-none');
    document.getElementById('successAlert').classList.add('d-none');
}
</script>

<style>
.ca-input { font-size: 13px; font-weight: 600; }
.ca-input:focus { background: #e8f4f8; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@endsection
