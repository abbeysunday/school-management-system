@extends('student.layouts.app')

@section('title', 'My Exams')
@section('page-title', 'CBT Exams')
@section('page-sub', 'Your assigned Computer-Based Tests')

@php
$exams = [
  [
    'id' => 1, 'subject' => 'Mathematics', 'subject_code' => 'MTH',
    'title' => 'Mathematics — First Term Mid-Term Examination',
    'exam_type' => 'Formal', 'questions' => 40, 'duration' => 45,
    'total_marks' => 40, 'date' => 'Mon, Oct 14, 2024', 'time' => '10:00 AM',
    'end_time' => '10:45 AM', 'status' => 'upcoming', 'score' => null,
  ],
  [
    'id' => 2, 'subject' => 'English', 'subject_code' => 'ENG',
    'title' => 'English Language Practice Test',
    'exam_type' => 'Practice', 'questions' => 30, 'duration' => 30,
    'total_marks' => 30, 'date' => 'Wed, Oct 16, 2024', 'time' => '9:00 AM',
    'end_time' => '9:30 AM', 'status' => 'upcoming', 'score' => null,
  ],
  [
    'id' => 3, 'subject' => 'Basic Sci.', 'subject_code' => 'BSC',
    'title' => 'Basic Science — Unit 3 Quiz',
    'exam_type' => 'Formal', 'questions' => 25, 'duration' => 30,
    'total_marks' => 25, 'date' => 'Fri, Oct 18, 2024', 'time' => '11:00 AM',
    'end_time' => '11:30 AM', 'status' => 'active', 'score' => null,
  ],
  [
    'id' => 4, 'subject' => 'Social St.', 'subject_code' => 'SST',
    'title' => 'Social Studies — Chapter 4 Assessment',
    'exam_type' => 'Practice', 'questions' => 20, 'duration' => 25,
    'total_marks' => 20, 'date' => 'Mon, Oct 7, 2024', 'time' => '10:00 AM',
    'end_time' => '10:25 AM', 'status' => 'completed', 'score' => 17,
  ],
  [
    'id' => 5, 'subject' => 'Mathematics', 'subject_code' => 'MTH',
    'title' => 'Mathematics — Numbers and Numeration Practice',
    'exam_type' => 'Practice', 'questions' => 20, 'duration' => 20,
    'total_marks' => 20, 'date' => 'Fri, Oct 4, 2024', 'time' => '2:00 PM',
    'end_time' => '2:20 PM', 'status' => 'completed', 'score' => 15,
  ],
];
@endphp

@section('content')

{{-- Filter bar --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;flex-wrap:wrap">
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    @foreach(['All', 'Upcoming', 'Active', 'Completed'] as $filter)
    <button class="term-pill {{ $loop->first ? 'active' : '' }}" data-filter="{{ strtolower($filter) }}"
            onclick="filterExams('{{ strtolower($filter) }}')">
      {{ $filter }}
    </button>
    @endforeach
  </div>
  <div style="margin-left:auto;font-size:13px;color:var(--text-muted)">
    Showing {{ count($exams) }} exams
  </div>
</div>

{{-- Active exam banner --}}
@php $activeExam = collect($exams)->firstWhere('status', 'active'); @endphp
@if($activeExam)
<div class="result-publish-banner" style="margin-bottom:20px;background:#fef3c7;border-color:#fde68a">
  <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="width:18px;height:18px;flex-shrink:0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
  <div class="result-publish-text" style="color:#92400e">
    <strong>Active exam:</strong> {{ $activeExam['title'] }} is currently LIVE!
  </div>
  <a href="{{ route('student.exams.lobby', $activeExam['id']) }}" class="btn btn-accent btn-sm">Enter Now</a>
</div>
@endif

{{-- Exams grid --}}
<div class="exams-grid" id="exams-grid">
  @foreach($exams as $exam)
  <div class="exam-card {{ $exam['status'] === 'active' ? 'active-exam' : '' }}" data-status="{{ $exam['status'] }}">

    <div class="exam-card-top">
      <span class="exam-subject-chip">{{ $exam['subject_code'] }}</span>
      <div class="badge badge-{{ $exam['status'] }}">
        <span class="badge-dot"></span>
        {{ ucfirst($exam['status']) }}
      </div>
    </div>

    <div class="exam-title">{{ $exam['title'] }}</div>
    <div class="exam-type-label">{{ $exam['exam_type'] }} Exam</div>

    <div class="exam-meta-row">
      <div class="exam-meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <strong>{{ $exam['date'] }}</strong>
      </div>
      <div class="exam-meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <strong>{{ $exam['time'] }}</strong>
      </div>
      <div class="exam-meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        <strong>{{ $exam['duration'] }} min</strong>
      </div>
      <div class="exam-meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 2h7M12 2v6l3 2"/><circle cx="12" cy="14" r="8"/></svg>
        <strong>{{ $exam['questions'] }} questions</strong>
      </div>
    </div>

    @if($exam['status'] === 'completed' && $exam['score'] !== null)
    <div style="margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:5px">
        <span>Your Score</span>
        <strong style="color:var(--text-heading)">{{ $exam['score'] }}/{{ $exam['total_marks'] }}</strong>
      </div>
      @php $pct = round(($exam['score'] / $exam['total_marks']) * 100); @endphp
      <div class="score-bar-track" style="height:7px">
        <div class="score-bar-fill" data-width="{{ $pct }}%" style="width:0%"></div>
      </div>
      <div style="font-size:11.5px;color:var(--text-muted);margin-top:3px">{{ $pct }}%</div>
    </div>
    @endif

    <div class="exam-card-actions">
      @if($exam['status'] === 'active')
        <a href="{{ route('student.exams.lobby', $exam['id']) }}" class="btn btn-accent">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
          Enter Exam
        </a>
      @elseif($exam['status'] === 'upcoming')
        <a href="{{ route('student.exams.lobby', $exam['id']) }}" class="btn btn-outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          View Details
        </a>
      @else
        @if($exam['score'] !== null)
          <span class="exam-score-chip">{{ round(($exam['score']/$exam['total_marks'])*100) }}% — {{ $exam['score'] }}/{{ $exam['total_marks'] }}</span>
        @endif
        <a href="{{ route('student.exams.result', $exam['id']) }}" class="btn btn-ghost btn-sm">
          View Result →
        </a>
      @endif
    </div>
  </div>
  @endforeach
</div>

@endsection

@push('scripts')
<script>
function filterExams(status) {
  document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
  document.querySelector(`[data-filter="${status}"]`)?.classList.add('active');
  document.querySelectorAll('[data-status]').forEach(card => {
    card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
  });
  // Re-animate score bars on reveal
  document.querySelectorAll('.score-bar-fill').forEach(bar => {
    if (bar.style.width === '0%') bar.style.width = bar.dataset.width || '0%';
  });
}
// Init score bars
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.querySelectorAll('.score-bar-fill').forEach(bar => {
      bar.style.width = bar.dataset.width || '0%';
    });
  }, 200);
});
</script>
@endpush
