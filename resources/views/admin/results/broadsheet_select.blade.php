@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Result Broadsheet</h5>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fe fe-filter me-2"></i>Select Class Arm & Term</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.results.broadsheet') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Class Arm <span class="text-danger">*</span></label>
                                <select name="arm_id" class="form-select" required>
                                    <option value="">— Select Class Arm —</option>
                                    @foreach($classArms as $arm)
                                        <option value="{{ $arm->id }}">{{ $arm->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
                                <select name="term_id" class="form-select" required>
                                    <option value="">— Select Term —</option>
                                    @foreach($terms as $t)
                                        <option value="{{ $t->id }}" {{ $t->is_current ? 'selected' : '' }}>
                                            {{ $t->name }} ({{ $t->session->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-grid me-2"></i>View Broadsheet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
