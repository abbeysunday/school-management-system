@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Attendance Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('teacher.attendance.index') }}">Attendance</a></li>
                        <li class="breadcrumb-item active">Report</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Back to Marking
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.attendance.report') }}" class="row g-3 align-items-end">
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
                        <label class="form-label">Month</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>

        {{-- Report Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-calendar me-2"></i>
                    {{ $classArm->classLevel->name }}{{ $classArm->arm }} — {{ date('F Y', strtotime($month . '-01')) }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-sm mb-0" style="min-width:{{ max(800, count($schoolDays) * 45 + 300) }}px;">
                        <thead>
                            <tr>
                                <th style="min-width:180px;position:sticky;left:0;background:#fff;z-index:2;">Student</th>
                                @foreach($schoolDays as $day)
                                    <th class="text-center" style="min-width:36px;padding:4px;font-size:10px;">
                                        {{ date('d', strtotime($day)) }}
                                    </th>
                                @endforeach
                                <th class="text-center bg-success text-white">P</th>
                                <th class="text-center bg-danger text-white">A</th>
                                <th class="text-center bg-warning">L</th>
                                <th class="text-center">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $row)
                                <tr>
                                    <td style="position:sticky;left:0;background:#fff;z-index:1;min-width:180px;">
                                        <div class="fw-semibold" style="font-size:12px;">{{ $row['name'] }}</div>
                                        <div class="text-muted" style="font-size:10px;">{{ $row['admission_no'] }}</div>
                                    </td>
                                    @foreach($row['days'] as $day => $status)
                                        <td class="text-center p-1">
                                            @if($status)
                                                <span class="badge bg-{{ $status == 'Present' ? 'success' : ($status == 'Absent' ? 'danger' : ($status == 'Late' ? 'warning' : ($status == 'Sick' ? 'info' : 'secondary'))) }}" style="font-size:9px;padding:2px 4px;">
                                                    {{ substr($status, 0, 1) }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size:9px;">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-bold text-success">{{ $row['stats']['Present'] + $row['stats']['Late'] }}</td>
                                    <td class="text-center fw-bold text-danger">{{ $row['stats']['Absent'] }}</td>
                                    <td class="text-center fw-bold text-warning">{{ $row['stats']['Late'] }}</td>
                                    <td class="text-center fw-bold">{{ $row['attendance_rate'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex gap-3 flex-wrap">
                    <span class="badge bg-success">P</span> Present
                    <span class="badge bg-danger">A</span> Absent
                    <span class="badge bg-warning">L</span> Late
                    <span class="badge bg-info">S</span> Sick
                    <span class="badge bg-secondary">E</span> Excused
                    <span class="text-muted">—</span> Not marked
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
