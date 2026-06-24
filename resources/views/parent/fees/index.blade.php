@extends('parent.layouts.app')

@section('title', 'Fees & Payments')
@section('page-title', 'Fees & Payments')
@section('page-sub', 'Outstanding and paid fees for ' . ($currentTerm?->name . ' — ' . $currentTerm?->session?->name ?? 'this term'))

@section('content')

@php
$grandTotal = 0; $grandPaid = 0; $grandOwed = 0;
$totalOwingChildren = 0;
foreach ($feesByChild as $child) {
    foreach ($child['categories'] as $fee) {
        $grandTotal += $fee['amount'];
        $grandPaid  += $fee['paid'];
        $grandOwed  += ($fee['amount'] - $fee['paid']);
    }
    $childOwed = collect($child['categories'])->sum(fn($f) => $f['amount'] - $f['paid']);
    if ($childOwed > 0) $totalOwingChildren++;
}
@endphp

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-3 md:gap-4 mb-5">
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-gray-400 to-gray-300"></div>
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">₦{{ number_format($grandTotal) }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Total Billed</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-green-500 to-emerald-400"></div>
    <p class="font-display text-2xl md:text-3xl font-bold text-green-700">₦{{ number_format($grandPaid) }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Total Paid</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-red-500 to-red-400"></div>
    <p class="font-display text-2xl md:text-3xl font-bold text-red-600">₦{{ number_format($grandOwed) }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Outstanding</p>
  </div>
</div>

{{-- Quick Pay All (if multiple children have fees) --}}
@if($totalOwingChildren > 1)
  <div class="bg-forest-50 border border-forest-200 rounded-2xl p-4 mb-5 flex items-center justify-between">
    <div>
      <p class="text-sm font-semibold text-forest-900">Pay for all children at once</p>
      <p class="text-xs text-forest-600">You have outstanding fees for {{ $totalOwingChildren }} children</p>
    </div>
    <a href="{{ route('parent.fees.checkout') }}" class="flex items-center gap-1.5 px-4 py-2 bg-forest-700 text-white text-sm font-bold rounded-xl hover:bg-forest-900 transition-colors">
      Pay All →
    </a>
  </div>
@endif

{{-- Per-child switcher --}}
@if(count($children) > 1)
  <div class="flex gap-2 flex-wrap mb-5 overflow-x-auto pb-1">
    @foreach($children as $i => $child)
      <button class="child-tab {{ $i===0?'active':'' }}" data-child-tab="{{ $child['id'] }}">
        <div class="child-tab-avatar">{{ strtoupper(substr($child['first_name'],0,1)) }}</div>
        <span class="child-tab-name">{{ $child['first_name'] }}</span>
      </button>
    @endforeach
  </div>
@endif

{{-- Fee breakdown per child --}}
@foreach($children as $i => $child)
  @php $fees = $feesByChild[$child['id']] ?? null; @endphp
  @if(!$fees) @continue @endif

  <div data-child-panel="{{ $child['id'] }}" {{ $i > 0 ? 'style=display:none' : '' }}>

    @php
      $owing = array_filter($fees['categories'], fn($f) => ($f['amount'] - $f['paid']) > 0);
      $paid  = array_filter($fees['categories'], fn($f) => ($f['amount'] - $f['paid']) <= 0);
      $childOwed = collect($fees['categories'])->sum(fn($f) => $f['amount'] - $f['paid']);
    @endphp

    @if(count($owing) > 0)
      <div class="bg-white rounded-2xl border border-gray-200 mb-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-red-50/40">
          <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Outstanding — {{ $fees['name'] }}
            <span class="text-xs font-normal text-gray-400 ml-1">({{ $fees['class'] }})</span>
          </h3>
          <a href="{{ route('parent.fees.checkout', ['child' => $child['id']]) }}" class="flex items-center gap-1.5 px-4 py-2 bg-forest-700 text-white text-sm font-bold rounded-xl hover:bg-forest-900 transition-colors">
            Pay Now →
          </a>
        </div>
        <div class="divide-y divide-gray-100">
          @foreach($owing as $fee)
            @php $balance = $fee['amount'] - $fee['paid']; @endphp
            <div class="flex flex-wrap items-center gap-4 px-5 py-4">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $fee['category'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $fee['description'] }}</p>
                @if($fee['paid'] > 0)
                  <p class="text-xs text-green-600 font-semibold mt-1">₦{{ number_format($fee['paid']) }} already paid</p>
                @endif
              </div>
              <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-red-600">₦{{ number_format($balance) }} due</p>
                <p class="text-xs text-gray-400 mt-0.5">of ₦{{ number_format($fee['amount']) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Due: {{ date('d M Y', strtotime($fee['due_date'])) }}</p>
              </div>
              @if($fee['paid'] > 0)
                <div class="w-full">
                  <div class="score-bar">
                    <div class="score-bar-fill" style="width: {{ round($fee['paid']/$fee['amount']*100) }}%"></div>
                  </div>
                  <p class="text-[10px] text-gray-400 mt-1">{{ round($fee['paid']/$fee['amount']*100) }}% paid</p>
                </div>
              @endif
            </div>
          @endforeach
        </div>
        @if($childOwed > 0)
          <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500 font-medium">Total due for {{ $fees['first_name'] }}</span>
            <span class="text-sm font-bold text-red-600">₦{{ number_format($childOwed) }}</span>
          </div>
        @endif
      </div>
    @endif

    @if(count($paid) > 0)
      <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="flex items-center px-5 py-4 border-b border-gray-100 bg-green-50/40">
          <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Paid — {{ $fees['name'] }}
          </h3>
        </div>
        <div class="divide-y divide-gray-100">
          @foreach($paid as $fee)
            <div class="flex flex-wrap items-center gap-4 px-5 py-4">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $fee['category'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $fee['description'] }}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-green-600">₦{{ number_format($fee['amount']) }}</p>
                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                  <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                  Paid
                </span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    @if(count($owing) === 0 && count($paid) === 0)
      <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
        <svg class="w-12 h-12 text-green-500 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <h3 class="font-display font-bold text-gray-900 mb-1">All Caught Up!</h3>
        <p class="text-gray-500 text-sm">{{ $fees['name'] }} has no fees for {{ $currentTerm?->name ?? 'this term' }}.</p>
      </div>
    @endif

  </div>
@endforeach

@endsection
