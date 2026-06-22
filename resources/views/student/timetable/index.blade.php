@extends('student.layouts.app')

@section('title', 'My Timetable')
@section('page-title', 'My Timetable')
@section('page-sub', 'Class schedule for ' . ($currentTerm ?? 'this term'))

@php
$todayNum = (int) date('N'); // 1=Mon … 5=Fri; 6,7=weekend → default 1
$todayNum = ($todayNum >= 1 && $todayNum <= 5) ? $todayNum : 1;
$currentTerm    = 'First Term';

$weekDays = [
    1 => ['short'=>'Mon', 'full'=>'Monday',    'date'=>date('j', strtotime('monday this week'))],
    2 => ['short'=>'Tue', 'full'=>'Tuesday',   'date'=>date('j', strtotime('tuesday this week'))],
    3 => ['short'=>'Wed', 'full'=>'Wednesday', 'date'=>date('j', strtotime('wednesday this week'))],
    4 => ['short'=>'Thu', 'full'=>'Thursday',  'date'=>date('j', strtotime('thursday this week'))],
    5 => ['short'=>'Fri', 'full'=>'Friday',    'date'=>date('j', strtotime('friday this week'))],
];

$colors = [
    'Mathematics'      => '#16a34a',
    'English Language' => '#2563eb',
    'Basic Science'    => '#7c3aed',
    'Social Studies'   => '#d97706',
    'French'           => '#ea580c',
    'Computer Studies' => '#0891b2',
    'Civic Education'  => '#db2777',
    'Basic Technology' => '#64748b',
    'Agricultural Sci' => '#65a30d',
    'PHE'              => '#e11d48',
    'Music'            => '#0d9488',
    'Christian R.K.'   => '#9333ea',
];

// Schedule: period_label => [time, day1..day5 or break]
$schedule = [
    ['time'=>'8:00 – 8:45',   'break'=>false, 'periods'=>[
        1=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
        2=>['subject'=>'English Language',  'teacher'=>'Mrs. Eze C.',     'venue'=>'Room 2B'],
        3=>['subject'=>'Basic Science',     'teacher'=>'Mr. Umar B.',     'venue'=>'Lab 1'],
        4=>['subject'=>'Social Studies',    'teacher'=>'Mrs. Nwosu A.',   'venue'=>'Room 2B'],
        5=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
    ]],
    ['time'=>'8:45 – 9:30',   'break'=>false, 'periods'=>[
        1=>['subject'=>'English Language',  'teacher'=>'Mrs. Eze C.',     'venue'=>'Room 2B'],
        2=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
        3=>['subject'=>'Social Studies',    'teacher'=>'Mrs. Nwosu A.',   'venue'=>'Room 2B'],
        4=>['subject'=>'Computer Studies',  'teacher'=>'Mr. Balogun D.',  'venue'=>'ICT Lab'],
        5=>['subject'=>'English Language',  'teacher'=>'Mrs. Eze C.',     'venue'=>'Room 2B'],
    ]],
    ['time'=>'9:30 – 10:15',  'break'=>false, 'periods'=>[
        1=>['subject'=>'Basic Science',     'teacher'=>'Mr. Umar B.',     'venue'=>'Lab 1'],
        2=>['subject'=>'French',            'teacher'=>'Mr. Chukwu K.',   'venue'=>'Room 2B'],
        3=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
        4=>['subject'=>'Basic Science',     'teacher'=>'Mr. Umar B.',     'venue'=>'Lab 1'],
        5=>['subject'=>'Civic Education',   'teacher'=>'Miss Afolabi R.', 'venue'=>'Room 2B'],
    ]],
    ['time'=>'10:15 – 10:30', 'break'=>true,  'label'=>'Short Break'],
    ['time'=>'10:30 – 11:15', 'break'=>false, 'periods'=>[
        1=>['subject'=>'Social Studies',    'teacher'=>'Mrs. Nwosu A.',   'venue'=>'Room 2B'],
        2=>['subject'=>'Civic Education',   'teacher'=>'Miss Afolabi R.', 'venue'=>'Room 2B'],
        3=>['subject'=>'English Language',  'teacher'=>'Mrs. Eze C.',     'venue'=>'Room 2B'],
        4=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
        5=>['subject'=>'Basic Science',     'teacher'=>'Mr. Umar B.',     'venue'=>'Lab 1'],
    ]],
    ['time'=>'11:15 – 12:00', 'break'=>false, 'periods'=>[
        1=>['subject'=>'French',            'teacher'=>'Mr. Chukwu K.',   'venue'=>'Room 2B'],
        2=>['subject'=>'PHE',               'teacher'=>'Mr. Hassan Y.',   'venue'=>'Sports Field'],
        3=>['subject'=>'PHE',               'teacher'=>'Mr. Hassan Y.',   'venue'=>'Sports Field'],
        4=>['subject'=>'French',            'teacher'=>'Mr. Chukwu K.',   'venue'=>'Room 2B'],
        5=>['subject'=>'Computer Studies',  'teacher'=>'Mr. Balogun D.',  'venue'=>'ICT Lab'],
    ]],
    ['time'=>'12:00 – 1:00',  'break'=>true,  'label'=>'Lunch Break'],
    ['time'=>'1:00 – 1:45',   'break'=>false, 'periods'=>[
        1=>['subject'=>'Computer Studies',  'teacher'=>'Mr. Balogun D.',  'venue'=>'ICT Lab'],
        2=>['subject'=>'Basic Technology',  'teacher'=>'Mr. Emeka S.',    'venue'=>'Tech Lab'],
        3=>['subject'=>'Christian R.K.',    'teacher'=>'Mrs. Ojo F.',     'venue'=>'Room 2B'],
        4=>['subject'=>'Agricultural Sci',  'teacher'=>'Mr. Obi P.',      'venue'=>'Room 2B'],
        5=>['subject'=>'Social Studies',    'teacher'=>'Mrs. Nwosu A.',   'venue'=>'Room 2B'],
    ]],
    ['time'=>'1:45 – 2:30',   'break'=>false, 'periods'=>[
        1=>['subject'=>'Agricultural Sci',  'teacher'=>'Mr. Obi P.',      'venue'=>'Room 2B'],
        2=>['subject'=>'Basic Science',     'teacher'=>'Mr. Umar B.',     'venue'=>'Lab 1'],
        3=>['subject'=>'Music',             'teacher'=>'Mrs. Ikenna T.',  'venue'=>'Music Room'],
        4=>['subject'=>'PHE',               'teacher'=>'Mr. Hassan Y.',   'venue'=>'Sports Field'],
        5=>['subject'=>'Mathematics',       'teacher'=>'Mr. Adebayo T.',  'venue'=>'Room 2B'],
    ]],
];

