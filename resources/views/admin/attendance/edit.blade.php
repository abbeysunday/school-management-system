@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Edit Attendance Record</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.attendance.index') }}">
                            <i class="fe fe-arrow-left me-2"></i>Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0 fw-bold">Record Details</h5></div>
                    <div class="card-body">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Student</p>
                                <p class="fw-bold">{{ $attendance->student->user->full_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Admission No</p>
                                <p class="fw-bold font-monospace">{{ $attendance->student->admission_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Class Arm</p>
                                <p class="fw-bold">{{ $attendance->classArm->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Date</p>
                                <p class="fw-bold">{{ $attendance->attendance_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Original Status</p>
                                <p class="fw-bold">
                                    <span class="badge bg-{{ $attendance->status == 'Present' ? 'success' : ($attendance->status == 'Absent' ? 'danger' : ($attendance->status == 'Late' ? 'warning' : ($attendance->status == 'Sick' ? 'info' : 'secondary'))) }}">
                                        {{ $attendance->status }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Originally Marked By</p>
                                <p class="fw-bold">{{ $attendance->markedBy?->full_name ?? '—' }}</p>
                            </div>
                        </div>

                        <hr>

                        <form action="{{ route('admin.attendance.update', $attendance) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">New Status <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    @foreach(['Present' => 'success', 'Absent' => 'danger', 'Late' => 'warning', 'Sick' => 'info', 'Excused' => 'secondary'] as $status => $color)
                                        <div class="col">
                                            <div class="form-check card p-3 border h-100 status-card" onclick="selectStatus('{{ $status }}')">
                                                <input type="radio" name="status" id="status_{{ $status }}" value="{{ $status }}" class="form-check-input" {{ $attendance->status == $status ? 'checked' : '' }} required>
                                                <label class="form-check-label w-100 text-center" for="status_{{ $status }}">
                                                    <span class="badge bg-{{ $color }} mb-2">{{ $status }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Remarks</label>
                                <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $attendance->remarks) }}" placeholder="e.g. Medical certificate submitted">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Reason for Override <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="2" class="form-control" required placeholder="Why are you changing this record?">{{ old('reason') }}</textarea>
                                <div class="form-text">This will be logged for audit purposes.</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-save me-1"></i> Update Record
                                </button>
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Audit Info</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-2"><i class="fe fe-clock me-1 text-info"></i> Created: {{ $attendance->created_at?->format('d M Y, h:i A') ?? 'N/A' }}</li>
                            <li class="mb-2"><i class="fe fe-edit me-1 text-warning"></i> Last Updated: {{ $attendance->updated_at?->format('d M Y, h:i A') ?? 'N/A' }}</li>
                            <li><i class="fe fe-user me-1 text-primary"></i> Marked By: {{ $attendance->markedBy?->full_name ?? 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function selectStatus(status) {
    document.querySelectorAll('.status-card').forEach(card => card.classList.remove('border-primary'));
    event.currentTarget.classList.add('border-primary');
}
</script>

@endsection
