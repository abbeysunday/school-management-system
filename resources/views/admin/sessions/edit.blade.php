@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-7 col-sm-12 m-auto">
                    <div class="content-page-header">
                        <h5 class="mb-2">Edit Academic Session</h5>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Session Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $session->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Start Year <span class="text-danger">*</span></label>
                                <input type="number" name="start_year" class="form-control @error('start_year') is-invalid @enderror"
                                       value="{{ old('start_year', $session->start_year) }}">
                                @error('start_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>End Year <span class="text-danger">*</span></label>
                                <input type="number" name="end_year" class="form-control @error('end_year') is-invalid @enderror"
                                       value="{{ old('end_year', $session->end_year) }}">
                                @error('end_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="is_closed" value="0">
                                <input class="form-check-input" type="checkbox" name="is_closed" value="1" id="is_closed"
                                    {{ old('is_closed', $session->is_closed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_closed">Mark as Closed</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="btn-path">
                                <a href="{{ route('admin.sessions.index') }}" class="btn btn-cancel me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Session</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
