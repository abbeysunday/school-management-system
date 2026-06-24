@extends('parent.layouts.app')

@section('title', 'Pay Fees')
@section('page-title', 'Pay Fees')
@section('page-sub', 'Select items and pay securely via Paystack')

@section('content')

@php
$hasPayable = collect($payableByChild)->sum(fn($c) => count($c['items'])) > 0;
$totalPayable = collect($payableItems)->sum('amount');
@endphp

<div class="max-w-2xl mx-auto">

  <a href="{{ route('parent.fees.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-forest-700 mb-5 transition-colors">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Fee Overview
  </a>

  @if($hasPayable)

    {{-- Child Selector --}}
    @if(count($children) > 1)
      <div class="flex gap-2 flex-wrap mb-5 overflow-x-auto pb-1">
        <button class="child-tab {{ is_null($selectedChildId) ? 'active' : '' }}" onclick="filterChild(null)">
          <div class="child-tab-avatar">A</div>
          <span class="child-tab-name">All</span>
        </button>
        @foreach($children as $child)
          <button class="child-tab {{ $selectedChildId == $child->id ? 'active' : '' }}" onclick="filterChild({{ $child->id }})">
            <div class="child-tab-avatar">{{ strtoupper(substr($child->user->first_name,0,1)) }}</div>
            <span class="child-tab-name">{{ $child->user->first_name }}</span>
          </button>
        @endforeach
      </div>
    @endif

    <form id="paymentForm" onsubmit="return false;">

      {{-- Fee items grouped by child --}}
      <div id="feeItemsContainer" class="flex flex-col gap-4 mb-5">
        @foreach($payableByChild as $childId => $childData)
          @if(count($childData['items']) === 0) @continue @endif

          <div class="child-fee-group" data-child-id="{{ $childId }}" data-child-name="{{ $childData['first_name'] }}">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-display text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-forest-100 text-forest-700 text-xs font-bold flex items-center justify-center">
                  {{ strtoupper(substr($childData['first_name'],0,1)) }}
                </span>
                {{ $childData['name'] }}
                <span class="text-xs font-normal text-gray-400">({{ $childData['class'] }})</span>
              </h3>
              <div class="flex gap-2">
                <button type="button" onclick="selectAllForChild({{ $childId }})" class="text-[11px] font-semibold text-forest-700 hover:text-gold-600 transition-colors">Select All</button>
                <span class="text-gray-300">|</span>
                <button type="button" onclick="clearAllForChild({{ $childId }})" class="text-[11px] font-semibold text-gray-400 hover:text-gray-600 transition-colors">Clear</button>
              </div>
            </div>

            <div class="flex flex-col gap-2" id="childItems-{{ $childId }}">
              @foreach($childData['items'] as $item)
                <div class="fee-item selected"
                     data-fee-id="{{ $item['id'] }}"
                     data-amount="{{ $item['amount'] }}"
                     data-child-id="{{ $childId }}"
                     onclick="toggleFee(this)">
                  <div class="fee-item-check">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ $item['category'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $item['description'] }}</p>
                  </div>
                  <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-gray-900">₦{{ number_format($item['amount']) }}</p>
                  </div>
                  <input type="checkbox" name="ledger_ids[]" value="{{ $item['ledger_id'] }}" checked class="hidden">
                </div>
              @endforeach
            </div>

            @if($childData['subtotal'] > 0)
              <div class="text-right mt-1 px-1">
                <span class="text-xs text-gray-400">Subtotal: </span>
                <span class="text-xs font-bold text-gray-700" id="child-subtotal-{{ $childId }}">₦{{ number_format($childData['subtotal']) }}</span>
              </div>
            @endif
          </div>
        @endforeach
      </div>

      {{-- Global actions --}}
      <div class="flex items-center justify-between mb-5 px-1">
        <div class="flex gap-3">
          <button type="button" onclick="selectAllFees()" class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">Select All</button>
          <span class="text-gray-300">|</span>
          <button type="button" onclick="deselectAllFees()" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition-colors">Clear All</button>
        </div>
        <span class="text-xs text-gray-400" id="global-item-count">{{ count($payableItems) }} items available</span>
      </div>

      {{-- Order Summary --}}
      <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
        <h3 class="font-display font-semibold text-gray-900 mb-4">Order Summary</h3>

        <div class="space-y-3 mb-4 pb-4 border-b border-gray-100" id="summaryLines">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500" id="checkout-count">0 items selected</span>
            <span class="font-semibold text-gray-900" id="checkout-subtotal">₦0.00</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Paystack processing fee</span>
            <span class="text-gray-400 text-xs">Included</span>
          </div>
        </div>

        <div class="flex items-center justify-between mb-5">
          <span class="font-bold text-gray-900">Total</span>
          <span class="font-display font-bold text-xl text-gray-900" id="checkout-total">₦0.00</span>
        </div>

        <input type="hidden" name="amount" id="totalAmount" value="0">

        {{-- Error message container --}}
        <div id="paymentError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"></div>

        <button type="button" id="payBtn" onclick="payWithPaystack()" class="paystack-btn w-full flex items-center justify-center gap-2 py-4 px-6 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-900 transition-all shadow-lg shadow-forest-700/20 disabled:opacity-50 disabled:cursor-not-allowed">
          <svg id="payBtnIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <span id="payBtnText">Pay Securely with Paystack</span>
          <svg id="payBtnSpinner" class="hidden w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M2 12h4m12 0h4"/></svg>
        </button>

        <p class="text-center text-xs text-gray-400 mt-3">
          🔒 Secured by Paystack · Your card details are never stored
        </p>
      </div>
    </form>

  @else
    <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
      <svg class="w-12 h-12 text-green-500 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <h3 class="font-display font-bold text-gray-900 mb-1">All Caught Up!</h3>
      <p class="text-gray-500 text-sm">You have no outstanding fees for {{ $currentTerm?->name ?? 'this term' }}.</p>
      <a href="{{ route('parent.fees.history') }}" class="inline-block mt-4 text-sm text-forest-700 font-semibold hover:text-gold-600">View Payment History →</a>
    </div>
  @endif

  <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
    <p class="font-semibold mb-1 flex items-center gap-1.5">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      Payment Information
    </p>
    <ul class="space-y-1 text-xs text-blue-700">
      <li>• Payments are processed in NGN via Paystack.</li>
      <li>• You will receive an automated receipt by email after payment.</li>
      <li>• Allow up to 24 hours for your balance to update on the portal.</li>
      <li>• For issues, contact the bursary at {{ $school?->email ?? 'bursary@school.edu' }}</li>
    </ul>
  </div>

