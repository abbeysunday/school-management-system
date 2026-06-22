@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Fee Ledger Details</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.ledger.index', ['term_id' => $term?->id]) }}" class="btn btn-light">
                            <i class="fe fe-arrow-left me-2"></i>Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Student Summary Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-6 d-flex align-items-center gap-3">
                        <img src="{{ $student->user->photo_url }}" class="rounded-circle" width="60" height="60" style="object-fit:cover;">
                        <div>
                            <h4 class="mb-1">{{ $student->user->full_name }}</h4>
                            <p class="text-muted mb-0">{{ $student->admission_number }} • {{ $student->currentEnrollment?->classArm?->full_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-primary mb-0">₦{{ number_format($totalDue, 2) }}</h5>
                                <small class="text-muted">Total Due</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-success mb-0">₦{{ number_format($totalPaid, 2) }}</h5>
                                <small class="text-muted">Total Paid</small>
                            </div>
                            <div class="col-4">
                                <h5 class="{{ $balance > 0 ? 'text-danger' : 'text-success' }} mb-0">
                                    ₦{{ number_format($balance, 2) }}
                                </h5>
                                <small class="text-muted">Balance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ledger Items --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Fee Breakdown — {{ $term?->name }} ({{ $term?->session?->name }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Original</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Net Amount</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ledger as $item)
                            <tr>
                                <td>
                                    {{ $item->feeStructure->feeCategory->name }}
                                    @if($item->discount_reason)
                                        <br><small class="text-muted"><i class="fe fe-award"></i> {{ $item->discount_reason }}</small>
                                    @endif
                                </td>
                                <td class="text-end">₦{{ number_format($item->original_amount, 2) }}</td>
                                <td class="text-end {{ $item->discount_amount > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ $item->discount_amount > 0 ? '-₦'.number_format($item->discount_amount, 2) : '—' }}
                                </td>
                                <td class="text-end fw-bold">₦{{ number_format($item->net_amount, 2) }}</td>
                                <td class="text-end text-success">₦{{ number_format($item->amount_paid, 2) }}</td>
                                <td class="text-end {{ $item->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    ₦{{ number_format($item->balance, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status=='Paid'?'success':($item->status=='Partial'?'warning':'danger') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status != 'Paid')
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#discountModal"
                                        onclick="setDiscount({{ $item->id }}, {{ $item->original_amount }}, {{ $item->discount_amount }})">
                                        <i class="fe fe-award"></i> Discount
                                    </button>
                                    @endif
                                    @if($item->discount_amount > 0)
                                    <form action="{{ route('admin.ledger.discount.remove') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="ledger_id" value="{{ $item->id }}">
                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Remove discount?')">
                                            <i class="fe fe-x"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No ledger entries for this term.
                                    <a href="{{ route('admin.ledger.index') }}">Generate ledgers</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payment History for this student --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Payment History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Method</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->payments()->where('term_id', $term?->id)->latest()->get() as $payment)
                            <tr>
                                <td>{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                                <td><code>{{ $payment->payment_reference }}</code></td>
                                <td>{{ $payment->payment_method }}</td>
                                <td class="text-end">₦{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status=='Verified'?'success':($payment->status=='Pending'?'warning':'danger') }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No payments recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply Discount Modal -->
<div class="modal fade" id="discountModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply Scholarship / Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ledger.discount') }}" method="POST">
                @csrf
                <input type="hidden" name="ledger_id" id="discountLedgerId">
                <div class="modal-body">
                    <div class="alert alert-info">
                        Original amount: <strong id="discountOriginal">₦0.00</strong><br>
                        Current discount: <strong id="discountCurrent">₦0.00</strong>
                    </div>
                    <div class="form-group mb-3">
                        <label>Discount Amount (₦) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" required min="0.01">
                    </div>
                    <div class="form-group mb-0">
                        <label>Reason <span class="text-danger">*</span></label>
                        <input type="text" name="discount_reason" class="form-control" placeholder="e.g. Scholarship, Staff child, Orphanage" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setDiscount(id, original, current) {
    document.getElementById('discountLedgerId').value = id;
    document.getElementById('discountOriginal').textContent = '₦' + parseFloat(original).toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('discountCurrent').textContent = '₦' + parseFloat(current).toLocaleString('en-NG', {minimumFractionDigits:2});
}
</script>

@endsection
