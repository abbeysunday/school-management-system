@extends('parent.layouts.app')

@section('title', 'Payment Successful')
@section('page-title', 'Payment Successful')
@section('page-sub', 'Your payment has been confirmed')

@section('content')

<div class="max-w-lg mx-auto text-center py-6">

  <div class="success-icon w-20 h-20 rounded-full bg-green-100 border-4 border-green-200 flex items-center justify-center mx-auto mb-5">
    <svg class="w-9 h-9 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  </div>

  <h1 class="font-display text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
  <p class="text-gray-500 mb-6">Your payment of <strong class="text-gray-900">₦{{ number_format($amountPaid) }}</strong> has been confirmed.</p>

  <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5 text-left">
    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
      <div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Reference</p>
        <p class="text-sm font-bold text-gray-900 font-mono">{{ $reference }}</p>
      </div>
      <div class="text-right">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date</p>
        <p class="text-sm text-gray-600">{{ now()->format('d M Y, H:i') }}</p>
      </div>
    </div>

    <div class="space-y-3 mb-4 pb-4 border-b border-gray-100">
      @forelse($itemsPaid as $item)
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-semibold text-gray-900">{{ $item['category'] }}</p>
            <p class="text-xs text-gray-400">{{ $item['child'] }}</p>
          </div>
          <p class="text-sm font-bold text-gray-900">₦{{ number_format($item['amount']) }}</p>
        </div>
      @empty
        <p class="text-sm text-gray-500">General payment recorded.</p>
      @endforelse
    </div>

    <div class="flex items-center justify-between">
      <p class="font-bold text-gray-900">Total Paid</p>
      <p class="font-display font-bold text-xl text-green-700">₦{{ number_format($amountPaid) }}</p>
    </div>
  </div>

  <div class="flex flex-col sm:flex-row gap-3">
    <a href="{{ route('parent.fees.receipt', $reference) }}" class="download-btn flex-1">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download Receipt
    </a>
    <a href="{{ route('parent.fees.history') }}" class="flex-1 flex items-center justify-center gap-2 py-3.5 px-5 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-700 hover:border-gray-300 hover:bg-gray-50 transition-all">
      Payment History
    </a>
  </div>

  <div class="mt-5">
    <a href="{{ route('parent.dashboard') }}" class="text-sm text-forest-700 font-semibold hover:text-gold-600 transition-colors">
      ← Back to Dashboard
    </a>
  </div>

</div>
@endsection
