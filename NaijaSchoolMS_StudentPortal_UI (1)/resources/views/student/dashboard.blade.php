@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Welcome back! Here\'s your overview for today.')

@php
// ── Dummy data (replace with real DB queries when backend is wired) ──
$student = [
  'first_name'       => 'Chidinma',
  'last_name'        => 'Okafor',
  'admission_number' => 'EXC/JSS2/2024/047',
  'class'            => 'JSS 2B',
  'photo'            => null,
];

$currentTerm    = 'First Term — 2024/2025';
$attendancePct  = 92;
$totalSubjects  = 12;
$avgScore       = 73.4;
$classPosition  = '5th';

$todayTimetable = [
  ['time' => '8:00 – 8:45',  'subject' => 'Mathematics',        'teacher' => 'Mr. Adebayo',     'color' => '#16a34a', 'now' => true],
  ['time' => '8:45 – 9:30',  'subject' => 'English Language',   'teacher' => 'Mrs. Eze',         'color' => '#2563eb', 'now' => false],
  ['time' => '9:30 – 10:15', 'subject' => 'Basic Science',      'teacher' => 'Mr. Umar',         'color' => '#7c3aed', 'now' => false],
  ['time' => '10:15 – 10:30','subject' => 'Short Break',        'teacher' => '',                 'color' => '#9ca3af', 'now' => false],
  ['time' => '10:30 – 11:15','subject' => 'Social Studies',     'teacher' => 'Mrs. Nwosu',       'color' => '#d97706', 'now' => false],
  ['time' => '11:15 – 12:00','subject' => 'French',             'teacher' => 'Mr. Chukwu',       'color' => '#ea580c', 'now' => false],
  ['time' => '12:00 – 1:00', 'subject' => 'Lunch Break',        'teacher' => '',                 'color' => '#9ca3af', 'now' => false],
  ['time' => '1:00 – 1:45',  'subject' => 'Computer Studies',   'teacher' => 'Mr. Balogun',      'color' => '#0891b2', 'now' => false],
];

$upcomingExams = [
  ['id' => 1, 'subject' => 'Maths',  'title' => 'Mathematics Mid-Term CBT',       'date' => 'Mon, 14 Oct 2024', 'time' => '10:00 AM', 'duration' => 45, 'status' => 'upcoming'],
  ['id' => 2, 'subject' => 'Eng',    'title' => 'English Language Practice Test', 'date' => 'Wed, 16 Oct 2024', 'time' => '9:00 AM',  'duration' => 30, 'status' => 'upcoming'],
  ['id' => 3, 'subject' => 'Sci',    'title' => 'Basic Science Quiz',             'date' => 'Fri, 18 Oct 2024', 'time' => '11:00 AM', 'duration' => 30, 'status' => 'upcoming'],
];

$announcements = [
  ['title' => 'PTA Meeting — Saturday 19th October', 'excerpt' => 'Dear parents and guardians, there will be a PTA meeting this Saturday. All parents are expected to attend.', 'date' => '2 hours ago',  'priority' => 'important'],
  ['title' => 'Inter-House Sports Day Postponed',    'excerpt' => 'Due to the weather forecast, the sports day originally scheduled for this Friday has been moved to November 8.', 'date' => '1 day ago',    'priority' => 'normal'],
  ['title' => 'Mid-Term Exam Timetable Released',    'excerpt' => 'The timetable for first term mid-term examinations is now available. Please check the notice board.', 'date' => '3 days ago',   'priority' => 'important'],
  ['title' => 'Emergency: Water Supply Disruption',  'excerpt' => 'There will be no water supply tomorrow due to plumbing works. Students should come with extra water.', 'date' => '4 days ago',   'priority' => 'emergency'],
];
@endphp

@section('content')

