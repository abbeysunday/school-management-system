@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.cbt.exams.update', $exam) }}" method="POST">
            @csrf @method('PUT')
            <div class="content-page-header content-page-headersplit mb-4">
                <h5>Edit Exam — {{ $exam->title }}</h5>
                <div class="list-btn">
                    <a href="{{ route('admin.cbt.exams.show', $exam) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">Exam Details</h6></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Exam Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $exam->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <select name="subject_id" class="form-control select @error('subject_id') is-invalid @enderror" required>
                                            @foreach($subjects as $id => $name)
                                                <option value="{{ $id }}" {{ old('subject_id', $exam->subject_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Class / Arm <span class="text-danger">*</span></label>
                                        <select name="class_arm_id" class="form-control select" required>
                                            @foreach($classArms as $arm)
                                                <option value="{{ $arm->id }}" {{ old('class_arm_id', $exam->class_arm_id) == $arm->id ? 'selected' : '' }}>
                                                    {{ $arm->classLevel->name ?? '' }} — {{ $arm->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Term <span class="text-danger">*</span></label>
                                        <select name="term_id" class="form-control select" required>
                                            @foreach($terms as $term)
                                                <option value="{{ $term->id }}" {{ old('term_id', $exam->term_id) == $term->id ? 'selected' : '' }}>
                                                    {{ $term->name }} ({{ $term->session->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Exam Type <span class="text-danger">*</span></label>
                                        <select name="exam_type" class="form-control select" required>
                                            @foreach(['Practice','Assessment','Final'] as $t)
                                                <option value="{{ $t }}" {{ old('exam_type', $exam->exam_type) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control select">
                                            @foreach(['Draft','Scheduled','Active','Completed','Cancelled'] as $s)
                                                <option value="{{ $s }}" {{ old('status', $exam->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Total Questions <span class="text-danger">*</span></label>
                                        <input type="number" name="total_questions" class="form-control"
                                               value="{{ old('total_questions', $exam->total_questions) }}" min="1" max="200" required id="total-q">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Marks per Question <span class="text-danger">*</span></label>
                                        <input type="number" name="marks_per_question" class="form-control"
                                               value="{{ old('marks_per_question', $exam->marks_per_question) }}" step="0.5" min="0.5" required id="marks-q">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label>Total Marks</label>
                                        <input type="text" class="form-control bg-light" id="total-marks" readonly
                                               value="{{ $exam->total_marks }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Duration (minutes) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration_minutes" class="form-control"
                                               value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="480" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Marks Deducted per Wrong Answer</label>
                                        <input type="number" name="marks_deducted_per_wrong" class="form-control"
                                               value="{{ old('marks_deducted_per_wrong', $exam->marks_deducted_per_wrong ?? 0) }}" step="0.5" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label>Instructions</label>
                                <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $exam->instructions) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">Schedule</h6></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Start Date &amp; Time</label>
                                <input type="datetime-local" name="start_datetime" class="form-control"
                                       value="{{ old('start_datetime', $exam->start_datetime?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="form-group mb-0">
                                <label>End Date &amp; Time</label>
                                <input type="datetime-local" name="end_datetime" class="form-control"
                                       value="{{ old('end_datetime', $exam->end_datetime?->format('Y-m-d\TH:i')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">Options</h6></div>
                        <div class="card-body">
                            @foreach([
                                ['negative_marking',       'Negative Marking'],
                                ['randomize_questions',    'Randomize Question Order'],
                                ['randomize_options',      'Randomize Option Order'],
                                ['show_result_immediately','Show Result Immediately'],
                                ['allow_retake',           'Allow Retake'],
                            ] as [$name, $label])
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1"
                                       id="{{ $name }}" {{ old($name, $exam->$name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                            </div>
                            @endforeach
                            <div class="form-group mt-3 mb-0">
                                <label>Max Retakes</label>
                                <input type="number" name="max_retakes" class="form-control"
                                       value="{{ old('max_retakes', $exam->max_retakes ?? 1) }}" min="1">
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
