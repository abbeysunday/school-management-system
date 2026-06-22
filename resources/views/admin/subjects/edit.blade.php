@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-7 col-sm-12 m-auto">
                    <div class="content-page-header">
                        <h5 class="mb-2">Edit Subject</h5>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Code</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $subject->code) }}">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-control select" required>
                                    @foreach(['General','Science','Arts','Commercial','Technical','Vocational'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $subject->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="is_core" value="0">
                                <input class="form-check-input" type="checkbox" name="is_core" value="1" id="is_core" {{ old('is_core', $subject->is_core) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_core">Core Subject</label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="is_waec_subject" value="0">
                                <input class="form-check-input" type="checkbox" name="is_waec_subject" value="1" id="is_waec" {{ old('is_waec_subject', $subject->is_waec_subject) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_waec">WAEC</label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="is_neco_subject" value="0">
                                <input class="form-check-input" type="checkbox" name="is_neco_subject" value="1" id="is_neco" {{ old('is_neco_subject', $subject->is_neco_subject) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_neco">NECO</label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="btn-path">
                                <a href="{{ route('admin.subjects.index') }}" class="btn btn-cancel me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Subject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
