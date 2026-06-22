@extends('admin.admin_layout')

@section('title', 'Student Enrollments')

@section('content')
<div class="content-page-header">
    <h3>Student Enrollments</h3>
    <div class="content-page-headersplit">
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
            <i data-feather="plus"></i> New Enrollment
        </a>
        <a href="{{ route('admin.enrollments.promote') }}" class="btn btn-success ms-2">
            <i data-feather="arrow-up-circle"></i> Promote Students
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="row g-3">
            <div class="col-md-3">
                <select name="session_id" class="form-select">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" @selected(request('session_id') == $session->id)>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="class_arm_id" class="form-select">
                    <option value="">All Class Arms</option>
                    @foreach($classArms as $arm)
                        <option value="{{ $arm->id }}" @selected(request('class_arm_id') == $arm->id)>
                            {{ $arm->classLevel->name ?? '' }} {{ $arm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" @selected(request('is_active') === '1')>Active</option>
                    <option value="0" @selected(request('is_active') === '0')>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-cancel">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Adm. No</th>
                        <th>Class Arm</th>
                        <th>Session</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $i => $enrollment)
                    <tr>
                        <td>{{ $enrollments->firstItem() + $i }}</td>
                        <td>{{ $enrollment->student->surname ?? '' }} {{ $enrollment->student->firstname ?? '' }}</td>
                        <td>{{ $enrollment->student->admission_number ?? 'N/A' }}</td>
                        <td>
                            {{ $enrollment->classArm->classLevel->name ?? '' }}
                            {{ $enrollment->classArm->arm ?? '' }}
                        </td>
                        <td>{{ $enrollment->session->name ?? '' }}</td>
                        <td>{{ $enrollment->enrollment_date?->format('d M Y') }}</td>
                        <td>
                            @if($enrollment->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-sm btn-warning">
                                <i data-feather="edit-2"></i>
                            </a>
                            <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this enrollment?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i data-feather="trash-2"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No enrollments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enrollments->links() }}
    </div>
</div>
@endsection
