@extends('parent.layouts.app')

@section('title', 'Pay Fees')
@section('page-title', 'Pay Fees')
@section('page-sub', 'Select items and pay securely via Paystack')

@section('content')

@php
$totalPayable = collect($payableItems)->sum('amount');
@endphp

<div class="max-w-2xl mx-auto">

  <a href="{{ route('parent.fees.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-forest-700 mb-5 transition-colors">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Fee Overview
  </a>

  @if($payableItems)
  <form action="{{ route('parent.fees.pay') }}" method="POST" id="paymentForm">
    @csrf

    <div class="flex items-center justify-between mb-3">
      <h2 class="font-display text-base font-semibold text-gray-900">Select items to pay</h2>
      <div class="flex gap-3">
        <button type="button" onclick="selectAllFees()" class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">Select All</button>
        <span class="text-gray-300">|</span>
        <button type="button" onclick="deselectAllFees()" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition-colors">Clear</button>
      </div>
    </div>

    <div class="flex flex-col gap-3 mb-5" id="feeItems">
      @foreach($payableItems as $item)
        <div class="fee-item selected"
             data-fee-id="{{ $item['id'] }}"
             data-amount="{{ $item['amount'] }}"
             onclick="toggleFee(this)">
          <div class="fee-item-check">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900">{{ $item['category'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $item['child'] }} · {{ $item['description'] }}</p>
          </div>
          <p class="text-sm font-bold text-gray-900 flex-shrink-0">₦{{ number_format($item['amount']) }}</p>
          <input type="checkbox" name="ledger_ids[]" value="{{ $item['ledger_id'] }}" checked class="hidden">
        </div>
      @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
      <h3 class="font-display font-semibold text-gray-900 mb-4">Order Summary</h3>

      <div class="space-y-3 mb-4 pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500" id="checkout-count">{{ count($payableItems) }} items</span>
          <span class="font-semibold text-gray-900" id="checkout-subtotal">₦{{ number_format($totalPayable, 2) }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500">Paystack processing fee</span>
          <span class="text-gray-400 text-xs">Calculated at checkout</span>
        </div>
      </div>

      <div class="flex items-center justify-between mb-5">
        <span class="font-bold text-gray-900">Total</span>
        <span class="font-display font-bold text-xl text-gray-900" id="checkout-total">₦{{ number_format($totalPayable, 2) }}</span>
      </div>

      <input type="hidden" name="amount" id="totalAmount" value="{{ $totalPayable }}">

      <button type="submit" class="paystack-btn w-full flex items-center justify-center gap-2 py-4 px-6 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-900 transition-all shadow-lg shadow-forest-700/20">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Pay Securely with Paystack
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

<script>
function toggleFee(el) {
    el.classList.toggle('selected');
    const cb = el.querySelector('input[type="checkbox"]');
    cb.checked = el.classList.contains('selected');
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

function calculateTotal() {
    let total = 0;
    let count = 0;
    document.querySelectorAll('.fee-item.selected').forEach(el => {
        total += parseFloat(el.dataset.amount);
        count++;
    });
    document.getElementById('checkout-subtotal').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('checkout-total').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits:2});
    document.getElementById('checkout-count').textContent = count + ' item' + (count !== 1 ? 's' : '');
    document.getElementById('totalAmount').value = total;
}
</script>
@endsection