// Current period detection
$nowMinutes = (int)date('H') * 60 + (int)date('i');
$periodRanges = [[480,525],[525,570],[570,615],[630,675],[675,720],[780,825],[825,870]];
$periodSlotIndexes = [0,1,2,4,5,7,8]; // maps to $schedule indices (skipping breaks)
$nowPeriodSlot = null;
foreach ($periodRanges as $pi => [$start,$end]) {
    if ($nowMinutes >= $start && $nowMinutes < $end) {
        $nowPeriodSlot = $periodSlotIndexes[$pi] ?? null;
        break;
    }
}
@endphp

@section('content')

{{-- Day selector --}}
<div class="grid grid-cols-5 gap-2 md:gap-3 mb-5">
  @foreach($weekDays as $num => $day)
    <button data-week-card="{{ $num }}"
            onclick="selectTimetableDay({{ $num }})"
            class="border rounded-xl p-2.5 md:p-3 text-center cursor-pointer transition-all hover:border-forest-600
                   {{ $num === $todayNum ? 'bg-forest-700 text-white border-forest-700' : 'bg-white border-gray-200 text-gray-600' }}">
      <div class="text-[10px] font-bold uppercase tracking-widest opacity-70">{{ $day['short'] }}</div>
      <div class="font-display font-bold text-lg md:text-xl mt-0.5">{{ $day['date'] }}</div>
      @if($num === $todayNum)
        <div class="text-[10px] mt-0.5 opacity-70">Today</div>
      @endif
    </button>
  @endforeach
</div>

{{-- Day label --}}
<div class="flex items-center justify-between mb-3">
  <h2 class="font-display text-lg font-semibold text-gray-900" id="day-label">
    {{ $weekDays[$todayNum]['full'] }}
    @if($todayNum === (int)date('N'))
      <span class="text-sm font-normal text-gray-400 font-body"> — Today</span>
    @endif
  </h2>
  <span class="text-xs text-gray-400">{{ $currentTerm }}</span>
</div>

{{-- Periods table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  @foreach($schedule as $slotIdx => $slot)

    @if($slot['break'])
      {{-- Break --}}
      <div class="flex items-center gap-4 px-5 py-2.5 bg-gray-50/80 border-b border-gray-100">
        <span class="text-xs text-gray-400 w-28 flex-shrink-0 font-mono">{{ $slot['time'] }}</span>
        <span class="text-xs text-gray-300 italic">{{ $slot['label'] }}</span>
      </div>

    @else
      {{-- Period rows — one per day, JS shows correct one --}}
      @foreach($weekDays as $dayNum => $day)
        @php $p = $slot['periods'][$dayNum] ?? null; @endphp
        <div data-day-row="{{ $dayNum }}"
             style="{{ $dayNum !== $todayNum ? 'display:none' : '' }}"
             class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 hover:bg-gray-50/50 transition-colors
                    {{ $slotIdx === $nowPeriodSlot && $dayNum === $todayNum ? 'bg-green-50/60' : '' }}">

          {{-- Time --}}
          <div class="w-28 flex-shrink-0">
            <span class="text-xs text-gray-400 font-mono">{{ $slot['time'] }}</span>
            @if($slotIdx === $nowPeriodSlot && $dayNum === $todayNum)
              <span class="block mt-0.5 text-[10px] font-bold text-white bg-green-600 px-1.5 py-0.5 rounded-full w-fit">Now</span>
            @endif
          </div>

          @if($p)
            {{-- Color bar + subject --}}
            <div class="w-1 h-9 rounded-full flex-shrink-0" style="background:{{ $colors[$p['subject']] ?? '#9ca3af' }}"></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-900">{{ $p['subject'] }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $p['teacher'] }}</p>
            </div>
            <span class="hidden md:inline-flex text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full flex-shrink-0">{{ $p['venue'] }}</span>
          @else
            <div class="flex-1">
              <span class="text-sm text-gray-300 italic">No class</span>
            </div>
          @endif

        </div>
      @endforeach
    @endif

  @endforeach
</div>

@endsection
