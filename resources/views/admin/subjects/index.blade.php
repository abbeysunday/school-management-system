@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Subjects</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.subjects.create') }}">
                            <i class="fa fa-plus me-2"></i>Add Subject
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Category</th>
                                <th>Core</th>
                                <th>WAEC</th>
                                <th>NECO</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->code ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ $subject->category }}</span></td>
                                <td>
                                    @if($subject->is_core)
                                        <span class="badge bg-primary">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subject->is_waec_subject)
                                        <i class="fe fe-check-circle text-success"></i>
                                    @else
                                        <i class="fe fe-x-circle text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($subject->is_neco_subject)
                                        <i class="fe fe-check-circle text-success"></i>
                                    @else
                                        <i class="fe fe-x-circle text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($subject->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions d-flex">
                                        <a class="btn delete-table me-2" href="{{ route('admin.subjects.edit', $subject) }}">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete subject?')">
                                            @csrf @method('DELETE')
                                            <button class="btn delete-table"><i class="fe fe-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No subjects found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
