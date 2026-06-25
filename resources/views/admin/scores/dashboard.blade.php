@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Score Entry Dashboard</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        @if($term->results_published ?? false)
                            <span class="badge bg-dark text-white px-3 py-2">
                                <i class="fe fe-check-circle me-1"></i>Results Published
                            </span>
                        @else
                            <form action="{{ route('admin.scores.publish') }}" method="POST" onsubmit="return confirm('Publish results for {{ $term->name }}? This will lock ALL score entry.')">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fe fe-check-circle me-2"></i>Publish Results
                                </button>
                            </form>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="dash-widget">
                    <div class="dash-content">
                        <h6>{{ $summary['total_subjects'] }}</h6>
                        <p>Total Subjects</p>
                    </div>
                    <div class="dash-widget-icon bg-info-light"><i class="ti ti-book"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="dash-widget">
                    <div class="dash-content">
                        <h6>{{ $summary['ca_complete'] }}</h6>
                        <p>CA Complete</p>
                    </div>
                    <div class="dash-widget-icon bg-success-light"><i class="ti ti-clipboard-check"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="dash-widget">
                    <div class="dash-content">
                        <h6>{{ $summary['exam_complete'] }}</h6>
                        <p>Exam Complete</p>
                    </div>
                    <div class="dash-widget-icon bg-primary-light"><i class="ti ti-file-check"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="dash-widget">
                    <div class="dash-content">
                        <h6>{{ $summary['published'] ?? 0 }}</h6>
                        <p>Published</p>
                    </div>
                    <div class="dash-widget-icon bg-dark-light"><i class="ti ti-check"></i></div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus" onchange="filterTable()">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="Ready for Submission">Ready for Submission</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Published">Published</option>
                            <option value="No Students">No Students</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchSubject" placeholder="Search subject or class..." onkeyup="filterTable()">
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Table --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="statusTable">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class Arm</th>
                                <th>Teacher</th>
                                <th>Students</th>
                                <th>CA Entry</th>
                                <th>Exam Entry</th>
                                <th>Overall</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statuses as $item)
                                @php
                                    $statusClass = match($item['overall_status']) {
                                        'Published' => 'bg-dark text-white',
                                        'Submitted' => 'bg-success-subtle text-success',
                                        'Ready for Submission' => 'bg-primary-subtle text-primary',
                                        'Pending' => 'bg-warning-subtle text-warning',
                                        'No Students' => 'bg-secondary-subtle text-secondary',
                                        default => 'bg-light',
                                    };
                                @endphp
                                <tr data-status="{{ $item['overall_status'] }}" data-search="{{ strtolower($item['subject']->name . ' ' . $item['class_arm']->full_name . ' ' . $item['teacher_name']) }}">
                                    <td>
                                        <div class="fw-semibold">{{ $item['subject']->name }}</div>
                                        <small class="text-muted">{{ $item['subject']->code ?? '' }}</small>
                                    </td>
                                    <td>{{ $item['class_arm']->full_name }}</td>
                                    <td>{{ $item['teacher_name'] }}</td>
                                    <td class="text-center">{{ $item['total_students'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-fill" style="height:6px;">
                                                <div class="progress-bar {{ $item['ca_percent'] == 100 ? 'bg-success' : 'bg-warning' }}" style="width:{{ $item['ca_percent'] }}%"></div>
                                            </div>
                                            <span class="small" style="min-width:40px;">{{ $item['ca_percent'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-fill" style="height:6px;">
                                                <div class="progress-bar {{ $item['exam_percent'] == 100 ? 'bg-success' : 'bg-info' }}" style="width:{{ $item['exam_percent'] }}%"></div>
                                            </div>
                                            <span class="small" style="min-width:40px;">{{ $item['exam_percent'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ $item['overall_status'] }}</span>
                                        @if($item['is_published'] ?? false)
                                            <i class="ti ti-check text-dark ms-1" title="Published"></i>
                                        @elseif($item['is_submitted'])
                                            <i class="ti ti-lock text-warning ms-1" title="Locked"></i>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.scores.ca-edit', $item['arm_subject']->id) }}">
                                                        <i class="ti ti-clipboard-list me-2 text-primary"></i>Edit CA Scores
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.scores.exam-edit', $item['arm_subject']->id) }}">
                                                        <i class="ti ti-file-text me-2 text-success"></i>Edit Exam Scores
                                                    </a>
                                                </li>
                                                @if($item['is_submitted'] && !($item['is_published'] ?? false))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.scores.unlock', $item['arm_subject']->id) }}" method="POST" onsubmit="return confirm('Unlock scores for {{ $item['subject']->name }} — {{ $item['class_arm']->full_name }}?')">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="ti ti-lock-open me-2"></i>Unlock for Editing
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="ti ti-clipboard-x text-muted" style="font-size:36px;"></i>
                                        <p class="text-muted mt-2">No subjects configured for this session.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function filterTable() {
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('searchSubject').value.toLowerCase();

    document.querySelectorAll('#statusTable tbody tr').forEach(row => {
        const rowStatus = row.dataset.status;
        const rowSearch = row.dataset.search;
        const matchStatus = !status || rowStatus === status;
        const matchSearch = !search || rowSearch.includes(search);
        row.style.display = (matchStatus && matchSearch) ? '' : 'none';
    });
}
</script>

@endsection
