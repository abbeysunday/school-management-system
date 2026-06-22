@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.cbt.exams.store') }}" method="POST">
            @csrf
            <div class="content-page-header content-page-headersplit mb-4">
                <h5>Create New Exam</h5>
                <div class="list-btn">
                    <a href="{{ route('admin.cbt.exams.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create &amp; Add Questions</button>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="row">
                {{-- Left: Core Settings --}}
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">Exam Details</h6></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Exam Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="e.g. First Term Mathematics Assessment" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <select name="subject_id" class="form-control select @error('subject_id') is-invalid @enderror" required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $id => $name)
                                                <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Class / Arm <span class="text-danger">*</span></label>
                                        <select name="class_arm_id" class="form-control select @error('class_arm_id') is-invalid @enderror" required>
                                            <option value="">Select Class</option>
                                            @foreach($classArms as $arm)
                                                <option value="{{ $arm->id }}" {{ old('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                                    {{ $arm->classLevel->name ?? '' }} — {{ $arm->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_arm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Term <span class="text-danger">*</span></label>
                                        <select name="term_id" class="form-control select @error('term_id') is-invalid @enderror" required>
                                            <option value="">Select Term</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term->id }}"
                                                    {{ old('term_id', $currentTerm?->id) == $term->id ? 'selected' : '' }}>
                                                    {{ $term->name }} ({{ $term->session->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('term_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Exam Type <span class="text-danger">*</span></label>
                                        <select name="exam_type" class="form-control select" required>
                                            @foreach(['Practice','Assessment','Final'] as $t)
                                                <option value="{{ $t }}" {{ old('exam_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Total Questions <span class="text-danger">*</span></label>
                                        <input type="number" name="total_questions" class="form-control" value="{{ old('total_questions', 40) }}" min="1" max="200" required id="total-q">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Marks per Question <span class="text-danger">*</span></label>
                                        <input type="number" name="marks_per_question" class="form-control" value="{{ old('marks_per_question', 2.5) }}" step="0.5" min="0.5" required id="marks-q">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Total Marks</label>
                                        <input type="text" class="form-control bg-light" id="total-marks" readonly value="{{ old('total_questions', 40) * old('marks_per_question', 2.5) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Duration (minutes) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 60) }}" min="5" max="480" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Marks Deducted per Wrong Answer</label>
                                        <input type="number" name="marks_deducted_per_wrong" class="form-control" value="{{ old('marks_deducted_per_wrong', 0) }}" step="0.5" min="0">
                                        <small class="text-muted">Set 0 to disable negative marking</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label>Instructions <small class="text-muted">(shown to student before exam)</small></label>
                                <textarea name="instructions" rows="3" class="form-control" placeholder="Answer all questions. Each question has only one correct answer…">{{ old('instructions') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Schedule & Options --}}
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">Schedule</h6></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Start Date &amp; Time</label>
                                <input type="datetime-local" name="start_datetime" class="form-control"
                                       value="{{ old('start_datetime') }}">
                                <small class="text-muted">Leave blank for manual activation</small>
                            </div>
                            <div class="form-group mb-0">
                                <label>End Date &amp; Time</label>
                                <input type="datetime-local" name="end_datetime" class="form-control"
                                       value="{{ old('end_datetime') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">Options</h6></div>
                        <div class="card-body">
                            @foreach([
                                ['negative_marking',       'Negative Marking',          false],
                                ['randomize_questions',    'Randomize Question Order',  true],
                                ['randomize_options',      'Randomize Option Order',    false],
                                ['show_result_immediately','Show Result Immediately',   true],
                                ['allow_retake',           'Allow Retake',              false],
                            ] as [$name, $label, $default])
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1"
                                       id="{{ $name }}" {{ old($name, $default ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                            </div>
                            @endforeach
                            <div class="form-group mt-3 mb-0">
                                <label>Max Retakes</label>
                                <input type="number" name="max_retakes" class="form-control" value="{{ old('max_retakes', 1) }}" min="1">
                                <small class="text-muted">Only applies if retake is enabled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('total-q').addEventListener('input', calcTotal);
document.getElementById('marks-q').addEventListener('input', calcTotal);
function calcTotal() {
    const q = parseFloat(document.getElementById('total-q').value) || 0;
    const m = parseFloat(document.getElementById('marks-q').value) || 0;
    document.getElementById('total-marks').value = (q * m).toFixed(1);
}
</script>

@endsection
