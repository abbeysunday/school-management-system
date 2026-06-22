@extends('parent.layouts.app')

@php
$parent   = $parent   ?? ['full_name'=>'Mr. Emmanuel Okafor','first_name'=>'Emmanuel','email'=>'e.okafor@gmail.com','phone'=>'08087654321','photo'=>null];
$children = $children ?? [
    ['id'=>1,'name'=>'Chidinma Okafor','first_name'=>'Chidinma','class'=>'JSS 2B','admission_no'=>'EXC/JSS2/2024/047','gender'=>'Female','photo'=>null,'attendance_pct'=>92,'fee_balance'=>35000,'last_avg'=>73.4,'position'=>'5th'],
    ['id'=>2,'name'=>'Emeka Okafor',   'first_name'=>'Emeka',   'class'=>'SS 1A', 'admission_no'=>'EXC/SS1/2023/019','gender'=>'Male',  'photo'=>null,'attendance_pct'=>88,'fee_balance'=>0,    'last_avg'=>81.2,'position'=>'3rd'],
];
$currentTerm         = $currentTerm         ?? 'First Term — 2024/2025';
$school              = $school              ?? (object)['name' => 'Excellence Academy'];
$unreadAnnouncements = $unreadAnnouncements ?? 2;
$totalOwed           = collect($children)->sum('fee_balance');

$announcements = [
    ['id'=>1,'title'=>'Emergency: Water Supply Disruption Tomorrow', 'date'=>'4 hours ago','priority'=>'emergency','read'=>false],
    ['id'=>2,'title'=>'PTA Meeting — Saturday 26th October 2024',   'date'=>'1 day ago',  'priority'=>'important','read'=>false],
    ['id'=>3,'title'=>'First Term Mid-Term Exam Timetable Released', 'date'=>'2 days ago', 'priority'=>'important','read'=>false],
    ['id'=>4,'title'=>'Inter-House Sports Day Rescheduled to 8 Nov', 'date'=>'1 week ago', 'priority'=>'normal',   'read'=>true],
];

$recentPayments = [
    ['ref'=>'NSM_2024_OCT_001','date'=>'14 Oct 2024','child'=>'Chidinma Okafor','amount'=>45000,'status'=>'success'],
    ['ref'=>'NSM_2024_SEP_001','date'=>'01 Sep 2024','child'=>'Emeka Okafor',   'amount'=>148000,'status'=>'success'],
];
@endphp

@section('title', 'Dashboard')
@section('page-title', 'Parent Dashboard')
@section('page-sub', $currentTerm)

@section('content')

{{-- ── Welcome banner ────────────────────────────────────────── --}}
<div class="relative bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 rounded-2xl p-5 md:p-7 text-white mb-5 overflow-hidden">
  <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/[.04] pointer-events-none"></div>
  <div class="absolute right-24 -bottom-10 w-28 h-28 rounded-full bg-white/[.03] pointer-events-none"></div>

  <p class="text-xs text-white/50 uppercase tracking-widest mb-1">Welcome back</p>
  <h2 class="font-display text-2xl md:text-3xl font-bold mb-2">Hello, {{ $parent['first_name'] }}! 👋</h2>
  <p class="text-sm text-white/70 mb-3">
    You have <strong class="text-white">{{ count($children) }}</strong> {{ Str::plural('child', count($children)) }} enrolled at {{ $school->name }}.
  </p>

  <div class="flex flex-wrap gap-2">
    <span class="flex items-center gap-1.5 text-xs text-white/75 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full">
      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      {{ $currentTerm }}
    </span>
    @if($totalOwed > 0)
      <span class="flex items-center gap-1.5 text-xs text-red-200 bg-red-500/20 border border-red-400/30 px-3 py-1.5 rounded-full font-semibold">
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        ₦{{ number_format($totalOwed) }} outstanding
      </span>
    @endif
    @if(($unreadAnnouncements ?? 0) > 0)
      <span class="flex items-center gap-1.5 text-xs text-yellow-200 bg-yellow-500/20 border border-yellow-400/30 px-3 py-1.5 rounded-full font-semibold">
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/></svg>
        {{ $unreadAnnouncements }} unread {{ Str::plural('notice', $unreadAnnouncements) }}
      </span>
    @endif
  </div>
</div>

