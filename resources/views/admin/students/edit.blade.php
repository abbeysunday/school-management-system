@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="content-page-header content-page-headersplit">
                <h5>Edit Student Profile</h5>
                <div class="list-btn">
                    <ul>
                        <li>
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-2"></i>Back
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="editStudentTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#biodata" type="button">Biodata</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact" type="button">Contact</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical" type="button">Medical</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#academic" type="button">Academic</button></li>
                    </ul>

                    <div class="tab-content">
                        {{-- Biodata --}}
                        <div class="tab-pane fade show active" id="biodata">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->user->first_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->user->last_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $student->user->middle_name) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control select" required>
                                            <option value="Male" {{ old('gender', $student->gender)=='Male'?'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender', $student->gender)=='Female'?'selected':'' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Religion</label>
                                        <select name="religion" class="form-control select">
                                            <option value="">Select</option>
                                            <option value="Christianity" {{ old('religion', $student->religion)=='Christianity'?'selected':'' }}>Christianity</option>
                                            <option value="Islam" {{ old('religion', $student->religion)=='Islam'?'selected':'' }}>Islam</option>
                                            <option value="Others" {{ old('religion', $student->religion)=='Others'?'selected':'' }}>Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Photo</label>
                                        @if($student->user->photo)
                                            <div class="mb-2 d-flex align-items-center gap-2">
                                                <img src="{{ $student->user->photo_url }}" class="rounded-circle border" width="50" height="50" style="object-fit:cover;">
                                                <span class="text-muted small">Current</span>
                                            </div>
                                        @endif
                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div class="tab-pane fade" id="contact">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>State of Origin</label>
                                        <input type="text" name="state_of_origin" class="form-control" value="{{ old('state_of_origin', $student->state_of_origin) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>LGA</label>
                                        <input type="text" name="lga" class="form-control" value="{{ old('lga', $student->lga) }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Home Address</label>
                                        <textarea name="home_address" rows="2" class="form-control">{{ old('home_address', $student->home_address) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Medical --}}
                        <div class="tab-pane fade" id="medical">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <select name="blood_group" class="form-control select">
                                            <option value="">Unknown</option>
                                            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                                <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group)==$bg?'selected':'' }}>{{ $bg }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Genotype</label>
                                        <select name="genotype" class="form-control select">
                                            <option value="">Unknown</option>
                                            @foreach(['AA','AS','SS','AC','SC'] as $gt)
                                                <option value="{{ $gt }}" {{ old('genotype', $student->genotype)==$gt?'selected':'' }}>{{ $gt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Medical Conditions</label>
                                        <textarea name="medical_conditions" rows="3" class="form-control">{{ old('medical_conditions', $student->medical_conditions) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Academic --}}
                        <div class="tab-pane fade" id="academic">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Class Arm</label>
                                        <select name="class_arm_id" class="form-control select">
                                            <option value="">-- No change --</option>
                                            @foreach($classArms as $arm)
                                                <option value="{{ $arm->id }}" {{ old('class_arm_id', $student->currentEnrollment?->class_arm_id)==$arm->id?'selected':'' }}>
                                                    {{ $arm->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Current: <strong>{{ $student->currentArm()?->full_name ?? 'Not enrolled' }}</strong></small>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control select" required>
                                            @foreach(['Active','Graduated','Withdrawn','Suspended','Transferred'] as $s)
                                                <option value="{{ $s }}" {{ old('status', $student->status)==$s?'selected':'' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Admission Date</label>
                                        <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Previous School</label>
                                        <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school', $student->previous_school) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-cancel me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fe fe-save me-2"></i>Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
