@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Attendance Records</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('teacher.attendance.index') }}">
                            <i class="fe fe-plus me-2"></i>Mark New
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row">
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-success"><i class="fe fe-check-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ $stats['today_present'] }}</h5>
                                <h6>Present Today</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-danger"><i class="fe fe-x-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ $stats['today_absent'] }}</h5>
                                <h6>Absent Today</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-info"><i class="fe fe-users text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ $stats['today_total'] }}</h5>
                                <h6>Total Marked Today</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Student name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Class Arm</label>
                        <select name="class_arm_id" class="form-select">
                            <option value="">All</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }}>Late</option>
                            <option value="Sick" {{ request('status') == 'Sick' ? 'selected' : '' }}>Sick</option>
                            <option value="Excused" {{ request('status') == 'Excused' ? 'selected' : '' }}>Excused</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fe fe-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-x"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Records Table --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Marked By</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->attendance_date->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $record->student->user->photo_url }}" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                                            <div>
                                                <div class="fw-semibold">{{ $record->student->user->full_name }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $record->student->admission_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $record->classArm->full_name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'Present' => 'success',
                                                'Absent' => 'danger',
                                                'Late' => 'warning',
                                                'Sick' => 'info',
                                                'Excused' => 'secondary',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$record->status] ?? 'light' }}">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td>{{ $record->remarks ?? '—' }}</td>
                                    <td>{{ $record->markedBy?->full_name ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.attendance.edit', $record) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fe fe-edit-2"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fe fe-inbox" style="width:40px;height:40px;" class="mb-2 opacity-25"></i>
                                        <p class="mb-0">No attendance records found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($records->hasPages())
                <div class="card-footer">{{ $records->links() }}</div>
            @endif
        </div>

    </div>
</div>

@endsection
