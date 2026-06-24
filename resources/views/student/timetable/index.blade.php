@extends('student.layouts.app')

@section('title', 'My Timetable')
@section('page-title', 'My Timetable')
@section('page-sub', $classArmName . ' — ' . $currentTerm . ' (' . $session->name . ')')

@section('content')

{{-- Class Info Header --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <h2 class="font-display text-xl font-bold text-gray-900">{{ $classArmName }}</h2>
        <p class="text-sm text-gray-500 mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1">
            <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $session->name }}
            </span>
            <span class="hidden md:inline text-gray-300">|</span>
            <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                {{ $currentTerm }}
            </span>
            <span class="hidden md:inline text-gray-300">|</span>
            <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                {{ $subjectsCount }} Subject{{ $subjectsCount !== 1 ? 's' : '' }}
            </span>
        </p>
    </div>
    <div class="flex items-center gap-2">
        @if($isWeekday && $nowPeriodId)
            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 px-3 py-1.5 rounded-full border border-green-200">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Class in session
            </span>
        @endif
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-full border border-gray-200 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </button>
    </div>
</div>

{{-- Day selector tabs --}}
<div class="grid grid-cols-5 gap-2 md:gap-3 mb-5">
  @foreach($weekDays as $num => $day)
    <button data-week-card="{{ $num }}"
            onclick="selectTimetableDay({{ $num }})"
            class="border rounded-xl p-2.5 md:p-3 text-center cursor-pointer transition-all duration-200
                   {{ $num === $activeDayNum ? 'bg-forest-700 text-white border-forest-700 shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-forest-400' }}">
      <div class="text-[10px] font-bold uppercase tracking-widest opacity-70">{{ $day['short'] }}</div>
      <div class="font-display font-bold text-lg md:text-xl mt-0.5">{{ $day['date'] }}</div>
      @if($num === $todayNum && $isWeekday)
        <div class="text-[10px] mt-0.5 opacity-70 font-medium">Today</div>
      @endif
    </button>
  @endforeach
</div>

{{-- Day label --}}
<div class="flex items-center justify-between mb-3">
  <h2 class="font-display text-lg font-semibold text-gray-900" id="day-label">
    {{ $weekDays[$activeDayNum]['full'] }}
    @if($isWeekday && $activeDayNum === $todayNum)
      <span class="text-sm font-normal text-gray-400 font-body"> — Today</span>
    @endif
  </h2>
  <span class="text-xs text-gray-400">{{ $currentTerm }}</span>
</div>

