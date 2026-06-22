@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Academic Terms</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.terms.create') }}">
                            <i class="fa fa-plus me-2"></i>Add Term
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @foreach($sessions as $session)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>{{ $session->name }}</strong>
                <small class="text-muted ms-2">({{ $session->start_year }} – {{ $session->end_year }})</small>
                @if($session->is_current)<span class="badge bg-success ms-2">Current Session</span>@endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Term</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>School Days</th>
                                <th>Current</th>
                                <th>Results</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($session->terms as $term)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $term->name }}</td>
                                <td>{{ $term->start_date?->format('M d, Y') }}</td>
                                <td>{{ $term->end_date?->format('M d, Y') }}</td>
                                <td>{{ $term->total_school_days }}</td>
                                <td>
                                    @if($term->is_current)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($term->results_published)
                                        <span class="badge bg-info">Published</span>
                                    @else
                                        <span class="badge bg-light text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions d-flex">
                                        <a class="btn delete-table me-2" href="{{ route('admin.terms.edit', $term) }}" title="Edit">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <button class="btn btn-info btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#update-school-days"
                                            onclick="setSchoolDays('{{ route('admin.terms.school-days', $term) }}', {{ $term->total_school_days }})" title="School Days">
                                            <i class="fe fe-calendar"></i>
                                        </button>
                                        @if(!$term->is_current)
                                        <form action="{{ route('admin.terms.set-current', $term) }}" method="POST" class="me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Set as Current">
                                                <i class="fe fe-check-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button class="btn delete-table" type="button" data-bs-toggle="modal" data-bs-target="#delete-term"
                                            onclick="setTermDelete('{{ route('admin.terms.destroy', $term) }}')">
                                            <i class="fe fe-trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No terms for this session.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Update School Days -->
<div class="modal fade" id="update-school-days">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update School Days</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="schoolDaysForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Total School Days <span class="text-danger">*</span></label>
                        <input type="number" name="total_school_days" id="schoolDaysInput" class="form-control" min="1" max="366" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Term -->
<div class="modal fade" id="delete-term">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="text-center">
                    <i class="fe fe-trash-2 text-danger fs-1"></i>
                    <div class="mt-4">
                        <h4>Delete Term?</h4>
                        <p class="text-muted mb-0">Are you sure want to delete this?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button type="button" class="btn w-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form id="deleteTermForm" method="POST" style="display:inline;">
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
function setSchoolDays(url, days) {
    document.getElementById('schoolDaysForm').action = url;
    document.getElementById('schoolDaysInput').value = days;
}
function setTermDelete(url) {
    document.getElementById('deleteTermForm').action = url;
}
</script>

@endsection
