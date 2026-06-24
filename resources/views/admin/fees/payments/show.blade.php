@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper">
    <div class="content">

        {{-- Page Header --}}
        <div class="content-page-header content-page-headersplit">
            <h5>Payment Details</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-success" href="{{ route('admin.payments.receipt', $payment) }}" target="_blank">
                            <i class="fe fe-printer me-2"></i>Print Receipt
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}">
                            <i class="fe fe-arrow-left me-2"></i>Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Payment Information</h5>
                        <span class="badge bg-{{ $payment->status == 'Verified' ? 'success' : ($payment->status == 'Pending' ? 'warning' : 'danger') }} fs-6">
                            {{ $payment->status }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Receipt Number</p>
                                <p class="fw-bold font-monospace fs-5 text-primary">{{ $payment->receipt_number ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Payment Reference</p>
                                <p class="fw-bold font-monospace">{{ $payment->payment_reference }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Amount</p>
                                <p class="fw-bold fs-4">₦{{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Payment Method</p>
                                <p class="fw-bold">{{ $payment->payment_method }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Date Paid</p>
                                <p class="fw-bold">{{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Date Verified</p>
                                <p class="fw-bold">{{ $payment->verified_at?->format('d M Y, h:i A') ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Recorded By</p>
                                <p class="fw-bold">{{ $payment->verifiedBy?->full_name ?? $payment->paidBy?->full_name ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small">Term</p>
                                <p class="fw-bold">{{ $payment->term?->name ?? '—' }} — {{ $payment->term?->session?->name ?? '' }}</p>
                            </div>
                            @if($payment->notes)
                                <div class="col-12">
                                    <p class="text-muted mb-1 small">Notes</p>
                                    <div class="alert alert-light border">{{ $payment->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Allocations --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">Fee Allocation Breakdown</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fee Category</th>
                                        <th class="text-end">Allocated</th>
                                        <th class="text-end">Previous Balance</th>
                                        <th class="text-end">New Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->allocations as $i => $alloc)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $alloc->ledger->feeStructure->feeCategory->name }}</td>
                                            <td class="text-end fw-bold">₦{{ number_format($alloc->amount_allocated, 2) }}</td>
                                            <td class="text-end">₦{{ number_format($alloc->ledger->net_amount - ($alloc->ledger->amount_paid - $alloc->amount_allocated), 2) }}</td>
                                            <td class="text-end">
                                                @php $newBal = $alloc->ledger->net_amount - $alloc->ledger->amount_paid; @endphp
                                                <span class="badge bg-{{ $newBal <= 0 ? 'success' : 'warning' }}">
                                                    ₦{{ number_format($newBal, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Sidebar --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ $payment->student->user->photo_url }}" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;">
                        <h5 class="fw-bold mb-1">{{ $payment->student->user->full_name }}</h5>
                        <p class="text-muted mb-2">{{ $payment->student->admission_number }}</p>
                        <p class="mb-0"><span class="badge bg-light text-dark border">{{ $payment->student->currentEnrollment?->classArm?->full_name ?? 'N/A' }}</span></p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-success w-100 mb-2" target="_blank">
                            <i class="fe fe-printer me-1"></i> Print Receipt
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fe fe-arrow-left me-1"></i> Back to Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
