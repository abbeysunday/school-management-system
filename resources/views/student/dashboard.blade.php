@php

$currentTerm    = 'First Term';
@endphp
@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', $currentTerm)

@php
$attendancePct = 92;
$avgScore      = 73.4;
$totalSubjects = 12;
$classPosition = '5th';
$studentClass = 'SS3A';

$todayPeriods = [
    ['time'=>'8:00–8:45',   'subject'=>'Mathematics',      'teacher'=>'Mr. Adebayo',   'color'=>'#16a34a', 'now'=>true],
    ['time'=>'8:45–9:30',   'subject'=>'English Language',  'teacher'=>'Mrs. Eze',      'color'=>'#2563eb', 'now'=>false],
    ['time'=>'9:30–10:15',  'subject'=>'Basic Science',     'teacher'=>'Mr. Umar',      'color'=>'#7c3aed', 'now'=>false],
    ['time'=>'10:15–10:30', 'subject'=>'Short Break',       'teacher'=>'',              'color'=>'#9ca3af', 'now'=>false, 'break'=>true],
    ['time'=>'10:30–11:15', 'subject'=>'Social Studies',    'teacher'=>'Mrs. Nwosu',    'color'=>'#d97706', 'now'=>false],
    ['time'=>'11:15–12:00', 'subject'=>'French',            'teacher'=>'Mr. Chukwu',    'color'=>'#ea580c', 'now'=>false],
    ['time'=>'12:00–1:00',  'subject'=>'Lunch Break',       'teacher'=>'',              'color'=>'#9ca3af', 'now'=>false, 'break'=>true],
    ['time'=>'1:00–1:45',   'subject'=>'Computer Studies',  'teacher'=>'Mr. Balogun',   'color'=>'#0891b2', 'now'=>false],
];

$announcements = [
    ['title'=>'PTA Meeting — Saturday 26th Oct',           'date'=>'2 hrs ago',  'priority'=>'important'],
    ['title'=>'Mid-Term Exam Timetable Released',           'date'=>'1 day ago',  'priority'=>'important'],
    ['title'=>'Inter-House Sports Day Postponed',           'date'=>'3 days ago', 'priority'=>'normal'],
    ['title'=>'Emergency: Water Supply Disruption Fri',     'date'=>'4 days ago', 'priority'=>'emergency'],
];

$upcomingExams = [
    ['subject'=>'Mathematics', 'title'=>'Mid-Term CBT', 'date'=>'Mon, 4 Nov',  'time'=>'10:00 AM', 'duration'=>45, 'id'=>1],
    ['subject'=>'English',     'title'=>'Practice Test', 'date'=>'Wed, 6 Nov',  'time'=>'9:00 AM',  'duration'=>30, 'id'=>2],
    ['subject'=>'Sci',         'title'=>'Science Quiz',  'date'=>'Fri, 8 Nov',  'time'=>'11:00 AM', 'duration'=>30, 'id'=>3],
];
@endphp

@section('content')

{{-- ── Greeting banner ──────────────────────────────────────── --}}
<div class="relative bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 rounded-2xl p-5 md:p-7 text-white mb-5 overflow-hidden">
  {{-- Decorative circles --}}
  <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/[.04] pointer-events-none"></div>
  <div class="absolute right-24 -bottom-10 w-28 h-28 rounded-full bg-white/[.03] pointer-events-none"></div>

  <p class="text-xs text-white/50 uppercase tracking-widest mb-1" id="greeting-time">Good Morning</p>
  {{-- <h2 class="font-display text-2xl md:text-3xl font-bold mb-1.5">Hello, {{ $student['first_name'] }}! 👋</h2> --}}
  <h2 class="font-display text-2xl md:text-3xl font-bold mb-1.5">Hello, Abiodun! 👋</h2>


  <div class="flex flex-wrap gap-2">
    <span class="flex items-center gap-1.5 text-xs text-white/75 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full">
      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      {{ $studentClass }}
    </span>
    <span class="flex items-center gap-1.5 text-xs text-white/75 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full">
      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      {{ $currentTerm }}
    </span>
    <span class="flex items-center gap-1.5 text-xs text-white/75 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full">
      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/></svg>
      {{ $attendancePct }}% Attendance
    </span>
  </div>
</div>

{{-- ── Stats row ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-5">

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-forest-700 to-forest-500"></div>
    <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center mb-3">
      <svg class="w-4.5 h-4.5 text-forest-700" style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $attendancePct }}%</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Attendance Rate</p>
    <p class="text-xs text-green-600 font-semibold mt-2">↑ Above average</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-gold-600 to-gold-400"></div>
    <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center mb-3">
      <svg class="w-4.5 h-4.5 text-gold-600" style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    </div>
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $avgScore }}</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Average Score</p>
    <p class="text-xs text-gold-600 font-semibold mt-2">Credit level</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 to-blue-400"></div>
    <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
      <svg class="w-4.5 h-4.5 text-blue-700" style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    </div>
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $totalSubjects }}</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Subjects</p>
    <p class="text-xs text-blue-600 font-semibold mt-2">This term</p>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow">
    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-purple-600 to-purple-400"></div>
    <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center mb-3">
      <svg class="w-4.5 h-4.5 text-purple-700" style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
    </div>
    <p class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $classPosition }}</p>
    <p class="text-xs text-gray-400 font-medium mt-0.5">Class Position</p>
    <p class="text-xs text-purple-600 font-semibold mt-2">In class</p>
  </div>

