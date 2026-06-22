@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit mb-4">
            <h5>Parents Directory</h5>
            <div class="list-btn">
                <ul class="d-flex align-items-center mb-0 gap-2">
                    <li>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.parents.create') }}">
                            <i class="fa fa-plus me-1"></i>Register Parent
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                @if(session('generated_password'))
                    <hr class="my-2">
                    <p class="mb-1 small fw-semibold">Login credentials for <strong>{{ session('parent_name') }}</strong> — share with parent now (shown only once):</p>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="text-muted small">Password:</span>
                            <code class="fs-6 text-dark fw-bold ms-1">{{ session('generated_password') }}</code>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="navigator.clipboard.writeText('{{ session('generated_password') }}');this.textContent='Copied!'">
                            Copy
                        </button>
                    </div>
                @endif
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
                            <i class="fe fe-users text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.05em">Total Parents</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3" style="background:rgba(var(--bs-success-rgb),.1)">
                            <i class="fe fe-link text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.05em">Linked to Students</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['linked'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded p-3" style="background:rgba(var(--bs-warning-rgb),.1)">
                            <i class="fe fe-user-x text-warning fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.05em">No Student Linked</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['unlinked'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-2">
                <form action="{{ route('admin.parents.index') }}" method="GET"
                      class="d-flex flex-wrap align-items-center gap-2">
                    <div class="flex-grow-1" style="min-width:180px;max-width:280px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fe fe-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="Name or phone..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Search</button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.parents.index') }}" class="btn btn-light btn-sm">
                            <i class="fe fe-x me-1"></i>Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="table-responsive card border-0 shadow-sm">
                    <table class="table datatable mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone / WhatsApp</th>
                                <th>Email</th>
                                <th>Linked Students</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parents as $key => $parent)
                                <tr>
                                    <td class="text-muted">{{ $parents->firstItem() + $key }}</td>
                                    <td>
                                        <p class="fw-semibold mb-0">{{ $parent->full_name }}</p>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $parent->phone) }}"
                                           target="_blank" class="text-success text-decoration-none">
                                            <i class="fe fe-phone me-1"></i>{{ $parent->phone }}
                                        </a>
                                    </td>
                                    <td>{{ $parent->email ?? '—' }}</td>
                                    <td>
                                        @forelse($parent->parentStudents as $link)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $link->student->user->full_name ?? '—' }}
                                                <span class="text-muted">({{ $link->relationship }})</span>
                                            </span>
                                        @empty
                                            <span class="text-muted small">None linked</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($parent->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="{{ route('admin.parents.edit', $parent) }}" title="Edit">
                                                <i class="fe fe-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.parents.regenerate-password', $parent) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Regenerate password for {{ addslashes($parent->full_name) }}? Their current password will stop working.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Regenerate Password">
                                                    <i class="fe fe-refresh-cw"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fe fe-users d-block mb-2" style="font-size:2rem"></i>
                                        No parents registered yet.
                                        @if(request()->filled('search'))
                                            <a href="{{ route('admin.parents.index') }}" class="d-block mt-1 small">Clear filters</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <p class="text-muted small mb-0">
                        Showing {{ $parents->firstItem() ?? 0 }}–{{ $parents->lastItem() ?? 0 }}
                        of {{ $parents->total() }} parents
                    </p>
                    {{ $parents->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
