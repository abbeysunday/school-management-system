@extends('parent.layouts.app')

@section('title', 'Payment History')
@section('page-title', 'Payment History')
@section('page-sub', 'All past transactions and receipts')

@php
$payments = [
    ['ref'=>'NSM_2024_OCT_001','date'=>'2024-10-14','child'=>'Chidinma Okafor','items'=>'School Fees (Term 1) — Balance','amount'=>45000,'status'=>'success','channel'=>'Card'],
    ['ref'=>'NSM_2024_SEP_002','date'=>'2024-09-02','child'=>'Chidinma Okafor','items'=>'School Fees (Term 1) — Deposit + ICT Fee + Dev Levy','amount'=>93000,'status'=>'success','channel'=>'Transfer'],
    ['ref'=>'NSM_2024_SEP_001','date'=>'2024-09-01','child'=>'Emeka Okafor',   'items'=>'School Fees (Term 1) Full + WAEC + NECO + ICT','amount'=>148000,'status'=>'success','channel'=>'Card'],
    ['ref'=>'NSM_2024_JAN_003','date'=>'2024-01-20','child'=>'Chidinma Okafor','items'=>'School Fees (Term 2 2023) Balance','amount'=>40000,'status'=>'success','channel'=>'Card'],
    ['ref'=>'NSM_2024_JAN_002','date'=>'2024-01-08','child'=>'Emeka Okafor',   'items'=>'School Fees (Term 2 2023) Full','amount'=>90000,'status'=>'success','channel'=>'Transfer'],
    ['ref'=>'NSM_2023_SEP_001','date'=>'2023-09-05','child'=>'Chidinma Okafor','items'=>'School Fees (Term 1 2023) Full + ICT + Dev','amount'=>108000,'status'=>'success','channel'=>'Card'],
    ['ref'=>'NSM_2024_OCT_ERR','date'=>'2024-10-30','child'=>'Chidinma Okafor','items'=>'PTA Levy + Exam Fee','amount'=>17000,'status'=>'failed','channel'=>'Card'],
];

$totalSuccessful = collect($payments)->where('status','success')->sum('amount');
$totalFailed     = collect($payments)->where('status','failed')->count();
@endphp

@section('content')

{{-- Summary --}}
<div class="grid grid-cols-3 gap-3 md:gap-4 mb-5">
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ collect($payments)->where('status','success')->count() }}</p>
    <p class="text-xs text-gray-400 mt-1">Successful</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-2xl md:text-3xl font-bold text-green-700">₦{{ number_format($totalSuccessful) }}</p>
    <p class="text-xs text-gray-400 mt-1">Total Paid</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-2xl md:text-3xl font-bold {{ $totalFailed>0?'text-red-600':'text-gray-300' }}">{{ $totalFailed }}</p>
    <p class="text-xs text-gray-400 mt-1">Failed</p>
  </div>
</div>

{{-- Filter --}}
<div class="flex gap-2 flex-wrap mb-5" data-filter-group="history">
  @foreach(['all'=>'All','success'=>'Successful','failed'=>'Failed'] as $k=>$l)
    <button data-filter="{{ $k }}"
            class="px-4 py-2 rounded-full border text-sm font-semibold transition-all
                   {{ $k==='all'?'bg-forest-700 text-white border-forest-700':'bg-white text-gray-500 border-gray-200 hover:border-forest-600 hover:text-forest-700' }}">
      {{ $l }}
    </button>
  @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-200">
          <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Reference</th>
          <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
          <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Child</th>
          <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Items</th>
          <th class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Receipt</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($payments as $pay)
          <tr data-filter-target="history"
              data-category="{{ $pay['status'] }}"
              class="hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3.5">
              <p class="text-xs font-mono text-gray-700 font-semibold">{{ $pay['ref'] }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5 sm:hidden">{{ date('d M Y', strtotime($pay['date'])) }}</p>
            </td>
            <td class="px-4 py-3.5 text-sm text-gray-500 whitespace-nowrap hidden sm:table-cell">
              {{ date('d M Y', strtotime($pay['date'])) }}
            </td>
            <td class="px-4 py-3.5 text-sm text-gray-700 hidden md:table-cell whitespace-nowrap">{{ $pay['child'] }}</td>
            <td class="px-4 py-3.5 text-xs text-gray-400 hidden lg:table-cell max-w-[180px] truncate" title="{{ $pay['items'] }}">
              {{ $pay['items'] }}
            </td>
            <td class="px-4 py-3.5 text-sm font-bold text-right {{ $pay['status']==='failed'?'text-gray-400 line-through':'text-gray-900' }}">
              ₦{{ number_format($pay['amount']) }}
            </td>
            <td class="px-4 py-3.5 text-center">
              @if($pay['status'] === 'success')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                  Paid
                </span>
              @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Failed
                </span>
              @endif
            </td>
            <td class="px-4 py-3.5 text-center">
              @if($pay['status'] === 'success')
                <a href="{{ route('parent.fees.receipt', $pay['ref']) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-forest-50 border border-forest-200 text-forest-700 text-xs font-bold rounded-lg hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  PDF
                </a>
              @else
                <span class="text-xs text-gray-300">—</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
