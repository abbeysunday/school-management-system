@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Timetable Periods</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.timetable.builder') }}">
                            <i class="fe fe-grid me-2"></i>Timetable Builder
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Add Period Form --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0 fw-bold">Add New Period</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.timetable.periods.store') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="period_name" class="form-control" placeholder="e.g. Period 9" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Order</label>
                        <input type="number" name="period_order" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="period_type" class="form-select" required>
                            <option value="Teaching">Teaching</option>
                            <option value="Break">Break</option>
                            <option value="Assembly">Assembly</option>
                            <option value="Games">Games</option>
                            <option value="Closing">Closing</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fe fe-plus me-1"></i> Add
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Periods List --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Duration</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $period)
                                <tr>
                                    <td>{{ $period->period_order }}</td>
                                    <td class="fw-semibold">{{ $period->period_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $period->period_type == 'Teaching' ? 'primary' : ($period->period_type == 'Break' ? 'warning' : ($period->period_type == 'Assembly' ? 'info' : ($period->period_type == 'Games' ? 'success' : 'secondary'))) }}">
                                            {{ $period->period_type }}
                                        </span>
                                    </td>
                                    <td>{{ $period->start_time }}</td>
                                    <td>{{ $period->end_time }}</td>
                                    <td>{{ \Carbon\Carbon::parse($period->start_time)->diffInMinutes(\Carbon\Carbon::parse($period->end_time)) }} mins</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPeriod{{ $period->id }}">
                                            <i class="fe fe-edit-2"></i>
                                        </button>
                                        <form action="{{ route('admin.timetable.periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this period?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editPeriod{{ $period->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.timetable.periods.update', $period) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit {{ $period->period_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="period_name" class="form-control" value="{{ $period->period_name }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Start Time</label>
                                                            <input type="time" name="start_time" class="form-control" value="{{ $period->start_time }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">End Time</label>
                                                            <input type="time" name="end_time" class="form-control" value="{{ $period->end_time }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Order</label>
                                                            <input type="number" name="period_order" class="form-control" value="{{ $period->period_order }}" min="1" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Type</label>
                                                            <select name="period_type" class="form-select" required>
                                                                <option value="Teaching" {{ $period->period_type == 'Teaching' ? 'selected' : '' }}>Teaching</option>
                                                                <option value="Break" {{ $period->period_type == 'Break' ? 'selected' : '' }}>Break</option>
                                                                <option value="Assembly" {{ $period->period_type == 'Assembly' ? 'selected' : '' }}>Assembly</option>
                                                                <option value="Games" {{ $period->period_type == 'Games' ? 'selected' : '' }}>Games</option>
                                                                <option value="Closing" {{ $period->period_type == 'Closing' ? 'selected' : '' }}>Closing</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