</div>

{{-- ── Main grid ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4 md:gap-5">

  {{-- Left column --}}
  <div class="flex flex-col gap-4 md:gap-5">

    {{-- Today's timetable --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
          <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Today's Schedule
        </h3>
        <a href="{{ route('student.timetable.index') }}" class="text-xs text-forest-700 font-semibold hover:text-gold-600 transition-colors">Full timetable →</a>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($todayPeriods as $period)
          @if($period['break'] ?? false)
            <div class="flex items-center gap-3 px-5 py-2.5 bg-gray-50/60">
              <span class="text-xs text-gray-400 w-20 flex-shrink-0">{{ $period['time'] }}</span>
              <span class="text-xs text-gray-300 italic">{{ $period['subject'] }}</span>
            </div>
          @else
            <div class="flex items-center gap-3 px-5 py-3 {{ $period['now'] ? 'bg-green-50' : 'hover:bg-gray-50' }} transition-colors">
              <span class="text-xs text-gray-400 w-20 flex-shrink-0">{{ $period['time'] }}</span>
              <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $period['color'] }}"></div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">
                  {{ $period['subject'] }}
                  @if($period['now'])
                    <span class="inline-block text-[10px] font-bold text-white bg-green-600 px-1.5 py-0.5 rounded-full ml-1">Now</span>
                  @endif
                </p>
                <p class="text-xs text-gray-400">{{ $period['teacher'] }}</p>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>

    {{-- Recent announcements --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
          <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
          Announcements
        </h3>
        <a href="{{ route('student.announcements.index') }}" class="text-xs text-forest-700 font-semibold hover:text-gold-600 transition-colors">View all →</a>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($announcements as $ann)
          @php
            $dotColor  = match($ann['priority']) { 'emergency'=>'bg-red-500', 'important'=>'bg-yellow-500', default=>'bg-forest-600' };
            $titleColor = match($ann['priority']) { 'emergency'=>'text-red-700', 'important'=>'text-yellow-700', default=>'text-gray-900' };
          @endphp
          <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors cursor-pointer">
            <div class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $dotColor }}"></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold {{ $titleColor }} leading-snug">{{ $ann['title'] }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $ann['date'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>{{-- /left column --}}

  {{-- Right column --}}
  <div class="flex flex-col gap-4 md:gap-5">

    {{-- Upcoming exams --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900 flex items-center gap-2">
          <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Upcoming Exams
        </h3>
        <a href="{{ route('student.exams.index') }}" class="text-xs text-forest-700 font-semibold hover:text-gold-600 transition-colors">All exams →</a>
      </div>
      <div class="p-4 flex flex-col gap-3">
        @foreach($upcomingExams as $exam)
          <div class="border border-gray-200 rounded-xl p-3.5 hover:border-forest-300 hover:bg-green-50/30 transition-all">
            <div class="flex items-start justify-between gap-2 mb-2">
              <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $exam['title'] }}</p>
              <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full whitespace-nowrap flex-shrink-0">{{ $exam['subject'] }}</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-400">
              <span>📅 {{ $exam['date'] }}</span>
              <span>🕐 {{ $exam['time'] }}</span>
              <span>⏱ {{ $exam['duration'] }}min</span>
            </div>
            <a href="{{ route('student.exams.lobby', $exam['id']) }}" class="mt-3 flex items-center justify-center gap-1.5 w-full py-2 text-xs font-bold text-forest-700 border border-forest-300 rounded-lg hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
              View Lobby
            </a>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-display font-semibold text-gray-900">Quick Actions</h3>
      </div>
      <div class="p-4 grid grid-cols-2 gap-2.5">
        @foreach([
          ['icon'=>'📋','label'=>'My Results',    'href'=>route('student.results.index')],
          ['icon'=>'📅','label'=>'Timetable',     'href'=>route('student.timetable.index')],
          ['icon'=>'✅','label'=>'Attendance',    'href'=>route('student.attendance.index')],
          ['icon'=>'👤','label'=>'Profile',       'href'=>route('student.profile.index')],
        ] as $action)
          <a href="{{ $action['href'] }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:bg-green-50 hover:border-forest-300 transition-all text-center">
            <span class="text-xl">{{ $action['icon'] }}</span>
            <span class="text-xs font-semibold text-gray-600">{{ $action['label'] }}</span>
          </a>
        @endforeach
      </div>
    </div>

  </div>{{-- /right column --}}

</div>

@endsection
