@extends('admin.layouts.app')

@section('title', 'Promote Students')

@section('content')
<div class="content-page-header">
    <h3>Student Promotion</h3>
    <div class="content-page-headersplit">
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-cancel">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="promoteForm" action="{{ route('admin.enrollments.process-promotion') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-3 form-groupheads">
                    <label>From Session <span class="text-danger">*</span></label>
                    <select name="from_session_id" id="fromSession" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-groupheads">
                    <label>From Class Arm <span class="text-danger">*</span></label>
                    <select name="from_class_arm_id" id="fromClassArm" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($classArms as $arm)
                            <option value="{{ $arm->id }}">
                                {{ $arm->classLevel->name ?? '' }} {{ $arm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-groupheads">
                    <label>To Session <span class="text-danger">*</span></label>
                    <select name="to_session_id" id="toSession" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-groupheads">
                    <label>To Class Arm <span class="text-danger">*</span></label>
                    <select name="to_class_arm_id" id="toClassArm" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($classArms as $arm)
                            <option value="{{ $arm->id }}">
                                {{ $arm->classLevel->name ?? '' }} {{ $arm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3 form-groupheads">
                    <label>Enrollment Date <span class="text-danger">*</span></label>
                    <input type="date" name="enrollment_date" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="loadStudents" class="btn btn-primary w-100">
                        <i data-feather="users"></i> Load Students
                    </button>
                </div>
            </div>

            <hr class="my-4">

            <div id="studentArea" style="display:none;">
                <h5 class="mb-3">Select Students to Promote</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllPromote"></th>
                                <th>Admission No</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody id="studentList"></tbody>
                    </table>
                </div>

                <div class="btn-path mt-4">
                    <button type="submit" class="btn btn-success" id="submitPromote" disabled>
                        <i data-feather="arrow-up-circle"></i> Promote Selected
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('loadStudents').addEventListener('click', function() {
        const sessionId = document.getElementById('fromSession').value;
        const armId = document.getElementById('fromClassArm').value;

        if (!sessionId || !armId) {
            alert('Please select From Session and From Class Arm');
            return;
        }

        fetch(`{{ url('admin/enrollments/promotable-students') }}?from_session_id=${sessionId}&from_class_arm_id=${armId}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('studentList');
                tbody.innerHTML = '';
                if (!data.students || data.students.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center">No active students found.</td></tr>';
                    document.getElementById('studentArea').style.display = 'block';
                    document.getElementById('submitPromote').disabled = true;
                    return;
                }

                data.students.forEach(s => {
                    tbody.innerHTML += `
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="${s.id}" class="promote-check"></td>
                            <td>${s.admission_number}</td>
                            <td>${s.surname} ${s.firstname}</td>
                        </tr>
                    `;
                });

                document.getElementById('studentArea').style.display = 'block';
                document.getElementById('submitPromote').disabled = false;

                document.getElementById('selectAllPromote').addEventListener('change', function() {
                    document.querySelectorAll('.promote-check').forEach(cb => cb.checked = this.checked);
                });
            })
            .catch(err => {
                alert('Failed to load students');
                console.error(err);
            });
    });
</script>
@endsection
