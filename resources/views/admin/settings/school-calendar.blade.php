@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>School Calendar</h5>
        </div>

        <div class="row">
            {{-- Term Management --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Term Dates & Breaks</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table datatable mb-0">
                                <thead>
                                    <tr>
                                        <th>Session</th>
                                        <th>Term</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Mid-Term Break</th>
                                        <th>Resumption</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($terms as $term)
                                    <tr>
                                        <td>{{ $term->session->name }}</td>
                                        <td>
                                            {{ $term->name }}
                                            @if($term->is_current)<span class="badge bg-success ms-1">Current</span>@endif
                                        </td>
                                        <td>{{ $term->start_date?->format('M d, Y') }}</td>
                                        <td>{{ $term->end_date?->format('M d, Y') }}</td>
                                        <td>
                                            @if($term->mid_term_break_start && $term->mid_term_break_end)
                                                {{ $term->mid_term_break_start->format('M d') }} – {{ $term->mid_term_break_end->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $term->next_resumption_date?->format('M d, Y') ?? '—' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTermModal"
                                                onclick="editTerm({{ $term }})">
                                                <i class="fe fe-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No terms found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $terms->links() }}
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Current Term</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $currentTerm = \App\Models\Term::with('session')->where('is_current', true)->first();
                        @endphp

                        @if($currentTerm)
                            <div class="mb-3">
                                <label class="text-muted small">Session</label>
                                <p class="fw-bold mb-1">{{ $currentTerm->session->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Term</label>
                                <p class="fw-bold mb-1">{{ $currentTerm->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Duration</label>
                                <p class="mb-1">{{ $currentTerm->start_date?->format('M d, Y') }} – {{ $currentTerm->end_date?->format('M d, Y') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">School Days</label>
                                <p class="fw-bold mb-1">{{ $currentTerm->total_school_days }} days</p>
                            </div>
                            <div class="mb-0">
                                <label class="text-muted small">Next Resumption</label>
                                <p class="fw-bold mb-1">{{ $currentTerm->next_resumption_date?->format('l, M d, Y') ?? 'Not set' }}</p>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">No current term set.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Term Modal -->
<div class="modal fade" id="editTermModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Term Calendar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="termCalendarForm" method="POST" action="{{ route('admin.settings.calendar.update') }}">
                @csrf
                <input type="hidden" name="term_id" id="editTermId">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Mid-Term Break Start</label>
                        <input type="date" name="mid_term_break_start" id="editBreakStart" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Mid-Term Break End</label>
                        <input type="date" name="mid_term_break_end" id="editBreakEnd" class="form-control">
                    </div>
                    <div class="form-group mb-0">
                        <label>Next Resumption Date</label>
                        <input type="date" name="next_resumption_date" id="editResumption" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Calendar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTerm(term) {
    document.getElementById('termCalendarForm').action = '{{ route("admin.settings.calendar.update") }}';
    document.getElementById('editTermId').value = term.id;
    document.getElementById('editBreakStart').value = term.mid_term_break_start ? term.mid_term_break_start.split('T')[0] : '';
    document.getElementById('editBreakEnd').value = term.mid_term_break_end ? term.mid_term_break_end.split('T')[0] : '';
    document.getElementById('editResumption').value = term.next_resumption_date ? term.next_resumption_date.split('T')[0] : '';
}
</script>

@endsection
