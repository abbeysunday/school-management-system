@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Teacher Assignments & Workload</h5>
                <p class="text-muted small mt-1 mb-0">
                    <span class="fw-semibold text-dark">{{ $teacher->full_name }}</span>
                    (<code class="text-primary">{{ $teacher->staff_id }}</code>) &bull; Session: {{ $session->name }}
                </p>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-light btn-sm"><i class="fe fe-arrow-left me-1"></i>Back</a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger"><i class="fe fe-alert-circle me-2"></i>{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.teachers.assignments.update', $teacher) }}" method="POST">
            @csrf

            <div class="row g-4">

                {{-- Form Teacher Assignment --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom">
                            <h6 class="mb-0"><i class="fe fe-star me-2 text-warning"></i>Form Teacher Role</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">Assign this teacher to oversee a specific class arm for the current academic session.</p>
                            <label class="form-label fw-semibold">Class Arm</label>
                            <select name="form_class_arm_id" class="form-control select">
                                <option value="">-- No Form Class Assigned --</option>
                                @foreach($classArms as $arm)
                                    <option value="{{ $arm->id }}" @selected(old('form_class_arm_id', $currentFormClass?->class_arm_id) == $arm->id)>
                                        {{ $arm->full_name }}
                                    </option>
                                @endforeach
                            </select>

                            @if($currentFormClass)
                                <div class="mt-3 p-2 bg-success-light text-success rounded small border border-success">
                                    <i class="fe fe-check-circle me-1"></i> Currently the Form Teacher for <strong>{{ $currentFormClass->classArm->full_name }}</strong>.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Subject Assignments --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fe fe-book-open me-2 text-primary"></i>Subject Assignments</h6>
                            <span class="badge bg-secondary" id="subjectCounter">{{ count($currentAssignedSubjectIds) }} Assigned</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="accordion accordion-flush" id="subjectsAccordion">
                                @forelse($armSubjects as $classArmId => $subjects)
                                    @php
                                        // Find class name from first item in collection
                                        $armName = $subjects->first()->classArm->full_name;

                                        // Check how many subjects in this arm are checked by this teacher
                                        $checkedCount = $subjects->whereIn('id', $currentAssignedSubjectIds)->count();
                                    @endphp
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="heading_{{ $classArmId }}">
                                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $classArmId }}">
                                                <span class="fw-semibold">{{ $armName }}</span>
                                                @if($checkedCount > 0)
                                                    <span class="badge bg-primary ms-2 rounded-pill">{{ $checkedCount }}</span>
                                                @endif
                                            </button>
                                        </h2>
                                        <div id="collapse_{{ $classArmId }}" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                            <div class="accordion-body bg-light border-top">
                                                <div class="row g-2">
                                                    @foreach($subjects as $armSubject)
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="form-check form-switch p-2 bg-white rounded border shadow-sm">
                                                                <input class="form-check-input ms-1 subject-checkbox" type="checkbox"
                                                                       name="arm_subjects[]"
                                                                       value="{{ $armSubject->id }}"
                                                                       id="sub_{{ $armSubject->id }}"
                                                                       @checked(in_array($armSubject->id, old('arm_subjects', $currentAssignedSubjectIds)))>
                                                                <label class="form-check-label small ms-2 d-block text-truncate" for="sub_{{ $armSubject->id }}" title="{{ $armSubject->subject->name }}">
                                                                    {{ $armSubject->subject->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-muted small">
                                        No subjects mapped to classes for this session yet. Please map subjects in Curriculum Settings first.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="text-end mt-4 pt-3 border-top position-sticky bottom-0 bg-white py-3" style="z-index: 10;">
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fe fe-save me-2"></i>Save All Assignments</button>
            </div>

        </form>

    </div>
</div>

<script>
// Simple JS to update the live counter at the top
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.subject-checkbox');
    const counter = document.getElementById('subjectCounter');

    function updateCount() {
        const checked = document.querySelectorAll('.subject-checkbox:checked').length;
        counter.textContent = checked + ' Assigned';

        if(checked > 15) {
            counter.classList.replace('bg-secondary', 'bg-danger');
        } else {
            counter.classList.replace('bg-danger', 'bg-secondary');
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });
});
</script>
@endsection
