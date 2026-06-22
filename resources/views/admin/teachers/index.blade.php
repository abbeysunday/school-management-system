@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <h5>Staff / Teachers Directory</h5>
            <div class="list-btn">
                <ul class="d-flex align-items-center mb-0 gap-2">
                    <li>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.teachers.create') }}">
                            <i class="fa fa-plus me-1"></i>Register Teacher
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3" style="background:rgba(var(--bs-primary-rgb),.1)">
                            <i class="fe fe-briefcase text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing:.05em">Total Teachers</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3" style="background:rgba(var(--bs-success-rgb),.1)">
                            <i class="fe fe-user-check text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing:.05em">Active Staff</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3" style="background:rgba(var(--bs-info-rgb),.1)">
                            <i class="fe fe-clock text-info fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing:.05em">Full-Time</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $stats['full_time'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter / Search Bar --}}
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-2">
                <form action="{{ route('admin.teachers.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div class="flex-grow-1" style="min-width:200px; max-width:300px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fe fe-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="Search name, staff ID, email or phone"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Search</button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-light btn-sm">
                            <i class="fe fe-x me-1"></i>Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Photo</th>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td>
                                        <img src="{{ $teacher->user->photo_url }}" class="rounded-circle border" width="40" height="40" style="object-fit:cover;" alt="photo">
                                    </td>
                                    <td><code class="text-primary">{{ $teacher->staff_id }}</code></td>
                                    <td>
                                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $teacher->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="small"><i class="fe fe-phone text-muted me-1"></i>{{ $teacher->user->phone }}</div>
                                        <div class="text-muted small"><i class="fe fe-mail text-muted me-1"></i>{{ $teacher->user->email }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $teacher->employment_type }}</span></td>
                                    <td>
                                        @if($teacher->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            {{-- View Profile --}}
                                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.teachers.show', $teacher) }}" title="View Profile">
                                                <i class="fe fe-eye"></i>
                                            </a>

                                            {{-- Edit Profile --}}
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.teachers.edit', $teacher) }}" title="Edit Profile">
                                                <i class="fe fe-edit"></i>
                                            </a>

                                            {{-- Manage Assignments --}}
                                            <a class="btn btn-sm btn-outline-success" href="{{ route('admin.teachers.assignments.edit', $teacher) }}" title="Manage Assignments">
                                                <i class="fe fe-book-open"></i>
                                            </a>

                                            {{-- Delete Form --}}
                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($teacher->full_name) }}? This action cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Teacher">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fe fe-users d-block mb-2" style="font-size:2rem"></i>
                                        No teachers registered yet.
                                        @if(request()->filled('search'))
                                            <a href="{{ route('admin.teachers.index') }}" class="d-block mt-1 small">Clear filters</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0">
                Showing {{ $teachers->firstItem() ?? 0 }}–{{ $teachers->lastItem() ?? 0 }}
                of {{ $teachers->total() }} teachers
            </p>
            {{ $teachers->links() }}
        </div>

    </div>
</div>
@endsection