{{-- Greeting Banner --}}
<div class="greeting-banner">
  <div class="greeting-time" id="greeting-time-label">Good Morning</div>
  <div class="greeting-name">Hello, {{ $student['first_name'] }}! 👋</div>
  <div class="greeting-sub">Ready to learn? You have {{ count($upcomingExams) }} upcoming CBT exams this week.</div>
  <div class="greeting-meta">
    <div class="greeting-meta-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      {{ $student['class'] }}
    </div>
    <div class="greeting-meta-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      {{ $currentTerm }}
    </div>
    <div class="greeting-meta-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/></svg>
      Attendance: {{ $attendancePct }}%
    </div>
  </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-icon green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="stat-value">{{ $attendancePct }}%</div>
    <div class="stat-label">Attendance Rate</div>
    <div class="stat-change up">↑ This term</div>
  </div>
  <div class="stat-card gold">
    <div class="stat-icon gold">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
    <div class="stat-value">{{ $avgScore }}%</div>
    <div class="stat-label">Average Score</div>
    <div class="stat-change up">↑ +3.2 from last term</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div class="stat-value">{{ $totalSubjects }}</div>
    <div class="stat-label">Subjects This Term</div>
    <div class="stat-change up">All active</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon purple">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
    </div>
    <div class="stat-value">{{ $classPosition }}</div>
    <div class="stat-label">Class Position</div>
    <div class="stat-change up">↑ Up 2 places</div>
  </div>
</div>

{{-- Main dashboard grid --}}
<div class="dashboard-grid">

  {{-- LEFT: timetable + results preview --}}
  <div style="display:flex;flex-direction:column;gap:20px">

    {{-- Today's Timetable --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Today's Schedule
        </div>
        <a href="{{ route('student.timetable.index') }}" class="card-action">Full timetable →</a>
      </div>
      <div class="card-body" style="padding-top:8px;padding-bottom:8px">
        @foreach($todayTimetable as $period)
        <div class="timetable-row">
          <div class="timetable-time">{{ $period['time'] }}</div>
          <div class="timetable-dot" style="background:{{ $period['color'] }}"></div>
          <div style="flex:1;min-width:0">
            <div class="timetable-subject-name">
              {{ $period['subject'] }}
              @if($period['now'])
                <span class="now-pill">Now</span>
              @endif
            </div>
            @if($period['teacher'])
              <div class="timetable-teacher">{{ $period['teacher'] }}</div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Recent Announcements --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          Recent Announcements
        </div>
        <a href="{{ route('student.announcements.index') }}" class="card-action">View all →</a>
      </div>
      <div class="card-body" style="padding-top:8px;padding-bottom:8px">
        @foreach($announcements as $ann)
        <div class="announcement-item">
          <div class="announcement-icon-wrap ann-{{ $ann['priority'] }}">
            @if($ann['priority'] === 'emergency')
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            @elseif($ann['priority'] === 'important')
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/></svg>
            @else
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            @endif
          </div>
          <div style="flex:1;min-width:0">
            <div class="announcement-title">{{ $ann['title'] }}</div>
            <div class="announcement-excerpt">{{ $ann['excerpt'] }}</div>
            <div class="announcement-date">{{ $ann['date'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>

  {{-- RIGHT: upcoming exams --}}
  <div style="display:flex;flex-direction:column;gap:20px">

    {{-- Upcoming CBT Exams --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Upcoming Exams
        </div>
        <a href="{{ route('student.exams.index') }}" class="card-action">All exams →</a>
      </div>
      <div class="card-body">
        @foreach($upcomingExams as $exam)
        <a href="{{ route('student.exams.lobby', $exam['id']) }}" class="cbt-mini-card">
          <div class="cbt-mini-subject">{{ $exam['subject'] }}</div>
          <div style="flex:1;min-width:0">
            <div class="cbt-mini-name">{{ $exam['title'] }}</div>
            <div class="cbt-mini-date">{{ $exam['date'] }}, {{ $exam['time'] }} · {{ $exam['duration'] }}min</div>
          </div>
          <span class="badge badge-upcoming"><span class="badge-dot"></span>Soon</span>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Quick actions --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          Quick Actions
        </div>
      </div>
      <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <a href="{{ route('student.results.index') }}" class="btn btn-outline btn-sm" style="justify-content:flex-start">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16h16V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          My Results
        </a>
        <a href="{{ route('student.exams.index') }}" class="btn btn-outline btn-sm" style="justify-content:flex-start">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          CBT Exams
        </a>
        <a href="{{ route('student.attendance.index') }}" class="btn btn-outline btn-sm" style="justify-content:flex-start">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/></svg>
          Attendance
        </a>
        <a href="{{ route('student.profile.index') }}" class="btn btn-outline btn-sm" style="justify-content:flex-start">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Profile
        </a>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
  // Dynamic greeting time
  const h = new Date().getHours();
  const greet = h < 12 ? 'Good Morning ☀️' : h < 17 ? 'Good Afternoon 🌤️' : 'Good Evening 🌙';
  document.getElementById('greeting-time-label').textContent = greet;
</script>
@endpush
