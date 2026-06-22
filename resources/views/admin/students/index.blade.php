@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <h5>Students Directory</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-light me-2" href="{{ route('admin.students.import.form') }}">
                            <i class="fe fe-upload me-2"></i>Bulk Import
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.students.create') }}">
                            <i class="fe fe-plus me-2"></i>Register Student
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3 bg-primary bg-opacity-10">
                            <i class="fe fe-users text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                            <p class="text-muted mb-0 small">Total Students</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3 bg-success bg-opacity-10">
                            <i class="fe fe-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] }}</h3>
                            <p class="text-muted mb-0 small">Active</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3 bg-info bg-opacity-10">
                            <i class="fe fe-award text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-info">{{ $stats['graduated'] }}</h3>
                            <p class="text-muted mb-0 small">Graduated</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3 bg-warning bg-opacity-10">
                            <i class="fe fe-alert-circle text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['others'] }}</h3>
                            <p class="text-muted mb-0 small">Others</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fe fe-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Name or admission no..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group mb-0">
                            <select name="class_arm_id" class="form-control select" onchange="this.form.submit()">
                                <option value="">All Classes</option>
                                @foreach($classArms as $arm)
                                    <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                        {{ $arm->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group mb-0">
                            <select name="status" class="form-control select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                @foreach(['Active','Graduated','Withdrawn','Suspended','Transferred'] as $s)
                                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
                @if(request()->hasAny(['search','class_arm_id','status']))
                    <div class="mt-2">
                        <a href="{{ route('admin.students.index') }}" class="small text-primary"><i class="fe fe-x me-1"></i>Clear filters</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Admission No.</th>
                                <th>Class</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $key => $student)
                            <tr>
                                <td class="text-muted">{{ $students->firstItem() + $key }}</td>
                                <td>
                                    <img src="{{ $student->user->photo_url }}" class="rounded-circle" width="40" height="40" style="object-fit:cover;" alt="">
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ $student->user->full_name }}
                                    </a>
                                </td>
                                <td><code class="text-primary">{{ $student->admission_number }}</code></td>
                                <td>{{ $student->currentArm()?->full_name ?? '—' }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>
                                    @php
                                        $color = match($student->status) {
                                            'Active' => 'success', 'Graduated' => 'info',
                                            'Suspended' => 'danger', 'Withdrawn' => 'warning',
                                            'Transferred' => 'secondary', default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $student->status }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="table-actions d-flex justify-content-end gap-1">
                                        <a class="btn btn-sm btn-outline-info" href="{{ route('admin.students.show', $student) }}" title="View">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.students.edit', $student) }}" title="Edit">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.students.id-card', $student) }}" target="_blank" title="ID Card">
                                            <i class="fe fe-credit-card"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($student->user->full_name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fe fe-users d-block mb-2" style="font-size:2rem"></i>
                                    No students found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <p class="text-muted small mb-0">Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}</p>
                    {{ $students->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
