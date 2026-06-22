@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Academic Sessions</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.sessions.create') }}">
                            <i class="fa fa-plus me-2"></i>Add Session
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
                                <th>Start Year</th>
                                <th>End Year</th>
                                <th>Terms</th>
                                <th>Current</th>
                                <th>Closed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $session->name }}</td>
                                <td>{{ $session->start_year }}</td>
                                <td>{{ $session->end_year }}</td>
                                <td>{{ $session->terms_count }}</td>
                                <td>
                                    @if($session->is_current)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->is_closed)
                                        <span class="badge bg-dark">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions d-flex">
                                        <a class="btn delete-table me-2" href="{{ route('admin.sessions.edit', $session) }}">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        @if(!$session->is_current)
                                        <form action="{{ route('admin.sessions.set-current', $session) }}" method="POST" class="me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Set as Current">
                                                <i class="fe fe-check-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button class="btn delete-table" type="button" data-bs-toggle="modal" data-bs-target="#delete-session" onclick="setDeleteAction('{{ route('admin.sessions.destroy', $session) }}')">
                                            <i class="fe fe-trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No sessions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Session -->
<div class="modal fade" id="delete-session">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="text-center">
                    <i class="fe fe-trash-2 text-danger fs-1"></i>
                    <div class="mt-4">
                        <h4>Delete Session?</h4>
                        <p class="text-muted mb-0">Are you sure want to delete this?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button type="button" class="btn w-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form id="deleteSessionForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn w-sm btn-danger">Yes, Delete It!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDeleteAction(url) {
    document.getElementById('deleteSessionForm').action = url;
}
</script>

@endsection