</div>

{{-- Paystack Inline JS --}}
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
// ── Child filtering ──────────────────────────────────────
function filterChild(childId) {
    if (!childId) {
        document.querySelectorAll('.child-fee-group').forEach(el => el.style.display = '');
        document.querySelectorAll('.child-tab').forEach((tab, i) => tab.classList.toggle('active', i === 0));
    } else {
        document.querySelectorAll('.child-fee-group').forEach(el => {
            el.style.display = (parseInt(el.dataset.childId) === childId) ? '' : 'none';
        });
        document.querySelectorAll('.child-tab').forEach(tab => {
            const tabChildId = tab.getAttribute('onclick')?.match(/\d+/)?.[0];
            tab.classList.toggle('active', parseInt(tabChildId) === childId);
        });
    }
    calculateTotal();
}

// ── Fee selection ────────────────────────────────────────
function toggleFee(el) {
    el.classList.toggle('selected');
    const cb = el.querySelector('input[type="checkbox"]');
    cb.checked = el.classList.contains('selected');
    calculateTotal();
}

function selectAllForChild(childId) {
    document.querySelectorAll(`.fee-item[data-child-id="${childId}"]`).forEach(el => {
        el.classList.add('selected');
        el.querySelector('input[type="checkbox"]').checked = true;
    });
    calculateTotal();
}

function clearAllForChild(childId) {
    document.querySelectorAll(`.fee-item[data-child-id="${childId}"]`).forEach(el => {
        el.classList.remove('selected');
        el.querySelector('input[type="checkbox"]').checked = false;
    });
    calculateTotal();
}

function selectAllFees() {
    document.querySelectorAll('.fee-item').forEach(el => {
        el.classList.add('selected');
        el.querySelector('input[type="checkbox"]').checked = true;
    });
    calculateTotal();
}

