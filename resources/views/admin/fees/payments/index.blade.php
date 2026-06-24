@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        {{-- Page Header --}}
        <div class="content-page-header content-page-headersplit">
            <h5>Payments</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.payments.create') }}">
                            <i class="fe fe-plus me-2"></i>Record Payment
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span><i class="fe fe-credit-card"></i></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format($stats['total']) }}</h5>
                                <h6>Total Payments</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span class="bg-success"><i class="fe fe-check-circle text-white"></i></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format($stats['verified']) }}</h5>
                                <h6>Verified</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span class="bg-warning"><i class="fe fe-dollar-sign text-white"></i></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($stats['today'], 2) }}</h5>
                                <h6>Today</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span class="bg-info"><i class="fe fe-calendar text-white"></i></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($stats['this_term'], 2) }}</h5>
                                <h6>This Term</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ref, receipt, name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Verified" {{ request('status')=='Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Pending" {{ request('status')=='Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Failed" {{ request('status')=='Failed' ? 'selected' : '' }}>Failed</option>
                            <option value="Refunded" {{ request('status')=='Refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Method</label>
                        <select name="method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="Paystack" {{ request('method')=='Paystack' ? 'selected' : '' }}>Paystack</option>
                            <option value="Cash" {{ request('method')=='Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ request('method')=='Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ request('method')=='Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="POS" {{ request('method')=='POS' ? 'selected' : '' }}>POS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Class Arm</label>
                        <select name="class_arm_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ request('class_arm_id')==$arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fe fe-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-x"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Recorded By</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-bold text-primary">{{ $payment->receipt_number ?? '—' }}</span>
                                        <div class="text-muted" style="font-size:11px;">{{ $payment->payment_reference }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $payment->student->user->photo_url }}" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                                            <div>
                                                <div class="fw-semibold">{{ $payment->student->user->full_name }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $payment->student->admission_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $payment->student->currentEnrollment?->classArm?->full_name ?? 'N/A' }}</td>
                                    <td class="fw-bold">₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->payment_method == 'Paystack' ? 'info' : ($payment->payment_method == 'Cash' ? 'success' : 'secondary') }}">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'Verified' => 'success',
                                                'Pending' => 'warning',
                                                'Failed' => 'danger',
                                                'Refunded' => 'dark',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                            {{ $payment->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $payment->paid_at?->format('d M Y') ?? '—' }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ $payment->term?->name }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $payment->verifiedBy?->full_name ?? $payment->paidBy?->full_name ?? '—' }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fe fe-eye" style="width:14px;height:14px;"></i>
                                            </a>
                                            @if($payment->status === 'Verified' && $payment->receipt_number)
                                                <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-outline-success" title="Receipt" target="_blank">
                                                    <i class="fe fe-printer" style="width:14px;height:14px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fe fe-inbox" style="width:40px;height:40px;" class="mb-2 opacity-25"></i>
                                        <p class="mb-0">No payments found matching your filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
                <div class="card-footer">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
