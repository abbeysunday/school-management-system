@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Fee Ledger — Student Overview</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-light" href="{{ route('admin.fees.structure') }}">
                            <i class="fe fe-grid me-1"></i> Fee Structure
                        </a>
                    </li>
                    <li>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                            <i class="fe fe-zap me-1"></i> Generate Ledger for Class
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end g-2">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label mb-1">Term <span class="text-danger">*</span></label>
                        <select name="term_id" class="form-control select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} ({{ $term->session->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label mb-1">Class / Arm</label>
                        <select name="class_arm_id" class="form-control select">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $armId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name ?? '' }} — {{ $arm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label mb-1">Search Student</label>
                        <input type="text" name="search" class="form-control" placeholder="Name or admission number…" value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        @if($summaries->count())
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    {{ $summaries->total() }} student(s) with ledger entries
                </h6>
                <div>
                    <span class="badge bg-danger me-1">Balance = outstanding</span>
                    <span class="badge bg-success">Balance = 0 means cleared</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Admission No.</th>
                                <th>Class</th>
                                <th class="text-end">Total Due (₦)</th>
                                <th class="text-end">Paid (₦)</th>
                                <th class="text-end">Balance (₦)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaries as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row['student']->user->full_name }}</strong>
                                </td>
                                <td>{{ $row['student']->admission_number }}</td>
                                <td>
                                    {{ $row['student']->currentEnrollment?->classArm?->classLevel?->name }}
                                    {{ $row['student']->currentEnrollment?->classArm?->name }}
                                </td>
                                <td class="text-end">{{ number_format($row['total_due'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['total_paid'], 2) }}</td>
                                <td class="text-end fw-bold {{ $row['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                                <td>
                                    @if($row['balance'] <= 0)
                                        <span class="badge bg-success">Cleared</span>
                                    @elseif($row['total_paid'] > 0)
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.fees.ledger.student', [$row['student'], 'term_id' => $termId]) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fe fe-eye me-1"></i>Breakdown
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3">Totals</td>
                                <td class="text-end">₦{{ number_format($summaries->sum('total_due'), 2) }}</td>
                                <td class="text-end">₦{{ number_format($summaries->sum('total_paid'), 2) }}</td>
                                <td class="text-end {{ $summaries->sum('balance') > 0 ? 'text-danger' : 'text-success' }}">
                                    ₦{{ number_format($summaries->sum('balance'), 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{ $summaries->links() }}
            </div>
        </div>
        @elseif($termId)
        <div class="alert alert-info">
            <i class="fe fe-info me-2"></i>
            No ledger entries found for the selected filters. Use <strong>"Generate Ledger for Class"</strong> to create entries.
        </div>
        @else
        <div class="alert alert-warning">
            <i class="fe fe-alert-triangle me-2"></i>
            Select a <strong>Term</strong> to view ledger data.
        </div>
        @endif
    </div>
</div>

{{-- Generate Ledger Modal --}}
<div class="modal fade" id="generateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Fee Ledger for Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fees.ledger.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        This generates ledger entries for all students enrolled in the selected class arm &amp; term.
                        Existing entries are never overwritten — only missing ones are created.
                    </p>
                    <div class="form-group mb-3">
                        <label>Term <span class="text-danger">*</span></label>
                        <select name="term_id" class="form-control select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} ({{ $term->session->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Class / Arm <span class="text-danger">*</span></label>
                        <select name="class_arm_id" class="form-control select" required>
                            <option value="">Select Class Arm</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $armId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name ?? '' }} — {{ $arm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-zap me-1"></i>Generate Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
