@extends('parent.layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('page-sub', 'Calendar view for your children')

@php
$parent   = $parent   ?? ['full_name'=>'Mr. Emmanuel Okafor','first_name'=>'Emmanuel','email'=>'e.okafor@gmail.com','phone'=>'08087654321','photo'=>null];
$children = $children ?? [
    ['id'=>1,'name'=>'Chidinma Okafor','first_name'=>'Chidinma','class'=>'JSS 2B','admission_no'=>'EXC/JSS2/2024/047','gender'=>'Female','photo'=>null,'attendance_pct'=>92,'fee_balance'=>35000,'last_avg'=>73.4,'position'=>'5th'],
    ['id'=>2,'name'=>'Emeka Okafor',   'first_name'=>'Emeka',   'class'=>'SS 1A', 'admission_no'=>'EXC/SS1/2023/019','gender'=>'Male',  'photo'=>null,'attendance_pct'=>88,'fee_balance'=>0,    'last_avg'=>81.2,'position'=>'3rd'],
];

$childId = $childId ?? 1;

$attendanceData = [
    1 => [
        'child'        => ['name'=>'Chidinma Okafor','class'=>'JSS 2B'],
        'total'        => 68, 'present'=>63, 'absent'=>3, 'late'=>2,
        'oct_offset'   => 1, // October 2024 starts on Tuesday (Mon=0)
        'oct_days'     => 31,
        'cal' => [
            1=>'P',2=>'P',3=>'P',4=>'P',5=>'W',6=>'W',
            7=>'P',8=>'P',9=>'P',10=>'P',11=>'P',12=>'W',13=>'W',
            14=>'P',15=>'A',16=>'P',17=>'P',18=>'P',19=>'W',20=>'W',
            21=>'L',22=>'P',23=>'P',24=>'H',25=>'P',26=>'W',27=>'W',
            28=>'P',29=>'P',30=>'P',31=>'P',
        ],
    ],
    2 => [
        'child'        => ['name'=>'Emeka Okafor','class'=>'SS 1A'],
        'total'        => 68, 'present'=>60, 'absent'=>5, 'late'=>3,
        'oct_offset'   => 1,
        'oct_days'     => 31,
        'cal' => [
            1=>'P',2=>'P',3=>'P',4=>'A',5=>'W',6=>'W',
            7=>'P',8=>'P',9=>'P',10=>'P',11=>'P',12=>'W',13=>'W',
            14=>'P',15=>'P',16=>'L',17=>'P',18=>'A',19=>'W',20=>'W',
            21=>'P',22=>'P',23=>'A',24=>'H',25=>'P',26=>'W',27=>'W',
            28=>'P',29=>'A',30=>'P',31=>'L',
        ],
    ],
];

$data = $attendanceData[$childId] ?? $attendanceData[1];
$pct  = round($data['present'] / $data['total'] * 100, 1);
@endphp

@section('content')

{{-- Child switcher --}}
@if(count($children) > 1)
  <div class="flex gap-2 flex-wrap mb-5 overflow-x-auto pb-1">
    @foreach($children as $child)
      <a href="{{ route('parent.children.attendance', $child['id']) }}"
         class="child-tab {{ $child['id'] == $childId ? 'active' : '' }}">
        <div class="child-tab-avatar">{{ strtoupper(substr($child['first_name'],0,1)) }}</div>
        <span class="child-tab-name">{{ $child['first_name'] }}</span>
      </a>
    @endforeach
  </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-5">
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-3xl font-bold text-gray-900">{{ $data['total'] }}</p>
    <p class="text-xs text-gray-400 mt-1">School Days</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-3xl font-bold text-green-700">{{ $data['present'] }}</p>
    <p class="text-xs text-gray-400 mt-1">Present</p>
    <p class="text-xs text-green-600 font-semibold mt-1">{{ $pct }}%</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-3xl font-bold text-red-600">{{ $data['absent'] }}</p>
    <p class="text-xs text-gray-400 mt-1">Absent</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
    <p class="font-display text-3xl font-bold text-yellow-600">{{ $data['late'] }}</p>
    <p class="text-xs text-gray-400 mt-1">Late Arrivals</p>
  </div>
</div>

{{-- Progress bar --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
  <div class="flex items-center justify-between mb-2">
    <p class="text-sm font-semibold text-gray-700">{{ $data['child']['name'] }} — Attendance Rate</p>
    <p class="text-sm font-bold {{ $pct>=90?'text-green-600':($pct>=75?'text-yellow-600':'text-red-600') }}">{{ $pct }}%</p>
  </div>
  <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
    <div class="h-full rounded-full {{ $pct>=90?'bg-gradient-to-r from-green-500 to-emerald-400':($pct>=75?'bg-gradient-to-r from-yellow-500 to-amber-400':'bg-gradient-to-r from-red-500 to-red-400') }}"
         style="width:{{ $pct }}%"></div>
  </div>
  <p class="text-xs font-semibold mt-2 {{ $pct>=90?'text-green-600':($pct>=75?'text-yellow-600':'text-red-600') }}">
    @if($pct>=90)     ✓ Excellent attendance — keep it up!
    @elseif($pct>=75) ⚠ Acceptable — minimum 75% required
    @else             ✕ Below minimum — please contact the school
    @endif
  </p>
</div>

{{-- Calendar --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-5">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
    <h3 class="font-display font-semibold text-gray-900">October 2024</h3>
    <div class="flex flex-wrap gap-3">
      @foreach(['Present'=>'bg-green-100 border border-green-300 text-green-700','Absent'=>'bg-red-100 border border-red-300 text-red-700','Late'=>'bg-yellow-100 border border-yellow-300 text-yellow-700','Holiday'=>'bg-gray-100 border border-gray-300 text-gray-500'] as $label=>$cls)
        <div class="flex items-center gap-1.5">
          <span class="w-4 h-4 rounded {{ $cls }} text-[8px] flex items-center justify-center font-bold">
            {{ strtoupper(substr($label,0,1)) }}
          </span>
          <span class="text-xs text-gray-500">{{ $label }}</span>
        </div>
      @endforeach
    </div>
  </div>
  <div class="p-4 md:p-5">
    <div class="grid grid-cols-7 gap-1 mb-2">
      @foreach(['M','T','W','T','F','S','S'] as $d)
        <div class="text-center text-[10px] font-bold text-gray-400 uppercase py-1">{{ $d }}</div>
      @endforeach
    </div>
    <div class="grid grid-cols-7 gap-1">
      @for($i=0;$i<$data['oct_offset'];$i++)
        <div class="att-day empty"></div>
      @endfor
      @for($d=1;$d<=$data['oct_days'];$d++)
        @php
          $s = $data['cal'][$d] ?? 'W';
          $cls = match($s) { 'P'=>'present','A'=>'absent','L'=>'late','H'=>'holiday','W'=>'weekend',default=>'weekend' };
          $title = match($s) { 'P'=>'Present','A'=>'Absent','L'=>'Late','H'=>'Holiday','W'=>'Weekend',default=>'' };
        @endphp
        <div class="att-day {{ $cls }} text-xs" title="{{ $title }}">
          {{ $d }}
          @if(in_array($s,['P','A','L']))
            <div class="att-dot {{ $cls }}"></div>
          @endif
        </div>
      @endfor
    </div>
  </div>
</div>

{{-- Note --}}
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
  <p class="font-semibold mb-1">About Attendance Policy</p>
  <p class="text-xs text-blue-700">Students must maintain a minimum of 75% attendance to sit end-of-term examinations. If you believe an absence has been recorded in error, please contact the class teacher or school office.</p>
</div>

@endsection
