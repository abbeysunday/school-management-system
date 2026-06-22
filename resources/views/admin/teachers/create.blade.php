@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">New Teacher Registration</h5>
                <p class="text-muted small mt-1 mb-0">Staff ID will be auto-generated based on school prefix.</p>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-light btn-sm"><i class="fe fe-arrow-left me-1"></i>Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                {{-- Account & Biodata --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent"><h6 class="mb-0"><i class="fe fe-user me-2"></i>Account & Biodata</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-control select" required>
                                        <option value="">-- Select --</option>
                                        <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                                        <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Professional & Next of Kin --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent"><h6 class="mb-0"><i class="fe fe-briefcase me-2"></i>Professional Info</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Qualification <span class="text-danger">*</span></label>
                                <input type="text" name="qualification" class="form-control" placeholder="e.g. B.Sc, NCE, PGDE" value="{{ old('qualification') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" placeholder="e.g. Mathematics" value="{{ old('specialization') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type" class="form-control select" required>
                                    <option value="Full-Time" @selected(old('employment_type') == 'Full-Time')>Full-Time</option>
                                    <option value="Part-Time" @selected(old('employment_type') == 'Part-Time')>Part-Time</option>
                                    <option value="Contract" @selected(old('employment_type') == 'Contract')>Contract</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Date Joined <span class="text-danger">*</span></label>
                                <input type="date" name="employment_date" class="form-control" value="{{ old('employment_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent"><h6 class="mb-0"><i class="fe fe-image me-2"></i>Photo</h6></div>
                        <div class="card-body">
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-2 border-top pt-3">
                <button type="submit" class="btn btn-primary"><i class="fe fe-save me-2"></i>Register Staff</button>
            </div>

        </form>

    </div>
</div>
@endsection
