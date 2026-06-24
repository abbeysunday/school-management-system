@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Record Manual Payment</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-light" href="{{ route('admin.payments.index') }}">
                            <i class="fe fe-arrow-left me-1"></i> Back to Payments
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.payments.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label>Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-control select @error('student_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $s)
                                        <option value="{{ $s['id'] }}"
                                            {{ old('student_id', $preselectedStudent?->id) == $s['id'] ? 'selected' : '' }}>
                                            {{ $s['admission_number'] }} — {{ $s['name'] }} ({{ $s['class'] ?: 'No class' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            @if($preselectedStudent && $currentTerm)
                                <div class="alert alert-info mb-3">
                                    <i class="fe fe-info me-2"></i>
                                    <strong>{{ $preselectedStudent->user->full_name }}</strong>
                                    currently has an outstanding balance of
                                    <strong>₦{{ number_format($preselectedBalance ?? 0, 2) }}</strong>
                                    for {{ $currentTerm->name }}.
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Term <span class="text-danger">*</span></label>
                                        <select name="term_id" class="form-control select @error('term_id') is-invalid @enderror" required>
                                            <option value="">Select Term</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term->id }}" {{ old('term_id', $currentTerm?->id) == $term->id ? 'selected' : '' }}>
                                                    {{ $term->name }} ({{ $term->session?->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('term_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Amount (₦) <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" step="0.01" min="1"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount') }}" required>
                                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="form-control select @error('payment_method') is-invalid @enderror" required>
                                            <option value="">Select Method</option>
                                            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="POS" {{ old('payment_method') == 'POS' ? 'selected' : '' }}>POS</option>
                                        </select>
                                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="paid_at"
                                               class="form-control @error('paid_at') is-invalid @enderror"
                                               value="{{ old('paid_at', now()->format('Y-m-d')) }}" required>
                                        @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Notes</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-success">
                                    <i class="fe fe-save me-1"></i> Save & Allocate Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fe fe-info me-1"></i> How it works</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fe fe-check text-success me-2"></i>
                                The payment is marked <strong>Verified</strong> immediately.
                            </li>
                            <li class="mb-2">
                                <i class="fe fe-check text-success me-2"></i>
                                The amount is auto-allocated to unpaid fee items (oldest first).
                            </li>
                            <li class="mb-2">
                                <i class="fe fe-check text-success me-2"></i>
                                A sequential receipt number like <strong>RCP/{{ now()->year }}/0001</strong> is generated.
                            </li>
                            <li class="mb-0">
                                <i class="fe fe-check text-success me-2"></i>
                                You can print the receipt from the payments list.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
