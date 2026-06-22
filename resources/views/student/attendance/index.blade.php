@extends('student.layouts.app')

@section('title', 'Attendance')
@section('page-title', 'My Attendance')
@section('page-sub', 'Attendance record for ' . ($currentTerm ?? 'this term'))

@php
$totalDays    = 68;
$daysPresent  = 63;
$daysAbsent   = 3;
$daysLate     = 2;
$pct          = round(($daysPresent / $totalDays) * 100, 1);

// October 2024: starts on Tuesday (offset=1 zero-indexed from Mon)
$calOffset = 1;
$calDays   = 31;
// P=Present A=Absent L=Late H=Holiday W=Weekend
$calStatus = [
    1=>'P',2=>'P',3=>'P',4=>'P',5=>'W',6=>'W',
    7=>'P',8=>'P',9=>'P',10=>'P',11=>'P',12=>'W',13=>'W',
    14=>'P',15=>'A',16=>'P',17=>'P',18=>'P',19=>'W',20=>'W',
    21=>'L',22=>'P',23=>'P',24=>'H',25=>'P',26=>'W',27=>'W',
    28=>'P',29=>'P',30=>'P',31=>'P',
];

$monthly = [
    ['month'=>'September 2024','total'=>20,'present'=>19,'absent'=>0,'late'=>1,'pct'=>95.0],
    ['month'=>'October 2024',  'total'=>23,'present'=>21,'absent'=>1,'late'=>1,'pct'=>91.3],
    ['month'=>'November 2024', 'total'=>20,'present'=>19,'absent'=>1,'late'=>0,'pct'=>95.0],
    ['month'=>'December 2024', 'total'=>5, 'present'=>4, 'absent'=>1,'late'=>0,'pct'=>80.0],
];
@endphp

@section('content')

{{-- Stats row --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-5">

  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-forest-700"></div>
    <p class="font-display text-3xl font-bold text-gray-900">{{ $totalDays }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">School Days</p>
    <p class="text-xs text-gray-400 mt-1">This term</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-green-500"></div>
    <p class="font-display text-3xl font-bold text-gray-900">{{ $daysPresent }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Days Present</p>
    <p class="text-xs text-green-600 font-semibold mt-1">{{ $pct }}% rate</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-red-500"></div>
    <p class="font-display text-3xl font-bold text-gray-900">{{ $daysAbsent }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Days Absent</p>
    <p class="text-xs text-red-500 font-semibold mt-1">{{ round($daysAbsent/$totalDays*100,1) }}% absent</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center relative overflow-hidden">
    <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-yellow-500"></div>
    <p class="font-display text-3xl font-bold text-gray-900">{{ $daysLate }}</p>
    <p class="text-xs text-gray-400 font-medium mt-1">Late Arrivals</p>
    <p class="text-xs text-yellow-600 font-semibold mt-1">{{ round($daysLate/$totalDays*100,1) }}% late</p>
  </div>

</div>

{{-- Overall progress bar --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
  <div class="flex items-center justify-between mb-2.5">
    <p class="text-sm font-semibold text-gray-700">Overall Attendance Rate</p>
    <p class="text-sm font-bold {{ $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-yellow-600' : 'text-red-600') }}">{{ $pct }}%</p>
  </div>
  <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
    <div class="h-full rounded-full transition-all duration-700
                {{ $pct >= 90 ? 'bg-gradient-to-r from-green-500 to-emerald-400' : ($pct >= 75 ? 'bg-gradient-to-r from-yellow-500 to-amber-400' : 'bg-gradient-to-r from-red-500 to-red-400') }}"
         style="width:{{ $pct }}%"></div>
  </div>
  <p class="text-xs mt-2 font-semibold {{ $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
    @if($pct >= 90) ✓ Excellent — You qualify for all exams and assessments.
    @elseif($pct >= 75) ⚠ Acceptable — Minimum 75% required. Try to improve.
    @else ✕ Below minimum — You may be barred from examinations.
    @endif
  </p>
</div>

{{-- Calendar card --}}
<div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
    <h3 class="font-display font-semibold text-gray-900">October 2024 Calendar</h3>
    {{-- Legend --}}
    <div class="flex flex-wrap gap-3">
      @foreach(['Present'=>'bg-green-100 border border-green-300','Absent'=>'bg-red-100 border border-red-300','Late'=>'bg-yellow-100 border border-yellow-300','Holiday'=>'bg-gray-100 border border-gray-300'] as $label => $cls)
        <div class="flex items-center gap-1.5">
          <span class="w-3.5 h-3.5 rounded {{ $cls }}"></span>
          <span class="text-xs text-gray-500">{{ $label }}</span>
        </div>
      @endforeach
    </div>
  </div>
  <div class="p-4 md:p-5">
    {{-- Day headers --}}
    <div class="grid grid-cols-7 gap-1.5 mb-2">
      @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dh)
        <div class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider py-1">{{ $dh }}</div>
      @endforeach
    </div>
    {{-- Day cells --}}
    <div class="grid grid-cols-7 gap-1.5">
      @for($i = 0; $i < $calOffset; $i++)
        <div class="att-day empty"></div>
      @endfor
      @for($d = 1; $d <= $calDays; $d++)
        @php
          $s = $calStatus[$d] ?? 'W';
          $cls = match($s) { 'P'=>'present','A'=>'absent','L'=>'late','H'=>'holiday','W'=>'weekend', default=>'weekend' };
          $title = match($s) { 'P'=>'Present','A'=>'Absent','L'=>'Late','H'=>'Holiday','W'=>'Weekend', default=>'' };
        @endphp
        <div class="att-day {{ $cls }}" title="{{ $title }}">{{ $d }}</div>
      @endfor
    </div>
  </div>
</div>

{{-- Monthly breakdown --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-4 border-b border-gray-100">
    <h3 class="font-display font-semibold text-gray-900">Monthly Breakdown</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-200">
          <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Month</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Days</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Present</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Absent</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Late</th>
          <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Rate</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($monthly as $m)
          @php $pctColor = $m['pct']>=90?'text-green-600':($m['pct']>=75?'text-yellow-600':'text-red-600'); @endphp
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-semibold text-gray-900">{{ $m['month'] }}</td>
            <td class="px-4 py-3.5 text-sm text-gray-500 text-center">{{ $m['total'] }}</td>
            <td class="px-4 py-3.5 text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">{{ $m['present'] }}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
              @if($m['absent']>0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $m['absent'] }}</span>
              @else
                <span class="text-gray-300">—</span>
              @endif
            </td>
            <td class="px-4 py-3.5 text-center">
              @if($m['late']>0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">{{ $m['late'] }}</span>
              @else
                <span class="text-gray-300">—</span>
              @endif
            </td>
            <td class="px-4 py-3.5">
              <div class="flex items-center justify-center gap-2">
                <div class="score-bar flex-1 max-w-[60px]">
                  <div class="score-bar-fill" data-score="{{ $m['pct'] }}"></div>
                </div>
                <span class="text-xs font-bold {{ $pctColor }} w-10 text-right">{{ $m['pct'] }}%</span>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