function deselectAllFees() {
    document.querySelectorAll('.fee-item').forEach(el => {
        el.classList.remove('selected');
        el.querySelector('input[type="checkbox"]').checked = false;
    });
    calculateTotal();
}

// ── Total calculation ────────────────────────────────────
function calculateTotal() {
    let total = 0;
    let count = 0;

    document.querySelectorAll('.fee-item.selected').forEach(el => {
        total += parseFloat(el.dataset.amount);
        count++;
    });

    // Update per-child subtotals
    document.querySelectorAll('.child-fee-group').forEach(group => {
        const childId = group.dataset.childId;
        let childTotal = 0;
        group.querySelectorAll('.fee-item.selected').forEach(el => {
            childTotal += parseFloat(el.dataset.amount);
        });
        const subEl = document.getElementById('child-subtotal-' + childId);
        if (subEl) subEl.textContent = '₦' + childTotal.toLocaleString('en-NG');
    });

    document.getElementById('checkout-subtotal').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('checkout-total').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('checkout-count').textContent = count + ' item' + (count !== 1 ? 's' : '') + ' selected';
    document.getElementById('totalAmount').value = total;

    // Disable pay button if nothing selected
    const payBtn = document.getElementById('payBtn');
    payBtn.disabled = (count === 0);
    payBtn.style.opacity = (count === 0) ? '0.5' : '1';
}

// ── Paystack Inline Payment ──────────────────────────────
function payWithPaystack() {
    const selected = document.querySelectorAll('.fee-item.selected');
    if (selected.length === 0) {
        showError('Please select at least one fee item to pay.');
        return;
    }

    const ledgerIds = Array.from(selected).map(el => el.querySelector('input[type="checkbox"]').value);
    const amount = parseFloat(document.getElementById('totalAmount').value);

    // Check if multiple children selected
    const childIds = new Set(Array.from(selected).map(el => el.dataset.childId));
    if (childIds.size > 1) {
        showError('Please select fees for one child at a time. Use the child tabs above to filter.');
        return;
    }

    setLoading(true);
    hideError();

    fetch('{{ route('parent.fees.pay') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            ledger_ids: ledgerIds,
            amount: amount
        })
    })
    .then(r => r.json())
    .then(data => {
        setLoading(false);
        if (!data.success) {
            showError(data.message || 'Payment initialization failed.');
            return;
        }

        // Open Paystack inline
        const handler = PaystackPop.setup({
            key: data.data.public_key,
            email: data.data.email,
            amount: data.data.amount,
            ref: data.data.reference,
            currency: 'NGN',
            metadata: {
                custom_fields: [
                    { display_name: "Student", variable_name: "student_name", value: data.data.student_name || '' }
                ]
            },
            callback: function(response) {
                window.location.href = '{{ route('parent.fees.callback') }}?reference=' + encodeURIComponent(response.reference);
            },
            onClose: function() {
                showError('Payment window was closed. If you completed the payment, please check your history.');
            }
        });
        handler.openIframe();
    })
    .catch(err => {
        setLoading(false);
        showError('Network error. Please check your connection and try again.');
        console.error(err);
    });
}

// ── UI Helpers ───────────────────────────────────────────
function setLoading(isLoading) {
    const btn = document.getElementById('payBtn');
    const icon = document.getElementById('payBtnIcon');
    const text = document.getElementById('payBtnText');
    const spinner = document.getElementById('payBtnSpinner');

    btn.disabled = isLoading;
    if (isLoading) {
        icon.classList.add('hidden');
        text.textContent = 'Processing...';
        spinner.classList.remove('hidden');
    } else {
        icon.classList.remove('hidden');
        text.textContent = 'Pay Securely with Paystack';
        spinner.classList.add('hidden');
    }
}

function showError(msg) {
    const el = document.getElementById('paymentError');
    el.textContent = msg;
    el.classList.remove('hidden');
}

function hideError() {
    document.getElementById('paymentError').classList.add('hidden');
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();

    // Pre-filter if child was passed in URL
    @if($selectedChildId)
        filterChild({{ $selectedChildId }});
    @endif
});
</script>
@endsection
