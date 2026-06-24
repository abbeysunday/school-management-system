@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper">
    <div class="content">

        {{-- Page Header --}}
        <div class="content-page-header content-page-headersplit">
            <h5>Record Payment</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}">
                            <i class="fe fe-arrow-left me-2"></i>Back to Payments
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.payments.store') }}" method="POST" id="paymentForm">
                            @csrf

                            {{-- Student Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required
                                    onchange="window.location.href='{{ route('admin.payments.create') }}?student_id=' + this.value">
                                    <option value="">— Select Student —</option>
                                    @foreach($students as $s)
                                        <option value="{{ $s->id }}" {{ $student?->id == $s->id ? 'selected' : '' }}>
                                            {{ $s->user->full_name }} ({{ $s->admission_number }}) — {{ $s->currentEnrollment?->classArm?->full_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            @if($student)
                                {{-- Student Info Banner --}}
                                <div class="alert alert-light border mb-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $student->user->photo_url }}" class="rounded-circle me-3" style="width:48px;height:48px;object-fit:cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $student->user->full_name }}</h6>
                                            <p class="mb-0 text-muted small">{{ $student->admission_number }} · {{ $student->currentEnrollment?->classArm?->full_name ?? 'N/A' }} · {{ $term?->name ?? 'No active term' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($ledgers->isEmpty())
                                    <div class="alert alert-success">
                                        <i class="fe fe-check-circle me-2"></i>
                                        This student has no outstanding fees for the current term.
                                    </div>
                                @else
                                    {{-- Fee Items --}}
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-semibold mb-0">Outstanding Fees <span class="text-danger">*</span></label>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" onclick="selectAllFees()">Select All</button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="clearAllFees()">Clear</button>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-hover border">
                                                <thead>
                                                    <tr>
                                                        <th style="width:40px;"></th>
                                                        <th>Fee Category</th>
                                                        <th class="text-end">Original</th>
                                                        <th class="text-end">Paid</th>
                                                        <th class="text-end">Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($ledgers as $ledger)
                                                        <tr class="fee-row" onclick="toggleFeeRow(this)" style="cursor:pointer;">
                                                            <td>
                                                                <div class="form-check">
                                                                    <input type="checkbox" name="ledger_ids[]" value="{{ $ledger['id'] }}"
                                                                        class="form-check-input fee-checkbox"
                                                                        data-balance="{{ $ledger['balance'] }}"
                                                                        onchange="event.stopPropagation(); calculateTotal();">
                                                                </div>
                                                            </td>
                                                            <td class="fw-semibold">{{ $ledger['category'] }}</td>
                                                            <td class="text-end">₦{{ number_format($ledger['original'], 2) }}</td>
                                                            <td class="text-end text-success">₦{{ number_format($ledger['paid'], 2) }}</td>
                                                            <td class="text-end fw-bold text-danger">₦{{ number_format($ledger['balance'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-end fw-bold">Total Selected:</td>
                                                        <td class="text-end fw-bold text-primary" id="selectedTotal">₦0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        @error('ledger_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Amount --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Amount Received <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₦</span>
                                            <input type="number" name="amount" id="amountInput" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" required readonly>
                                        </div>
                                        <div class="form-text">Auto-calculated from selected fee items.</div>
                                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Payment Method --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                        <div class="row g-2">
                                            @foreach($methods as $method)
                                                <div class="col-6 col-md-3">
                                                    <div class="form-check card p-3 border h-100">
                                                        <input type="radio" name="payment_method" id="method_{{ $loop->index }}" value="{{ $method }}"
                                                            class="form-check-input" {{ $loop->first ? 'checked' : '' }} required>
                                                        <label class="form-check-label w-100" for="method_{{ $loop->index }}">
                                                            <div class="fw-semibold">{{ $method }}</div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Paid Date --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', now()->format('Y-m-d')) }}" required>
                                        @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Notes --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Notes</label>
                                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="e.g. Bank teller number, cheque number, etc.">{{ old('notes') }}</textarea>
                                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Submit --}}
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fe fe-check me-1"></i>
                                            Record Payment & Generate Receipt
                                        </button>
                                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fe fe-user" style="width:48px;height:48px;" class="mb-3 opacity-25"></i>
                                    <p class="mb-0">Select a student to view their outstanding fees.</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Quick Tips --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">Quick Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Select a student to see their outstanding fees.</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Tick the fee items the parent is paying for.</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> The amount auto-calculates from selected items.</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Choose the payment method (Cash, Bank, Cheque, POS).</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> The receipt is generated instantly and can be printed.</li>
                            <li><i class="fe fe-check-circle me-1 text-success"></i> Payment is marked <strong>Verified</strong> immediately.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleFeeRow(row) {
    const cb = row.querySelector('.fee-checkbox');
    cb.checked = !cb.checked;
    row.classList.toggle('table-active', cb.checked);
    calculateTotal();
}

function selectAllFees() {
    document.querySelectorAll('.fee-checkbox').forEach(cb => {
        cb.checked = true;
        cb.closest('tr').classList.add('table-active');
    });
    calculateTotal();
}

function clearAllFees() {
    document.querySelectorAll('.fee-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').classList.remove('table-active');
    });
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.fee-checkbox:checked').forEach(cb => {
        total += parseFloat(cb.dataset.balance);
    });
    document.getElementById('selectedTotal').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('amountInput').value = total.toFixed(2);
}

// Style already-checked rows on load
document.querySelectorAll('.fee-checkbox:checked').forEach(cb => {
    cb.closest('tr').classList.add('table-active');
});
calculateTotal();
</script>

@endsection
