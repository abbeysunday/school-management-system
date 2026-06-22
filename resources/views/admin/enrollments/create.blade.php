@extends('admin.layouts.app')

@section('title', 'Bulk Enrollment')

@section('content')
<div class="content-page-header">
    <h3>Bulk Enrollment</h3>
    <div class="content-page-headersplit">
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-cancel">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.enrollments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 form-groupheads">
                    <label>Academic Session <span class="text-danger">*</span></label>
                    <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @selected(old('session_id') == $session->id || $session->is_current)>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 form-groupheads">
                    <label>Class Arm <span class="text-danger">*</span></label>
                    <select name="class_arm_id" class="form-select @error('class_arm_id') is-invalid @enderror" required>
                        <option value="">Select Class Arm</option>
                        @foreach($classArms as $arm)
                            <option value="{{ $arm->id }}" @selected(old('class_arm_id') == $arm->id)>
                                {{ $arm->classLevel->name ?? '' }} {{ $arm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_arm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 form-groupheads">
                    <label>Term (Optional)</label>
                    <select name="term_id" class="form-select">
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected(old('term_id') == $term->id || $term->is_current)>
                                {{ $term->name }} ({{ $term->session->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4 form-groupheads">
                    <label>Enrollment Date <span class="text-danger">*</span></label>
                    <input type="date" name="enrollment_date" class="form-control @error('enrollment_date') is-invalid @enderror"
                           value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required>
                    @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">Select Students</h5>
            @error('student_ids')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-check">
                            </td>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ $student->surname }} {{ $student->firstname }} {{ $student->middlename ?? '' }}</td>
                            <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="btn-path mt-4">
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-primary">Enroll Selected</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
