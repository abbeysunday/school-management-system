@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Edit CA Scores</h5>
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
                <span class="text-muted ms-2">You are editing scores as an administrator. Changes will be logged under your account.</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $armSubject->subject->name }} — {{ $armSubject->classArm->full_name }} — {{ $term->name }}</h5>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('admin.scores.ca-update', $armSubject->id) }}" method="POST" id="caForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">S/N</th>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    @foreach($caConfigs as $config)
                                        <th class="text-center">
                                            {{ $config->component_name }}
                                            <span class="d-block text-muted" style="font-size:10px;">Max: {{ $config->max_score }}</span>
                                        </th>
                                    @endforeach
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $i => $enrollment)
                                    @php $student = $enrollment->student; @endphp
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $student->user->full_name }}</td>
                                        <td><code>{{ $student->admission_number }}</code></td>
                                        @foreach($caConfigs as $config)
                                            <td class="text-center p-1">
                                                <input type="number"
                                                       name="scores[{{ $student->id }}_{{ $config->id }}][student_id]"
                                                       value="{{ $student->id }}" hidden>
                                                <input type="number"
                                                       name="scores[{{ $student->id }}_{{ $config->id }}][config_id]"
                                                       value="{{ $config->id }}" hidden>
                                                <input type="number"
                                                       name="scores[{{ $student->id }}_{{ $config->id }}][score]"
                                                       class="form-control form-control-sm text-center"
                                                       value="{{ $scoreMatrix[$student->id][$config->id] ?? '' }}"
                                                       min="0"
                                                       max="{{ $config->max_score }}"
                                                       step="0.01"
                                                       placeholder="0">
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bold text-primary">
                                            {{ number_format(array_sum(array_map(fn($v) => (float) $v, $scoreMatrix[$student->id] ?? [])), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-2"></i>Update CA Scores
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
