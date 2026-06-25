@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Edit Exam Scores</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.scores.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="fe fe-shield me-2 fs-5"></i>
            <div>
                <strong>Admin Override Mode</strong>
                <span class="text-muted ms-2">You are editing exam scores as an administrator. This will recalculate all results.</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $armSubject->subject->name }} — {{ $armSubject->classArm->full_name }} — {{ $term->name }}</h5>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('admin.scores.exam-update', $armSubject->id) }}" method="POST" id="examForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">S/N</th>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th class="text-center">CA Total</th>
                                    <th class="text-center">
                                        Exam Score
                                        <span class="d-block text-muted" style="font-size:10px;">Max: {{ $examMax }}</span>
                                    </th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $i => $enrollment)
                                    @php
                                        $student = $enrollment->student;
                                        $caTotal = $caTotals[$student->id] ?? 0;
                                        $examScore = $existingScores->get($student->id)?->score ?? '';
                                        $total = $caTotal + ($examScore !== '' ? (float) $examScore : 0);
                                        $grade = '';
                                        foreach($gradingScales as $scale) {
                                            if ($total >= $scale->min_score && $total <= $scale->max_score) {
                                                $grade = $scale->grade;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $student->user->full_name }}</td>
                                        <td><code>{{ $student->admission_number }}</code></td>
                                        <td class="text-center fw-medium text-info">{{ number_format($caTotal, 2) }}</td>
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   name="scores[{{ $student->id }}][student_id]"
                                                   value="{{ $student->id }}" hidden>
                                            <input type="number"
                                                   name="scores[{{ $student->id }}][score]"
                                                   class="form-control form-control-sm text-center"
                                                   value="{{ $examScore !== '' ? number_format($examScore, 2) : '' }}"
                                                   min="0"
                                                   max="{{ $examMax }}"
                                                   step="0.01"
                                                   placeholder="0">
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($total, 2) }}</td>
                                        <td class="text-center">
                                            @if($grade)
                                                <span class="badge bg-primary">{{ $grade }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fe fe-save me-2"></i>Update Exam Scores
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