{{-- ── Summary stats ──────────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-3 md:gap-4 mb-5">

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow text-center">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-forest-700 to-forest-500"></div>
    <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center mb-3 mx-auto">
      <svg class="w-5 h-5 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <p class="font-display text-3xl font-bold text-gray-900">{{ count($children) }}</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Children Enrolled</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow text-center">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r {{ $totalOwed > 0 ? 'from-red-500 to-red-400' : 'from-green-500 to-emerald-400' }}"></div>
    <div class="w-9 h-9 rounded-xl {{ $totalOwed > 0 ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center mb-3 mx-auto">
      <svg class="w-5 h-5 {{ $totalOwed > 0 ? 'text-red-600' : 'text-green-600' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    @if($totalOwed > 0)
      <p class="font-display text-xl font-bold text-red-600">₦{{ number_format($totalOwed) }}</p>
    @else
      <p class="font-display text-3xl font-bold text-green-600">✓</p>
    @endif
    <p class="text-xs text-gray-400 font-medium mt-0.5">Outstanding Fees</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow text-center">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-gold-600 to-gold-400"></div>
    <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center mb-3 mx-auto">
      <svg class="w-5 h-5 text-gold-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
    </div>
    <p class="font-display text-3xl font-bold {{ ($unreadAnnouncements ?? 0) > 0 ? 'text-gold-600' : 'text-gray-300' }}">{{ $unreadAnnouncements ?? 0 }}</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Unread Notices</p>
  </div>

</div>

{{-- ── Children overview cards ────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-5">
  <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
    <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
      <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      My Children
    </h3>
  </div>
  <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($children as $child)
      <div class="border border-gray-200 rounded-2xl p-4 hover:border-forest-300 hover:shadow-sm transition-all">

        {{-- Child header --}}
        <div class="flex items-center gap-3 mb-4">
          <div class="w-11 h-11 rounded-full border-2 border-gray-100 bg-forest-900 text-white flex items-center justify-center font-display font-bold text-base flex-shrink-0" style="display:flex;align-items:center;justify-content:center">
            {{ strtoupper(substr($child['first_name'],0,1)) }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900">{{ $child['name'] }}</p>
            <p class="text-xs text-gray-400">{{ $child['class'] }} · {{ $child['admission_no'] }}</p>
          </div>
          @if($child['fee_balance'] > 0)
            <span class="text-[10px] font-bold text-red-700 bg-red-100 px-2 py-1 rounded-full flex-shrink-0">Fee Due</span>
          @else
            <span class="text-[10px] font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full flex-shrink-0">Fees OK</span>
          @endif
        </div>

        {{-- Mini stats --}}
        <div class="grid grid-cols-3 gap-2 mb-4">
          <div class="text-center bg-gray-50 rounded-xl py-2.5 px-1">
            <p class="font-display font-bold text-sm text-gray-900">{{ $child['attendance_pct'] }}%</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Attendance</p>
          </div>
          <div class="text-center bg-gray-50 rounded-xl py-2.5 px-1">
            <p class="font-display font-bold text-sm text-gray-900">{{ $child['last_avg'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Avg Score</p>
          </div>
          <div class="text-center bg-gray-50 rounded-xl py-2.5 px-1">
            <p class="font-display font-bold text-sm text-purple-700">{{ $child['position'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Position</p>
          </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-2">
          <a href="{{ route('parent.children.results', $child['id']) }}"
             class="flex-1 flex items-center justify-center py-2 text-xs font-bold text-forest-700 border border-forest-200 rounded-lg hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
            Results
          </a>
          <a href="{{ route('parent.children.attendance', $child['id']) }}"
             class="flex-1 flex items-center justify-center py-2 text-xs font-bold text-forest-700 border border-forest-200 rounded-lg hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
            Attendance
          </a>
          @if($child['fee_balance'] > 0)
            <a href="{{ route('parent.fees.index') }}"
               class="flex-1 flex items-center justify-center py-2 text-xs font-bold text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-all">
              Pay Fees
            </a>
          @endif
        </div>

      </div>
    @endforeach
  </div>
</div>

{{-- ── Lower two-column grid ──────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 md:gap-5">

  {{-- Recent Announcements --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
        <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
        Recent Announcements
      </h3>
      <a href="{{ route('parent.announcements.index') }}" class="text-xs text-forest-700 font-semibold hover:text-gold-600 transition-colors">View all →</a>
    </div>
    <div class="divide-y divide-gray-50">
      @foreach($announcements as $ann)
        @php
          $dot   = match($ann['priority']) { 'emergency'=>'bg-red-500', 'important'=>'bg-yellow-500', default=>'bg-forest-600' };
          $title = match($ann['priority']) { 'emergency'=>'text-red-700', 'important'=>'text-yellow-700', default=>'text-gray-900' };
        @endphp
        <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors cursor-pointer">
          <div class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold {{ $title }} leading-snug">{{ $ann['title'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
              {{ $ann['date'] }}
              @if(!$ann['read'])
                <span class="ml-2 text-[10px] font-bold text-forest-700 bg-green-100 px-1.5 py-0.5 rounded-full">New</span>
              @endif
            </p>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Right column --}}
  <div class="flex flex-col gap-4 md:gap-5">

    {{-- Recent Payments --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
          <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          Recent Payments
        </h3>
        <a href="{{ route('parent.fees.history') }}" class="text-xs text-forest-700 font-semibold hover:text-gold-600 transition-colors">All →</a>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($recentPayments as $pay)
          <div class="flex items-center gap-3 px-5 py-3.5">
            <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-bold text-gray-900">₦{{ number_format($pay['amount']) }}</p>
              <p class="text-[11px] text-gray-400 truncate">{{ $pay['child'] }}</p>
              <p class="text-[10px] text-gray-300 mt-0.5">{{ $pay['date'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900">Quick Actions</h3>
      </div>
      <div class="p-4 grid grid-cols-2 gap-2.5">
        @foreach([
          ['icon'=>'💳','label'=>'Pay Fees',     'href'=>route('parent.fees.index')],
          ['icon'=>'📋','label'=>'Results',       'href'=>route('parent.children.results', $children[0]['id'])],
          ['icon'=>'✅','label'=>'Attendance',    'href'=>route('parent.children.attendance', $children[0]['id'])],
          ['icon'=>'📢','label'=>'Notices',       'href'=>route('parent.announcements.index')],
          ['icon'=>'🧾','label'=>'Pay History',   'href'=>route('parent.fees.history')],
          ['icon'=>'👤','label'=>'My Profile',    'href'=>route('parent.profile')],
        ] as $action)
          <a href="{{ $action['href'] }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:bg-green-50 hover:border-forest-300 transition-all text-center">
            <span class="text-xl">{{ $action['icon'] }}</span>
            <span class="text-xs font-semibold text-gray-600">{{ $action['label'] }}</span>
          </a>
        @endforeach
      </div>
    </div>

  </div>

</div>

@endsection