{{-- Empty state: no periods configured at all --}}
@if($periods->isEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <h3 class="text-gray-900 font-semibold text-lg">No Timetable Available</h3>
    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Your class timetable has not been published yet. Please check back later or contact your class teacher.</p>
</div>

{{-- Empty state: periods exist but nothing scheduled for this class --}}
@elseif($scheduledPeriods === 0)
<div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    <h3 class="text-gray-900 font-semibold text-lg">Timetable Not Set</h3>
    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Periods are configured but no subjects have been assigned to {{ $classArmName }} yet.</p>
</div>

@else
{{-- Timetable Periods Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
  @foreach($periods as $period)

    @if(!$period->isTeaching())
      {{-- Non-teaching period: Break / Assembly / Games / Closing --}}
      <div class="flex items-center gap-4 px-5 py-2.5 bg-gray-50/80 border-b border-gray-100">
        <span class="text-xs text-gray-400 w-28 flex-shrink-0 font-mono">{{ \Carbon\Carbon::parse($period->start_time)->format('g:i') }} – {{ \Carbon\Carbon::parse($period->end_time)->format('g:i A') }}</span>
        <span class="inline-flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full
                {{ $period->period_type === 'Break' ? 'bg-amber-400' : ($period->period_type === 'Assembly' ? 'bg-sky-400' : ($period->period_type === 'Games' ? 'bg-emerald-400' : 'bg-gray-400')) }}"></span>
            <span class="text-xs text-gray-500 font-medium">{{ $period->period_name }}</span>
        </span>
        <span class="text-xs text-gray-300 italic">{{ $period->period_type }}</span>
      </div>

    @else
      {{-- Teaching period — one row per day, JS toggles visibility --}}
      @foreach($weekDays as $dayNum => $day)
        @php
          $entry = $timetableGrid[$day['full']][$period->id] ?? null;
          $isNow = ($period->id === $nowPeriodId && $dayNum === $todayNum && $isWeekday);
          $color = $entry && $entry->subject ? ($subjectColors[$entry->subject->name] ?? '#9ca3af') : null;
        @endphp
        <div data-day-row="{{ $dayNum }}"
             style="{{ $dayNum !== $activeDayNum ? 'display:none' : '' }}"
             class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 hover:bg-gray-50/50 transition-colors
                    {{ $isNow ? 'bg-green-50/60' : '' }}">

          {{-- Time column --}}
          <div class="w-28 flex-shrink-0">
            <span class="text-xs text-gray-400 font-mono">
                {{ \Carbon\Carbon::parse($period->start_time)->format('g:i') }} – {{ \Carbon\Carbon::parse($period->end_time)->format('g:i A') }}
            </span>
            @if($isNow)
              <span class="block mt-0.5 text-[10px] font-bold text-white bg-green-600 px-1.5 py-0.5 rounded-full w-fit">Now</span>
            @endif
          </div>

          @if($entry && $entry->subject)
            {{-- Color bar + subject info --}}
            <div class="w-1 h-9 rounded-full flex-shrink-0" style="background:{{ $color }}"></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-900 truncate">{{ $entry->subject->name }}</p>
              <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="truncate">{{ $entry->teacher?->user?->full_name ?? 'TBA' }}</span>
              </p>
            </div>
            <span class="hidden md:inline-flex text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full flex-shrink-0 items-center gap-1">
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $entry->room ?? 'TBA' }}
            </span>
          @else
            {{-- Free period / not scheduled --}}
            <div class="flex-1 flex items-center gap-2">
                <span class="w-1 h-6 rounded-full bg-gray-200 flex-shrink-0"></span>
                <span class="text-sm text-gray-300 italic">Free period</span>
            </div>
          @endif

        </div>
      @endforeach
    @endif

  @endforeach
</div>

{{-- Subject Color Legend --}}
@if(count($subjectColors) > 0)
<div class="mt-5">
    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Subjects</h4>
    <div class="flex flex-wrap gap-2">
        @foreach($subjectColors as $subjectName => $color)
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 bg-white border border-gray-200 px-2.5 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                <span class="truncate">{{ $subjectName }}</span>
            </span>
        @endforeach
    </div>
</div>
@endif

{{-- Stats Summary --}}
<div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900">{{ $totalPeriods }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Total Periods</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $teachingPeriods }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Teaching Periods</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $scheduledPeriods }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Scheduled</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-purple-600">{{ $subjectsCount }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Subjects</div>
    </div>
</div>

@endif

{{-- Weekend notice --}}
@if(!$isWeekday)
<div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
        <p class="text-sm font-medium text-amber-800">It's the weekend!</p>
        <p class="text-xs text-amber-600 mt-0.5">Showing Monday's schedule by default. Use the day tabs above to view other days.</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function selectTimetableDay(dayNum) {
    // Update tab styles
    document.querySelectorAll('[data-week-card]').forEach(btn => {
        const num = parseInt(btn.dataset.weekCard);
        if (num === dayNum) {
            btn.classList.remove('bg-white', 'border-gray-200', 'text-gray-600', 'hover:bg-gray-50', 'hover:border-forest-400');
            btn.classList.add('bg-forest-700', 'border-forest-700', 'text-white', 'shadow-sm');
        } else {
            btn.classList.remove('bg-forest-700', 'border-forest-700', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'border-gray-200', 'text-gray-600', 'hover:bg-gray-50', 'hover:border-forest-400');
        }
    });

    // Show/hide day rows
    document.querySelectorAll('[data-day-row]').forEach(row => {
        row.style.display = (parseInt(row.dataset.dayRow) === dayNum) ? '' : 'none';
    });

    // Update day label
    const labels = {1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday'};
    const todayN = {{ (int) now()->format('N') }};
    const isWeekday = {{ $isWeekday ? 'true' : 'false' }};
    let suffix = '';
    if (isWeekday && dayNum === todayN) {
        suffix = ' <span class="text-sm font-normal text-gray-400 font-body"> — Today</span>';
    }
    document.getElementById('day-label').innerHTML = labels[dayNum] + suffix;
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .sidebar, header, footer, [data-week-card], .no-print,
    .animate-ping, button { display: none !important; }
    .content { margin: 0 !important; padding: 0 !important; }
    [data-day-row] { display: flex !important; }
    .bg-white { box-shadow: none !important; border: 1px solid #ddd !important; }
    .bg-forest-700 { background: #1a5f2a !important; color: #fff !important; }
    body { background: #fff !important; }
}
</style>
@endpush
