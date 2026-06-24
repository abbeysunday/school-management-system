@extends('teacher.layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">My Timetable</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Timetable</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary mb-1">{{ $totalPeriods }}</h4>
                        <span class="text-muted">Total Periods</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success mb-1">{{ $teachingPeriods }}</h4>
                        <span class="text-muted">Teaching Periods</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info mb-1">{{ count($classArms) }}</h4>
                        <span class="text-muted">Class Arms</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timetable Grid --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-calendar me-2"></i>Weekly Schedule</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered timetable-grid mb-0">
                        <thead>
                            <tr>
                                <th style="min-width:120px;background:#1a5f2a;color:#fff;">Period</th>
                                @foreach($days as $day)
                                    <th class="text-center" style="min-width:150px;background:#1a5f2a;color:#fff;">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $period)
                                <tr>
                                    <td class="fw-semibold" style="background:#f8f9fa;white-space:nowrap;">
                                        <div>{{ $period->period_name }}</div>
                                        <div class="text-muted" style="font-size:10px;">{{ $period->start_time }} - {{ $period->end_time }}</div>
                                    </td>
                                    @foreach($days as $day)
                                        @php $entry = $timetableGrid[$day][$period->id] ?? null; @endphp
                                        <td class="p-2" style="min-width:150px;vertical-align:top;">
                                            @if($entry)
                                                <div class="timetable-entry bg-light border rounded p-2">
                                                    <div class="fw-bold text-primary" style="font-size:12px;">{{ $entry->subject?->name ?? '—' }}</div>
                                                    <div class="text-muted" style="font-size:11px;">
                                                        <i class="ti ti-school me-1"></i>{{ $entry->classArm?->full_name ?? 'N/A' }}
                                                    </div>
                                                    @if($entry->room)
                                                        <div class="text-muted" style="font-size:10px;">
                                                            <i class="ti ti-map-pin me-1"></i>{{ $entry->room }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-3" style="font-size:12px;">—</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Class Arms List --}}
        @if(count($classArms) > 0)
            <div class="card mt-4">
                <div class="card-header"><h5 class="mb-0"><i class="ti ti-school me-2"></i>My Class Arms</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($classArms as $armName)
                            <span class="badge bg-light text-dark border">{{ $armName }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<style>
.timetable-grid th, .timetable-grid td { border: 1px solid #dee2e6; }
.timetable-entry { transition: all 0.2s; }
</style>

@endsection
