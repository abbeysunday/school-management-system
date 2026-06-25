@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Score Entry</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Score Entry</li>
                    </ul>
                </div>
                <div class="col-auto">
                    @if($term->results_published)
                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                            <i class="ti ti-lock me-1"></i>Results Published — Scores Locked
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success px-3 py-2">
                            <i class="ti ti-pencil me-1"></i>Entry Active
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Term Info --}}
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ti ti-info-circle me-2 fs-5"></i>
            <div>
                <strong>{{ $session->name }}</strong> — <strong>{{ $term->name }}</strong>
                <span class="text-muted ms-2">Enter CA and Exam scores for your assigned subjects below.</span>
            </div>
        </div>

        {{-- Subject Cards --}}
        <div class="row">
            @forelse($subjects as $item)
                @php
                    $armSubject = $item['arm_subject'];
                    $classArm = $item['class_arm'];
                    $subject = $item['subject'];
                    $caProg = $item['ca_progress'];
                    $examProg = $item['exam_progress'];
                    $isLocked = $item['is_locked'];
                @endphp
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card h-100 {{ $isLocked ? 'border-danger' : 'border-0 shadow-sm' }}">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm bg-primary-soft rounded-circle">
                                        <i class="ti ti-book text-primary"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $subject->name }}</h6>
                                        <small class="text-muted">{{ $classArm->full_name }}</small>
                                    </div>
                                </div>
                                @if($isLocked)
                                    <span class="badge bg-danger-subtle text-danger"><i class="ti ti-lock me-1"></i>Locked</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- CA Progress --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small"><i class="ti ti-clipboard-list me-1"></i>CA Scores</span>
                                    <span class="small fw-semibold {{ $caProg['percentage'] == 100 ? 'text-success' : 'text-warning' }}">
                                        {{ $caProg['completed'] }}/{{ $caProg['total_students'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $caProg['percentage'] == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                                         style="width: {{ $caProg['percentage'] }}%"></div>
                                </div>
                            </div>

                            {{-- Exam Progress --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small"><i class="ti ti-file-text me-1"></i>Exam Scores</span>
                                    <span class="small fw-semibold {{ $examProg['percentage'] == 100 ? 'text-success' : 'text-warning' }}">
                                        {{ $examProg['completed'] }}/{{ $examProg['total_students'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $examProg['percentage'] == 100 ? 'bg-success' : 'bg-info' }}" role="progressbar"
                                         style="width: {{ $examProg['percentage'] }}%"></div>
                                </div>
                            </div>

                            {{-- Students count --}}
                            <div class="d-flex align-items-center gap-1 text-muted small">
                                <i class="ti ti-users"></i>
                                {{ $caProg['total_students'] }} Student{{ $caProg['total_students'] !== 1 ? 's' : '' }}
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('teacher.ca-scores.form', $armSubject->id) }}"
                                   class="btn btn-sm btn-outline-primary flex-fill {{ $isLocked ? 'disabled' : '' }}">
                                    <i class="ti ti-edit me-1"></i>CA Entry
                                </a>
                                <a href="{{ route('teacher.exam-scores.form', $armSubject->id) }}"
                                   class="btn btn-sm btn-outline-success flex-fill {{ $isLocked ? 'disabled' : '' }}">
                                    <i class="ti ti-file-text me-1"></i>Exam Entry
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ti ti-book-off text-muted" style="font-size:48px;"></i>
                            <h5 class="mt-3">No Subjects Assigned</h5>
                            <p class="text-muted">You are not assigned to teach any subject this term.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>

@endsection
