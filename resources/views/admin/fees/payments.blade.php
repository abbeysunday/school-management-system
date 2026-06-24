@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Manual Payments</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.payments.create') }}">
                            <i class="fe fe-plus me-1"></i> Record Payment
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end g-2">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Class Arm</label>
                        <select name="class_arm_id" class="form-control select">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel?->name ?? '' }} {{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Method</label>
                        <select name="payment_method" class="form-control select">
                            <option value="">All Methods</option>
                            <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ request('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="POS" {{ request('payment_method') == 'POS' ? 'selected' : '' }}>POS</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-control select">
                            <option value="">All Statuses</option>
                            <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                            <option value="Refunded" {{ request('status') == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label mb-1">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, adm. no, receipt..." value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fe fe-filter me-1"></i> Filter
                        </button>
                    </div>
                    @if(request()->hasAny(['date_from','date_to','class_arm_id','payment_method','status','search']))
                        <div class="col-lg-2 col-md-6">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-light w-100">
                                <i class="fe fe-refresh-cw me-1"></i> Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Payments</h6>
                            <h4 class="mb-0">{{ $payments->total() }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-light-primary rounded-circle">
                            <i class="fe fe-credit-card fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Amount</h6>
                            <h4 class="mb-0">₦{{ number_format($totalAmount, 2) }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-light-success rounded-circle">
                            <i class="fe fe-dollar-sign fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Payment Records</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Method</th>
                                <th class="text-end">Amount (₦)</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                @php
                                    $student = $payment->student;
                                    $enrollment = $student?->currentEnrollment;
                                    $classArm = $enrollment?->classArm;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $payment->receipt_number ?? 'N/A' }}</strong>
                                        <div class="text-muted small">{{ $payment->payment_reference }}</div>
                                    </td>
                                    <td>{{ $payment->paid_at?->format('Y-m-d') ?? $payment->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <strong>{{ $student?->user?->full_name ?? 'N/A' }}</strong>
                                        <div class="text-muted small">{{ $student?->admission_number }}</div>
                                    </td>
                                    <td>
                                        {{ $classArm?->classLevel?->name ?? '' }} {{ $classArm?->arm ?? 'N/A' }}
                                    </td>
                                    <td>{{ $payment->term?->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $payment->payment_method }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->status === 'Verified')
                                            <span class="badge bg-success">Verified</span>
                                        @elseif($payment->status === 'Pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($payment->status === 'Failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($payment->status === 'Refunded')
                                            <span class="badge bg-secondary">Refunded</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->verifiedBy?->full_name ?? 'System' }}</td>
                                    <td>
                                        @if($payment->status === 'Verified')
                                            <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fe fe-printer me-1"></i> Receipt
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fe fe-inbox fs-4 d-block mb-2"></i>
                                        No payment records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
