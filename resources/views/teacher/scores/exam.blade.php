@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Exam Score Entry</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('teacher.ca-scores.index') }}">Score Entry</a></li>
                        <li class="breadcrumb-item active">Exam Entry</li>
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

        {{-- Lock Banner --}}
        @if($isSubmitted)
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="ti ti-lock me-2 fs-5"></i>
            <div>
                <strong>Scores Submitted for Review</strong>
                <span class="text-muted ms-2">These scores have been submitted and are locked for editing. Contact admin if changes are needed.</span>
            </div>
        </div>
        @else
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ti ti-info-circle me-2 fs-5"></i>
            <div>
                Enter exam scores below. The <strong>Preview Total</strong> and <strong>Preview Grade</strong> columns update live as you type.
                Click <strong>Save Draft</strong> to save without locking. Click <strong>Submit for Review</strong> when ready.
            </div>
        </div>
        @endif

        {{-- Alerts --}}
        <div id="successAlert" class="alert alert-success d-none">
            <i class="ti ti-check me-1"></i><span id="successMessage"></span>
        </div>
        <div id="errorAlert" class="alert alert-danger d-none">
            <i class="ti ti-alert-triangle me-1"></i><span id="errorMessage"></span>
        </div>

        {{-- Score Entry Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-file-text me-2"></i>Examination Scores</h5>
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
                    <table class="table table-bordered mb-0" id="examScoreTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:50px;">S/N</th>
                                <th style="min-width:200px;">Student</th>
                                <th style="min-width:100px;">Admission No</th>
                                <th class="text-center" style="min-width:80px;">CA Total</th>
                                <th class="text-center" style="min-width:100px;">
                                    Exam
                                    <span class="d-block text-muted" style="font-size:10px;">Max: {{ $examMax }}</span>
                                </th>
                                <th class="text-center" style="min-width:80px;">
                                    Preview Total
                                </th>
                                <th class="text-center" style="min-width:60px;">
                                    Preview Grade
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $i => $enrollment)
                                @php
                                    $student = $enrollment->student;
                                    $caTotal = $caTotals[$student->id] ?? 0;
                                    $examScore = $existingScores->get($student->id)?->score ?? '';
                                    $total = $caTotal + ($examScore !== '' ? (float) $examScore : 0);
                                    $grade = '';
                                    $gradeColor = '';
                                    foreach($gradingScales as $scale) {
                                        if ($total >= $scale->min_score && $total <= $scale->max_score) {
                                            $grade = $scale->grade;
                                            $gradeColor = $scale->grade === 'A1' ? '#16a34a' : ($scale->grade === 'F9' ? '#dc2626' : '#2563eb');
                                            break;
                                        }
                                    }
                                @endphp
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
                                    <td class="text-center fw-medium text-info" id="ca-{{ $student->id }}">{{ number_format($caTotal, 2) }}</td>
                                    <td class="text-center p-1">
                                        <input type="number"
                                               class="form-control form-control-sm text-center exam-input"
                                               data-student-id="{{ $student->id }}"
                                               data-ca-total="{{ $caTotal }}"
                                               value="{{ $examScore !== '' ? number_format($examScore, 2) : '' }}"
                                               min="0"
                                               max="{{ $examMax }}"
                                               step="0.01"
                                               placeholder="0"
                                               {{ $isSubmitted ? 'disabled' : '' }}
                                               oninput="updatePreview(this)">
                                    </td>
                                    <td class="text-center fw-bold" id="preview-total-{{ $student->id }}">
                                        {{ number_format($total, 2) }}
                                    </td>
                                    <td class="text-center" id="preview-grade-{{ $student->id }}">
                                        @if($grade)
                                            <span class="badge" style="background: {{ $gradeColor }}; color: #fff;">{{ $grade }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">
                        <i class="ti ti-info-circle me-1"></i>
                        Final Total = CA Total ({{ 100 - $examMax }}) + Exam Score (max {{ $examMax }}) = 100.
                    </span>
                    <div class="d-flex gap-2">
                        @if(!$isSubmitted)
                            <button type="button" class="btn btn-primary" onclick="saveExamScores(false)" id="saveBtn">
                                <i class="ti ti-device-floppy me-1"></i> Save Draft
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitForReview()" id="submitBtn">
                                <i class="ti ti-send me-1"></i> Submit for Review
                            </button>
                        @else
                            <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                <i class="ti ti-lock me-1"></i>Locked — Contact Admin to Edit
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Grading scales for client-side preview
const gradingScales = @json($gradingScalesJson);

function resolveGrade(score) {
    for (const scale of gradingScales) {
        if (score >= scale.min && score <= scale.max) {
            return { grade: scale.grade, color: scale.color };
        }
    }
    return { grade: 'N/A', color: '#999' };
}

function updatePreview(input) {
    const max = {{ $examMax }};
    const caTotal = parseFloat(input.dataset.caTotal) || 0;
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

    const studentId = input.dataset.studentId;
    const total = caTotal + val;
    const grade = resolveGrade(total);

    document.getElementById('preview-total-' + studentId).textContent = total.toFixed(2);
    document.getElementById('preview-grade-' + studentId).innerHTML =
        `<span class="badge" style="background: ${grade.color}; color: #fff;">${grade.grade}</span>`;
}

function saveExamScores(isSubmit = false) {
    const btn = isSubmit ? document.getElementById('submitBtn') : document.getElementById('saveBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2 me-1 spin"></i> ' + (isSubmit ? 'Submitting...' : 'Saving...');

    const scores = [];
    document.querySelectorAll('.exam-input').forEach(input => {
        const studentId = input.dataset.studentId;
        const val = input.value.trim();
        if (val !== '') {
            scores.push({
                student_id: parseInt(studentId),
                score: parseFloat(val) || 0
            });
        }
    });

    fetch('{{ route("teacher.exam-scores.store", $armSubject->id) }}', {
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
            if (isSubmit) {
                submitForReviewApi();
            } else {
                showSuccess(data.message);
            }
        } else {
            showError(data.message || 'Save failed.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        showError('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function submitForReview() {
    if (!confirm('Submit scores for review? This will lock editing. You will need admin approval to make changes.')) {
        return;
    }
    // First save any unsaved changes, then submit
    saveExamScores(true);
}

function submitForReviewApi() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="ti ti-loader-2 me-1 spin"></i> Submitting...';

    fetch('{{ route("teacher.exam-scores.submit", $armSubject->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Submit failed.');
        }
    })
    .catch(err => {
        showError('Network error. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-send me-1"></i> Submit for Review';
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
.exam-input { font-size: 13px; font-weight: 600; }
.exam-input:focus { background: #e8f4f8; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@endsection
