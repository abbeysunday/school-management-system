@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        {{-- Header --}}
        <div class="content-page-header content-page-headersplit">
            <h5>Result Broadsheet</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.results.broadsheet', ['arm_id' => $classArm->id, 'term_id' => $term->id]) }}" class="btn btn-outline-secondary">
                            <i class="fe fe-refresh-cw me-2"></i>Refresh
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('admin.results.recalculate') }}" method="POST" style="display:inline;" onsubmit="return confirm('Recalculate all results for {{ $classArm->full_name }} — {{ $term->name }}?')">
                            @csrf
                            <input type="hidden" name="arm_id" value="{{ $classArm->id }}">
                            <input type="hidden" name="term_id" value="{{ $term->id }}">
                            <button type="submit" class="btn btn-warning">
                                <i class="fe fe-calculator me-2"></i>Recalculate
                            </button>
                        </form>
                    </li>
                    <li>
                        <a href="{{ route('admin.results.export', ['arm_id' => $classArm->id, 'term_id' => $term->id]) }}" class="btn btn-success">
                            <i class="fe fe-download me-2"></i>Export Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-primary-soft rounded-circle">
                                <i class="fe fe-users text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Class Arm</div>
                                <div class="fw-bold">{{ $classArm->full_name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-success-soft rounded-circle">
                                <i class="fe fe-calendar text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Term</div>
                                <div class="fw-bold">{{ $term->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $term->session->name ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-info-soft rounded-circle">
                                <i class="fe fe-book text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Subjects</div>
                                <div class="fw-bold">{{ count($subjects) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            @php $legend = ['A1'=>'#16a34a','B2'=>'#2563eb','B3'=>'#2563eb','C4'=>'#d97706','C5'=>'#d97706','C6'=>'#d97706','D7'=>'#ea580c','E8'=>'#ea580c','F9'=>'#dc2626']; @endphp
            @foreach($legend as $grade => $color)
                <span class="badge" style="background: {{ $color }}; color: #fff;">{{ $grade }}</span>
            @endforeach
        </div>

        {{-- Broadsheet Grid --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" id="broadsheetTable">
                        <thead>
                            <tr style="background: #1a5f2a; color: #fff;">
                                <th class="text-center" style="min-width:40px;position:sticky;left:0;z-index:20;background:#1a5f2a;">S/N</th>
                                <th style="min-width:180px;position:sticky;left:40px;z-index:20;background:#1a5f2a;">Student</th>
                                <th style="min-width:100px;position:sticky;left:220px;z-index:20;background:#1a5f2a;">Adm. No</th>
                                @foreach($subjects as $subject)
                                    <th class="text-center" style="min-width:70px;writing-mode:vertical-rl;text-orientation:mixed;white-space:nowrap;">
                                        {{ $subject->name }}
                                    </th>
                                @endforeach
                                <th class="text-center" style="min-width:60px;background:#145022;">Total</th>
                                <th class="text-center" style="min-width:60px;background:#145022;">Avg</th>
                                <th class="text-center" style="min-width:60px;background:#145022;">%</th>
                                <th class="text-center" style="min-width:50px;background:#145022;">Arm</th>
                                <th class="text-center" style="min-width:50px;background:#145022;">Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $i => $enrollment)
                                @php
                                    $student = $enrollment->student;
                                    $total = 0;
                                    $count = 0;
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold" style="position:sticky;left:0;background:#fff;z-index:10;">{{ $i + 1 }}</td>
                                    <td class="fw-semibold" style="position:sticky;left:40px;background:#fff;z-index:10;white-space:nowrap;">
                                        {{ $student->user->full_name }}
                                    </td>
                                    <td style="position:sticky;left:220px;background:#fff;z-index:10;">
                                        <code>{{ $student->admission_number }}</code>
                                    </td>
                                    @foreach($subjects as $subject)
                                        @php
                                            $result = $results->get($student->id . '|' . $subject->id);
                                            $colorData = $gradeColors[$student->id][$subject->id] ?? null;
                                        @endphp
                                        <td class="text-center" style="min-width:70px;{{ $colorData ? 'background:' . $colorData['bg'] . ';color:' . $colorData['color'] . ';font-weight:600;' : '' }}">
                                            @if($result)
                                                {{ number_format($result->total_score, 0) }}
                                                @php $total += (float) $result->total_score; $count++; @endphp
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @php
                                        $summary = $termSummaries->get($student->id);
                                        $avg = $count > 0 ? round($total / $count, 2) : 0;
                                    @endphp
                                    <td class="text-center fw-bold">{{ number_format($total, 0) }}</td>
                                    <td class="text-center">{{ number_format($avg, 2) }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $summary?->percentage ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $summary?->arm_position ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $summary?->class_position ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + count($subjects) + 5 }}" class="text-center py-5">
                                        <i class="fe fe-grid text-muted" style="font-size:36px;"></i>
                                        <p class="text-muted mt-2">No students enrolled in this class arm for this term.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
#broadsheetTable th, #broadsheetTable td {
    font-size: 11px;
    padding: 4px 6px;
    vertical-align: middle;
}
#broadsheetTable thead th {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
</style>

@endsection 
