@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Student Attendance Summary</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.attendance.calculate-summaries') }}" onclick="return confirm('This will recalculate attendance summaries for all students. Continue?')">
                            <i class="fe fe-refresh-cw me-2"></i>Calculate Summaries
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.attendance.report') }}">
                            <i class="fe fe-arrow-left me-2"></i>Back to Report
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span><i class="fe fe-calendar"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ $totalSchoolDays }}</h5>
                                <h6>School Days ({{ $term->name }})</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-success"><i class="fe fe-check-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format(collect($summaries)->sum('days_present')) }}</h5>
                                <h6>Total Present Days</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-danger"><i class="fe fe-x-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format(collect($summaries)->sum('days_absent')) }}</h5>
                                <h6>Total Absent Days</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-warning"><i class="fe fe-clock text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format(collect($summaries)->sum('days_late')) }}</h5>
                                <h6>Total Late Days</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Class Filter --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.student-summary') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Class Arm</label>
                        <select name="class_arm_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $classArmId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.attendance.class-register', ['arm_id' => $classArmId ?? $classArms->first()?->id, 'term_id' => $term->id]) }}" class="btn btn-success" target="_blank">
                            <i class="fe fe-printer me-1"></i> Print Class Register
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0 fw-bold">{{ $term->name }} — Attendance Summary</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th class="text-end">Present</th>
                                <th class="text-end">Absent</th>
                                <th class="text-end">Late</th>
                                <th class="text-end">Sick</th>
                                <th class="text-end">Excused</th>
                                <th class="text-end">Total Marked</th>
                                <th class="text-end">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaries as $i => $s)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $s['name'] }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ $s['admission_no'] }}</div>
                                    </td>
                                    <td>{{ $s['class'] }}</td>
                                    <td class="text-end text-success fw-bold">{{ $s['days_present'] }}</td>
                                    <td class="text-end text-danger fw-bold">{{ $s['days_absent'] }}</td>
                                    <td class="text-end text-warning fw-bold">{{ $s['days_late'] }}</td>
                                    <td class="text-end text-info fw-bold">{{ $s['days_sick'] }}</td>
                                    <td class="text-end text-secondary fw-bold">{{ $s['days_excused'] }}</td>
                                    <td class="text-end">{{ $s['total_marked'] }} / {{ $s['total_school_days'] }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $s['percentage'] >= 75 ? 'success' : ($s['percentage'] >= 50 ? 'warning' : 'danger') }}">
                                            {{ $s['percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
