@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header">
            <h5>Assign Subjects to Class Arms</h5>
        </div>

        {{-- Filter Form --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.subjects.assignments') }}">
                    <div class="row align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label>Class Arm <span class="text-danger">*</span></label>
                                <select name="class_arm_id" class="form-control select" required>
                                    <option value="">Select Class Arm</option>
                                    @foreach($classArms as $arm)
                                        <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                            {{ $arm->classLevel->name }}{{ $arm->arm }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label>Academic Session <span class="text-danger">*</span></label>
                                <select name="session_id" class="form-control select" required>
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $id => $name)
                                        <option value="{{ $id }}" {{ request('session_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <button type="submit" class="btn btn-primary w-100">Load Subjects</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Assignment Form --}}
        @if(request('class_arm_id') && request('session_id'))
        <form method="POST" action="{{ route('admin.subjects.assignments.store') }}">
            @csrf
            <input type="hidden" name="class_arm_id" value="{{ request('class_arm_id') }}">
            <input type="hidden" name="session_id" value="{{ request('session_id') }}">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Select Subjects</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll"><strong>Select All</strong></label>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($subjects->groupBy('category') as $category => $categorySubjects)
                        <h6 class="mt-3 mb-2 text-primary">{{ $category }}</h6>
                        <div class="row">
                            @foreach($categorySubjects as $subject)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input subject-checkbox" type="checkbox"
                                           name="subject_ids[]" value="{{ $subject->id }}"
                                           id="sub{{ $subject->id }}"
                                           {{ $assignedSubjectIds->contains($subject->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sub{{ $subject->id }}">
                                        {{ $subject->name }}
                                        @if($subject->is_core)<span class="badge bg-primary ms-1">Core</span>@endif
                                        @if($subject->is_waec_subject)<span class="badge bg-info ms-1">W</span>@endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr class="my-2">
                    @empty
                        <p class="text-muted">No active subjects found.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.subjects.assignments') }}" class="btn btn-cancel me-3">Reset</a>
                        <button type="submit" class="btn btn-primary">Save Assignments</button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="alert alert-info">
            <i class="fe fe-info me-2"></i> Select a <strong>Class Arm</strong> and <strong>Session</strong> to manage subject assignments.
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function(e) {
    document.querySelectorAll('.subject-checkbox').forEach(cb => cb.checked = e.target.checked);
});
</script>

@endsection
