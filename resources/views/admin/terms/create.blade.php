@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.terms.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-7 col-sm-12 m-auto">
                    <div class="content-page-header">
                        <h5 class="mb-2">Add Term</h5>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Academic Session <span class="text-danger">*</span></label>
                                <select name="session_id" class="form-control select @error('session_id') is-invalid @enderror">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $id => $name)
                                        <option value="{{ $id }}" {{ old('session_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Term Name <span class="text-danger">*</span></label>
                                <select name="name" class="form-control select @error('name') is-invalid @enderror">
                                    <option value="">Select Term</option>
                                    <option value="First Term" {{ old('name') == 'First Term' ? 'selected' : '' }}>First Term</option>
                                    <option value="Second Term" {{ old('name') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                                    <option value="Third Term" {{ old('name') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                                </select>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Mid-Term Break Start</label>
                                <input type="date" name="mid_term_break_start" class="form-control" value="{{ old('mid_term_break_start') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Mid-Term Break End</label>
                                <input type="date" name="mid_term_break_end" class="form-control" value="{{ old('mid_term_break_end') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Next Resumption Date</label>
                                <input type="date" name="next_resumption_date" class="form-control" value="{{ old('next_resumption_date') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Total School Days</label>
                                <input type="number" name="total_school_days" class="form-control" value="{{ old('total_school_days', 0) }}" min="0" max="366">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="btn-path">
                                <a href="{{ route('admin.terms.index') }}" class="btn btn-cancel me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Term</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
