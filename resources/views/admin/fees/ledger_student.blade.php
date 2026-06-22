@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="content-page-header content-page-headersplit mb-3">
            <div>
                <h5 class="mb-0">Fee Breakdown — {{ $student->user->full_name }}</h5>
                <small class="text-muted">
                    {{ $student->admission_number }} &bull;
                    {{ $student->currentEnrollment?->classArm?->classLevel?->name }}
                    {{ $student->currentEnrollment?->classArm?->name }}
                </small>
            </div>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.fees.ledger', ['term_id' => $termId]) }}"
                           class="btn btn-light"><i class="fe fe-arrow-left me-1"></i>Back to Ledger</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Term Selector --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row align-items-center g-2">
                    <div class="col-auto">
                        <label class="col-form-label">View Term:</label>
                    </div>
                    <div class="col-lg-4">
                        <select name="term_id" class="form-control select" onchange="this.form.submit()">
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} ({{ $term->session->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        @if($termId && count($ledger) > 0)

        {{-- Summary Cards --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-center border-0 bg-light">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Total Fees</p>
                        <h5 class="mb-0">₦{{ number_format($totals['due'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 bg-warning bg-opacity-10">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Discounts</p>
                        <h5 class="mb-0 text-warning">₦{{ number_format($totals['discount'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 bg-success bg-opacity-10">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Amount Paid</p>
                        <h5 class="mb-0 text-success">₦{{ number_format($totals['paid'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 {{ $totals['balance'] > 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-10">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Outstanding Balance</p>
                        <h5 class="mb-0 {{ $totals['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            ₦{{ number_format($totals['balance'], 2) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ledger Table --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Fee Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Item</th>
                                <th>Due Date</th>
                                <th class="text-end">Original (₦)</th>
                                <th class="text-end">Discount (₦)</th>
                                <th class="text-end">Net Due (₦)</th>
                                <th class="text-end">Paid (₦)</th>
                                <th class="text-end">Balance (₦)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledger as $entry)
                            <tr>
                                <td>
                                    <strong>{{ $entry->feeStructure->feeCategory->name }}</strong>
                                    @if($entry->discount_reason)
                                        <br><small class="text-success"><i class="fe fe-tag me-1"></i>{{ $entry->discount_reason }}</small>
                                    @endif
                                </td>
                                <td>{{ $entry->feeStructure->due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">{{ number_format($entry->original_amount, 2) }}</td>
                                <td class="text-end {{ $entry->discount_amount > 0 ? 'text-success' : '' }}">
                                    {{ $entry->discount_amount > 0 ? '-' : '' }}{{ number_format($entry->discount_amount, 2) }}
                                </td>
                                <td class="text-end">{{ number_format($entry->net_amount, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($entry->amount_paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $entry->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($entry->balance, 2) }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($entry->status) {
                                            'Paid'    => 'bg-success',
                                            'Partial' => 'bg-warning',
                                            default   => 'bg-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $entry->status }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#discountModal{{ $entry->id }}">
                                        <i class="fe fe-percent"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2">Totals</td>
                                <td class="text-end">₦{{ number_format($totals['due'], 2) }}</td>
                                <td class="text-end text-success">-₦{{ number_format($totals['discount'], 2) }}</td>
                                <td class="text-end">₦{{ number_format($totals['net'], 2) }}</td>
                                <td class="text-end text-success">₦{{ number_format($totals['paid'], 2) }}</td>
                                <td class="text-end {{ $totals['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    ₦{{ number_format($totals['balance'], 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Discount Modals --}}
        @foreach($ledger as $entry)
        <div class="modal fade" id="discountModal{{ $entry->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Apply Discount — {{ $entry->feeStructure->feeCategory->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.fees.ledger.discount', $entry) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Original</small>
                                        <strong>₦{{ number_format($entry->original_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Current Discount</small>
                                        <strong class="text-warning">₦{{ number_format($entry->discount_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Net Due</small>
                                        <strong class="text-primary">₦{{ number_format($entry->net_amount, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Discount Amount (₦) <span class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" step="0.01" min="0"
                                       max="{{ $entry->original_amount }}"
                                       class="form-control"
                                       value="{{ $entry->discount_amount }}"
                                       required>
                                <small class="text-muted">Max: ₦{{ number_format($entry->original_amount, 2) }}</small>
                            </div>
                            <div class="form-group mb-0">
                                <label>Reason / Scholarship Note <span class="text-danger">*</span></label>
                                <textarea name="discount_reason" rows="2" class="form-control"
                                          placeholder="e.g. Staff child discount, Scholarship award…"
                                          required>{{ $entry->discount_reason }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            @if($entry->discount_amount > 0)
                            <form action="{{ route('admin.fees.ledger.discount.remove', $entry) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Remove this discount?')">
                                    Remove Discount
                                </button>
                            </form>
                            @else
                            <span></span>
                            @endif
                            <div>
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning">Apply Discount</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        @elseif($termId)
        <div class="alert alert-info">
            <i class="fe fe-info me-2"></i>
            No ledger entries found for this student in the selected term. Generate the ledger first from the
            <a href="{{ route('admin.fees.ledger') }}">Ledger Overview</a> page.
        </div>
        @else
        <div class="alert alert-warning">
            <i class="fe fe-alert-triangle me-2"></i> Select a term to view this student's fee breakdown.
        </div>
        @endif

    </div>
</div>

@endsection
