@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>CBT Exams</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.cbt.exams.create') }}">
                            <i class="fa fa-plus me-1"></i> Create Exam
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end g-2">
                    <div class="col-lg-3 col-md-6">
                        <select name="status" class="form-control select">
                            <option value="">All Statuses</option>
                            @foreach(['Draft','Scheduled','Active','Completed','Cancelled'] as $s)
                                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="subject_id" class="form-control select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}" {{ request('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="class_arm_id" class="form-control select">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name ?? '' }} — {{ $arm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Type</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Schedule</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            <tr>
                                <td><strong>{{ $exam->title }}</strong></td>
                                <td>{{ $exam->subject->name }}</td>
                                <td>
                                    {{ $exam->classArm->classLevel->name ?? '' }}
                                    {{ $exam->classArm->name }}
                                </td>
                                <td><span class="badge bg-secondary">{{ $exam->exam_type }}</span></td>
                                <td>
                                    <span class="{{ $exam->questions_count >= $exam->total_questions ? 'text-success fw-bold' : 'text-warning' }}">
                                        {{ $exam->questions_count }}/{{ $exam->total_questions }}
                                    </span>
                                </td>
                                <td>{{ $exam->duration_minutes }} min</td>
                                <td>
                                    @php
                                        $badge = match($exam->status) {
                                            'Active'    => 'bg-success',
                                            'Scheduled' => 'bg-info',
                                            'Completed' => 'bg-secondary',
                                            'Cancelled' => 'bg-danger',
                                            default     => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $exam->status }}</span>
                                </td>
                                <td>
                                    @if($exam->start_datetime)
                                        <small>{{ $exam->start_datetime->format('d M Y H:i') }}</small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions d-flex">
                                        <a class="btn btn-sm btn-info me-1" href="{{ route('admin.cbt.exams.show', $exam) }}" title="Builder">
                                            <i class="fe fe-layers"></i>
                                        </a>
                                        <a class="btn delete-table me-1" href="{{ route('admin.cbt.exams.edit', $exam) }}" title="Edit">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.cbt.exams.destroy', $exam) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this exam?')">
                                            @csrf @method('DELETE')
                                            <button class="btn delete-table"><i class="fe fe-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted">No exams found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{ $exams->links() }}
    </div>
</div>

@endsection
