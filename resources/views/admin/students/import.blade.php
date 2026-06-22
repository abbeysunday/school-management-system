@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header">
            <h5 class="mb-2">Bulk Student Import</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.students.import.template') }}" class="btn btn-primary">
                            <i class="fe fe-download me-2"></i>Download Template
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @if(session('success_count') !== null)
            <div class="alert alert-success">
                <strong>Import Complete!</strong> Successfully imported {{ session('success_count') }} students.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 col-sm-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Upload Excel/CSV File <span class="text-danger">*</span></label>
                                <input type="file" name="import_file" class="form-control" accept=".xlsx, .csv" required>
                                @error('import_file')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="btn-path text-end mt-4">
                                <button type="submit" class="btn btn-primary">Run Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error_rows') && count(session('error_rows')) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-danger mb-3">Import Errors ({{ count(session('error_rows')) }} records failed)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th>Excel Row #</th>
                                    <th>Student Name</th>
                                    <th>Error Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('error_rows') as $error)
                                    <tr>
                                        <td>{{ $error['row'] }}</td>
                                        <td>{{ $error['name'] }}</td>
                                        <td class="text-danger">{{ $error['errors'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
