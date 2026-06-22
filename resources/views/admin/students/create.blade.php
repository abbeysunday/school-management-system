@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="content-page-header">
                <h5>New Student Registration</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="studentTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#biodata" type="button">Biodata</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact" type="button">Contact Info</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical" type="button">Medical</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#academic" type="button">Academic Placement</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Biodata --}}
                        <div class="tab-pane fade show active" id="biodata">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control select" required>
                                            <option value="">Select</option>
                                            <option value="Male" {{ old('gender')=='Male'?'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender')=='Female'?'selected':'' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Religion</label>
                                        <select name="religion" class="form-control select">
                                            <option value="">Select</option>
                                            <option value="Christianity" {{ old('religion')=='Christianity'?'selected':'' }}>Christianity</option>
                                            <option value="Islam" {{ old('religion')=='Islam'?'selected':'' }}>Islam</option>
                                            <option value="Others" {{ old('religion')=='Others'?'selected':'' }}>Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Passport Photograph</label>
                                        <input type="file" name="photo" id="photoInput" class="form-control" accept="image/*">
                                        <img id="photoPreview" src="#" class="d-none rounded-circle border mt-2" width="60" height="60" style="object-fit:cover;">
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
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>State of Origin</label>
                                        <input type="text" name="state_of_origin" class="form-control" value="{{ old('state_of_origin') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>LGA</label>
                                        <input type="text" name="lga" class="form-control" value="{{ old('lga') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Home Address</label>
                                        <textarea name="home_address" rows="2" class="form-control">{{ old('home_address') }}</textarea>
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
                                                <option value="{{ $bg }}" {{ old('blood_group')==$bg?'selected':'' }}>{{ $bg }}</option>
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
                                                <option value="{{ $gt }}" {{ old('genotype')==$gt?'selected':'' }}>{{ $gt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Medical Conditions / Allergies</label>
                                        <textarea name="medical_conditions" rows="3" class="form-control">{{ old('medical_conditions') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Academic --}}
                        <div class="tab-pane fade" id="academic">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Assign to Class Arm</label>
                                        <select name="class_arm_id" class="form-control select">
                                            <option value="">-- Do not assign yet --</option>
                                            @foreach($classArms as $arm)
                                                <option value="{{ $arm->id }}" {{ old('class_arm_id')==$arm->id?'selected':'' }}>{{ $arm->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Admission Date</label>
                                        <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Previous School</label>
                                        <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-cancel me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fe fe-save me-2"></i>Save Student</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('photoInput').addEventListener('change', function(e) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const preview = document.getElementById('photoPreview');
        preview.src = event.target.result;
        preview.classList.remove('d-none');
    }
    if(e.target.files[0]) reader.readAsDataURL(e.target.files[0]);
});
</script>
@endsection
