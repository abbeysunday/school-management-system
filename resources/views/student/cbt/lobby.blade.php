@extends('student.layouts.app')

@section('title', 'Exam Lobby')
@section('page-title', 'Exam Lobby')
@section('page-sub', 'Prepare before entering the exam')

@php
$examId   = $examId ?? 1;
$exam = [
    'id'          => $examId,
    'title'       => 'Mathematics Mid-Term CBT',
    'subject'     => 'Mathematics',
    'date'        => 'Monday, 4 November 2024',
    'start_time'  => '10:00 AM',
    'duration'    => 45,
    'questions'   => 40,
    'instructions'=> [
        'Read each question carefully before selecting your answer.',
        'Each question carries equal marks. No negative marking.',
        'You may flag questions and return to them before submitting.',
        'Do not refresh or close your browser during the exam.',
        'The exam will auto-submit when the timer reaches zero.',
        'Only one attempt is allowed. You cannot re-enter once submitted.',
    ],
    // Set this to a future time to test countdown; past time enables Enter button
    'start_datetime' => date('Y-m-d H:i:s', strtotime('+2 hours')),
];

// If start time has passed, enable button immediately
$examStarted = strtotime($exam['start_datetime']) <= time();
@endphp

@section('content')

<div class="max-w-2xl mx-auto">

  {{-- Back link --}}
  <a href="{{ route('student.exams.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-forest-700 mb-5 transition-colors">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Exams
  </a>

  {{-- Hero card --}}
  <div class="bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 rounded-2xl p-6 md:p-8 text-white mb-5 relative overflow-hidden">
    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-white/[.04] pointer-events-none"></div>
    <span class="inline-block text-xs font-bold uppercase tracking-widest text-white/50 mb-2">{{ $exam['subject'] }}</span>
    <h1 class="font-display text-2xl md:text-3xl font-bold mb-5">{{ $exam['title'] }}</h1>

    {{-- Info grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      @foreach([
        ['label'=>'Date',      'value'=>$exam['date']],
        ['label'=>'Start Time','value'=>$exam['start_time']],
        ['label'=>'Duration',  'value'=>$exam['duration'].' minutes'],
        ['label'=>'Questions', 'value'=>$exam['questions'].' questions'],
      ] as $info)
        <div class="bg-white/10 rounded-xl p-3">
          <p class="text-[10px] text-white/50 uppercase tracking-wider mb-1">{{ $info['label'] }}</p>
          <p class="text-sm font-bold text-white">{{ $info['value'] }}</p>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Countdown card --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-5 text-center"
       id="lobby-target"
       data-target="{{ $exam['start_datetime'] }}">

    @if($examStarted)
      <div class="py-4">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
          <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <p class="font-display text-xl font-bold text-gray-900 mb-1">Exam is Open</p>
        <p class="text-sm text-gray-400">You may enter the exam now.</p>
      </div>
    @else
      <p class="text-sm font-semibold text-gray-500 mb-5">Exam starts in</p>
      <div class="flex items-end justify-center gap-2 md:gap-3 mb-5">
        <div class="cd-unit">
          <span class="cd-num" id="cd-h">00</span>
          <p class="cd-lbl">Hours</p>
        </div>
        <span class="cd-sep mb-7">:</span>
        <div class="cd-unit">
          <span class="cd-num" id="cd-m">00</span>
          <p class="cd-lbl">Minutes</p>
        </div>
        <span class="cd-sep mb-7">:</span>
        <div class="cd-unit">
          <span class="cd-num" id="cd-s">00</span>
          <p class="cd-lbl">Seconds</p>
        </div>
      </div>
      <p class="text-xs text-gray-400">The Enter Exam button will activate when the exam starts.</p>
    @endif

  </div>

  {{-- Instructions --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
    <h3 class="font-display font-semibold text-gray-900 mb-4 flex items-center gap-2">
      <svg class="w-4 h-4 text-forest-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Instructions
    </h3>
    <ul class="space-y-3">
      @foreach($exam['instructions'] as $i => $inst)
        <li class="flex items-start gap-3 text-sm text-gray-600">
          <span class="w-5 h-5 rounded-full bg-forest-100 text-forest-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
          {{ $inst }}
        </li>
      @endforeach
    </ul>
  </div>

  {{-- Confirm + Enter --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-5">
    <label class="flex items-start gap-3 cursor-pointer mb-5">
      <input type="checkbox" id="lobby-confirm" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-forest-700 focus:ring-forest-600 cursor-pointer flex-shrink-0">
      <span class="text-sm text-gray-700">I have read and understood all the instructions above. I am ready to begin this exam.</span>
    </label>

    <form method="POST" action="{{ route('student.exams.start', $exam['id']) }}">
      @csrf
      <button id="lobby-enter-btn"
              type="submit"
              {{ !$examStarted ? 'disabled' : '' }}
              class="w-full py-3.5 bg-forest-700 text-white font-bold text-sm rounded-xl hover:bg-forest-900 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Enter Exam
      </button>
    </form>
  </div>

</div>

@endsection
