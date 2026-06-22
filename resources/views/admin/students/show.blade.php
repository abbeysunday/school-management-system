@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <div>
                <h5 class="mb-0">Student Profile</h5>
                <p class="text-muted small mb-0">{{ $student->admission_number }}</p>
            </div>
            <div class="list-btn">
                <ul>
                    <li><a href="{{ route('admin.students.index') }}" class="btn btn-light"><i class="fe fe-arrow-left me-2"></i>Back</a></li>
                    <li><a href="{{ route('admin.students.edit', $student) }}" class="btn btn-light"><i class="fe fe-edit me-2"></i>Edit</a></li>
                    <li><a href="{{ route('admin.students.id-card', $student) }}" target="_blank" class="btn btn-primary"><i class="fe fe-credit-card me-2"></i>ID Card</a></li>
                </ul>
            </div>
        </div>

        @php
            $activeEnrollment = $student->enrollments->where('is_active', true)->sortByDesc('id')->first();
        @endphp

        @if($activeEnrollment)
            <div class="alert alert-success d-flex align-items-center justify-content-between py-2 mb-4">
                <div><i class="fe fe-check-circle me-2"></i>Enrolled in <strong>{{ $activeEnrollment->classArm?->full_name ?? '—' }}</strong> • {{ $activeEnrollment->session?->name ?? '—' }}</div>
                <a href="{{ route('admin.students.enrollment', ['class_arm_id' => $activeEnrollment->class_arm_id]) }}" class="btn btn-sm btn-outline-success">Manage / Transfer</a>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center justify-content-between py-2 mb-4">
                <div><i class="fe fe-alert-circle me-2"></i>Not enrolled in any class</div>
                <a href="{{ route('admin.students.enrollment') }}" class="btn btn-sm btn-warning">Enroll Now</a>
            </div>
        @endif

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-4">
                <div class="card text-center mb-3">
                    <div class="card-body">
                        <img src="{{ $student->user->photo_url }}" class="rounded-circle mb-3 border" width="100" height="100" style="object-fit:cover;">
                        <h4 class="mb-1">{{ $student->user->full_name }}</h4>
                        <p class="text-muted small mb-2">{{ $student->admission_number }}</p>
                        @php
                            $color = match($student->status) { 'Active'=>'success','Graduated'=>'info','Suspended'=>'danger','Withdrawn'=>'warning','Transferred'=>'secondary',default=>'secondary' };
                        @endphp
                        <span class="badge bg-{{ $color }} mb-3">{{ $student->status }}</span>

                        <div class="text-start small">
                            <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Class</span><span class="fw-semibold">{{ $student->currentArm()?->full_name ?? '—' }}</span></div>
                            <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Gender</span><span>{{ $student->gender }}</span></div>
                            <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">D.O.B</span><span>{{ $student->date_of_birth?->format('M d, Y') ?? '—' }}</span></div>
                            <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Religion</span><span>{{ $student->religion ?? '—' }}</span></div>
                            <div class="d-flex justify-content-between py-1"><span class="text-muted">Admitted</span><span>{{ $student->admission_date?->format('M d, Y') ?? '—' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="fe fe-phone me-2"></i>Contact Info</h6></div>
                    <div class="card-body small">
                        <p class="mb-1"><i class="fe fe-mail text-muted me-2"></i>{{ $student->user->email ?? '—' }}</p>
                        <p class="mb-1"><i class="fe fe-phone text-muted me-2"></i>{{ $student->user->phone ?? '—' }}</p>
                        <p class="mb-1"><i class="fe fe-map-pin text-muted me-2"></i>{{ $student->state_of_origin ?? '—' }}{{ $student->lga ? ', '.$student->lga : '' }}</p>
                        @if($student->home_address)<p class="mb-0"><i class="fe fe-home text-muted me-2"></i>{{ $student->home_address }}</p>@endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="fe fe-activity me-2"></i>Medical</h6></div>
                    <div class="card-body small">
                        <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Blood Group</span><span class="fw-semibold">{{ $student->blood_group ?? '—' }}</span></div>
                        <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Genotype</span><span class="fw-semibold">{{ $student->genotype ?? '—' }}</span></div>
                        <div class="pt-2"><span class="text-muted d-block mb-1">Conditions</span>{{ $student->medical_conditions ?: 'None on record' }}</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fe fe-users me-2"></i>Parents</h6>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addParentForm"><i class="fe fe-plus"></i></button>
                    </div>
                    <div class="collapse" id="addParentForm">
                        <div class="card-body border-bottom bg-light">
                            @if($availableParents->isEmpty())
                                <p class="text-muted small mb-0">No available parents. <a href="#">Register one first</a>.</p>
                            @else
                                <form action="{{ route('admin.parents.link-student') }}" method="POST" class="row g-2">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <div class="col-6">
                                        <select name="parent_user_id" class="form-control select form-control-sm" required>
                                            <option value="">Select parent</option>
                                            @foreach($availableParents as $p)
                                                <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select name="relationship" class="form-control select form-control-sm" required>
                                            @foreach(['Father','Mother','Guardian','Uncle','Aunt','Sibling','Others'] as $rel)
                                                <option value="{{ $rel }}">{{ $rel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Link</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body small p-0">
                        @forelse($student->parentStudents as $link)
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $link->parentUser->full_name }}</p>
                                    <span class="text-muted small">{{ $link->relationship }}</span>
                                    @if($link->is_primary_contact)<span class="badge bg-primary ms-1" style="font-size:.6rem">Primary</span>@endif
                                </div>
                                <form action="{{ route('admin.parents.unlink', $link) }}" method="POST" class="d-inline" onsubmit="return confirm('Unlink?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fe fe-trash-2"></i></button>
                                </form>
                            </div>
                        @empty
                            <p class="text-muted mb-0 px-3 py-2">No parents linked.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between"><h6 class="mb-0"><i class="fe fe-book-open me-2"></i>Enrollment History</h6><span class="badge bg-secondary">{{ $student->enrollments->count() }}</span></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Session</th><th>Class</th><th>Date</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse($student->enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->session?->name ?? '—' }}</td>
                                            <td>{{ $enrollment->classArm?->full_name ?? '—' }}</td>
                                            <td>{{ $enrollment->enrollment_date?->format('M d, Y') ?? '—' }}</td>
                                            <td>@if($enrollment->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Past</span>@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">No history</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between"><h6 class="mb-0"><i class="fe fe-bar-chart-2 me-2"></i>Latest Results</h6><span class="badge bg-secondary">{{ $student->results->count() }}</span></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Subject</th><th class="text-center">CA</th><th class="text-center">Exam</th><th class="text-center">Total</th><th class="text-center">Grade</th></tr></thead>
                                <tbody>
                                    @forelse($student->results->take(10) as $result)
                                        <tr>
                                            <td>{{ $result->subject?->name ?? '—' }}</td>
                                            <td class="text-center">{{ $result->ca_score ?? '—' }}</td>
                                            <td class="text-center">{{ $result->exam_score ?? '—' }}</td>
                                            <td class="text-center"><strong>{{ $result->total_score ?? '—' }}</strong></td>
                                            <td class="text-center"><span class="badge bg-primary">{{ $result->grade ?? '—' }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">No results</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between"><h6 class="mb-0"><i class="fe fe-credit-card me-2"></i>Recent Payments</h6><span class="badge bg-secondary">{{ $student->payments->count() }}</span></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Date</th><th>Amount</th><th>Channel</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse($student->payments->take(5) as $payment)
                                        <tr>
                                            <td>{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                                            <td>₦{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->payment_method ?? '—' }}</td>
                                            <td><span class="badge bg-success">{{ $payment->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">No payments</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
