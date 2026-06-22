{{-- ══════════════════════════════════════════════════════
     CBT EXAM — full-screen layout (no sidebar/header/footer)
     ════════════════════════════════════════════════════════ --}}
@php
$attemptId = $attemptId ?? 1;
$exam = [
    'id'        => 1,
    'title'     => 'Mathematics Mid-Term CBT',
    'subject'   => 'Mathematics',
    'duration'  => 2700, // 45 min in seconds
    'questions' => [
        ['q'=>'Which of the following is the value of x in the equation 3x + 9 = 24?', 'opts'=>['A'=>'3','B'=>'5','C'=>'7','D'=>'9'],  'key'=>'B'],
        ['q'=>'Simplify: 2(3a - 4b) + 3(a + 2b)',                                      'opts'=>['A'=>'9a - 2b','B'=>'7a - 2b','C'=>'9a + 2b','D'=>'5a + 2b'], 'key'=>'A'],
        ['q'=>'What is the LCM of 12, 16, and 24?',                                     'opts'=>['A'=>'24','B'=>'48','C'=>'72','D'=>'96'], 'key'=>'B'],
        ['q'=>'The area of a rectangle is 48 cm². If the length is 8 cm, what is the width?', 'opts'=>['A'=>'4 cm','B'=>'6 cm','C'=>'8 cm','D'=>'10 cm'], 'key'=>'B'],
        ['q'=>'Which of the following is a prime number?',                               'opts'=>['A'=>'21','B'=>'27','C'=>'29','D'=>'33'], 'key'=>'C'],
    ],
];
$total = count($exam['questions']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $exam['title'] }} — NaijaSchoolMS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            forest: { 50:'#f0fdf4',100:'#dcfce7',300:'#86efac',500:'#22c55e',600:'#1a6e38',700:'#16582e',800:'#0f4a27',900:'#0d3b1f' },
            gold:   { 400:'#fbbf24',600:'#d97706' },
          },
          fontFamily: { display:['"Fraunces"','serif'], body:['"Plus Jakarta Sans"','sans-serif'] },
        }
      }
    }
  </script>
  <link rel="stylesheet" href="{{ asset('assets/css/student.css') }}">
</head>
<body class="bg-gray-100 font-body" id="exam-root"
      data-total="{{ $total }}"
      data-duration="{{ $exam['duration'] }}"
      data-attempt="{{ $attemptId }}">

<div class="exam-shell">

  {{-- ── Top bar ──────────────────────────────────────────── --}}
  <div class="exam-topbar">
    {{-- Subject badge --}}
    <span class="text-xs font-bold text-white/50 uppercase tracking-widest flex-shrink-0 hidden sm:block">{{ $exam['subject'] }}</span>
    <span class="text-white/30 hidden sm:block">|</span>

    {{-- Title --}}
    <span class="text-sm font-semibold text-white/80 flex-1 truncate">{{ $exam['title'] }}</span>

    {{-- Q counter --}}
    <span class="text-xs text-white/50 flex-shrink-0 hidden sm:block" id="q-count">1 / {{ $total }}</span>

    {{-- Timer --}}
    <div class="exam-timer" id="exam-timer">45:00</div>

    {{-- Flag button --}}
    <button onclick="toggleFlag()" id="flag-btn" title="Flag this question (F)"
            class="flex items-center gap-1 text-white/50 hover:text-yellow-400 text-xs font-semibold transition-colors flex-shrink-0">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
      <span class="hidden sm:inline">Flag</span>
    </button>
  </div>

  {{-- Progress bar --}}
  <div class="exam-progress-bar">
    <div class="exam-progress-fill" id="exam-progress-fill" style="width:{{ round(1/$total*100) }}%"></div>
  </div>

  {{-- ── Main area ────────────────────────────────────────── --}}
  <div class="flex flex-1 overflow-hidden">

    {{-- Question panel --}}
    <div class="flex-1 overflow-y-auto p-4 md:p-8">
      <div class="max-w-2xl mx-auto">

        @foreach($exam['questions'] as $qi => $question)
          <div class="question-slide {{ $qi === 0 ? 'active' : '' }}" data-q="{{ $qi }}">

            {{-- Question number + text --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 md:p-6 mb-4">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Question {{ $qi+1 }} of {{ $total }}</p>
              <p class="text-base md:text-lg font-semibold text-gray-900 leading-relaxed">{{ $question['q'] }}</p>
            </div>

            {{-- Options --}}
            <div class="flex flex-col gap-3">
              @foreach($question['opts'] as $key => $val)
                <button class="option-btn" data-q="{{ $qi }}" data-opt="{{ $key }}">
                  <span class="opt-key">{{ $key }}</span>
                  <span>{{ $val }}</span>
                </button>
              @endforeach
            </div>

          </div>
        @endforeach

        {{-- Navigation buttons --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
          <button onclick="prevQ()" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Previous
          </button>
          <button onclick="openSubmitModal()" class="flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Submit Exam
          </button>
          <button onclick="nextQ()" class="flex items-center gap-2 px-5 py-2.5 bg-forest-700 text-white text-sm font-semibold rounded-xl hover:bg-forest-900 transition-colors">
            Next
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>

      </div>
    </div>

    {{-- Navigator panel (right) --}}
    <div class="hidden lg:flex flex-col w-52 xl:w-60 border-l border-gray-200 bg-white overflow-y-auto flex-shrink-0">
      <div class="p-4 border-b border-gray-100">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Questions</p>
      </div>
      <div class="p-4">
        <div class="grid grid-cols-5 gap-1.5 mb-4">
          @foreach($exam['questions'] as $qi => $q)
            <button class="nav-q-btn {{ $qi === 0 ? 'current' : '' }}" data-qn="{{ $qi }}" onclick="gotoQuestion({{ $qi }})">{{ $qi+1 }}</button>
          @endforeach
        </div>
        <div class="space-y-1.5 text-xs">
          <div class="flex items-center gap-2 text-gray-500">
            <span class="w-4 h-4 rounded bg-forest-700 inline-block"></span> Answered
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <span class="w-4 h-4 rounded bg-yellow-400 inline-block"></span> Flagged
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <span class="w-4 h-4 rounded border-2 border-gray-300 inline-block"></span> Unanswered
          </div>
        </div>
      </div>
      <div class="mt-auto p-4 border-t border-gray-100">
        <button onclick="openSubmitModal()" class="w-full py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-colors">
          Submit Exam
        </button>
      </div>
    </div>

  </div>{{-- /main area --}}

</div>{{-- /exam-shell --}}

{{-- ── Submit modal ─────────────────────────────────────────── --}}
<div class="modal-overlay" id="submit-modal">
  <div class="modal">
    <h2 class="font-display text-xl font-bold text-gray-900 mb-1">Submit Exam?</h2>
    <p class="text-sm text-gray-500 mb-5">Once submitted you cannot re-enter this exam.</p>

    <div class="grid grid-cols-3 gap-3 mb-5">
      <div class="bg-green-50 rounded-xl p-3 text-center">
        <p class="font-display text-2xl font-bold text-gray-900" id="modal-answered">0</p>
        <p class="text-xs text-gray-400 mt-1">Answered</p>
      </div>
      <div class="bg-red-50 rounded-xl p-3 text-center">
        <p class="font-display text-2xl font-bold text-gray-900" id="modal-unanswered">0</p>
        <p class="text-xs text-gray-400 mt-1">Unanswered</p>
      </div>
      <div class="bg-yellow-50 rounded-xl p-3 text-center">
        <p class="font-display text-2xl font-bold text-gray-900" id="modal-flagged">0</p>
        <p class="text-xs text-gray-400 mt-1">Flagged</p>
      </div>
    </div>

    <div class="flex gap-3">
      <button onclick="closeSubmitModal()" class="flex-1 py-2.5 border-2 border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors">
        Continue Exam
      </button>
      <button onclick="confirmSubmit()" class="flex-1 py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-colors">
        Submit Now
      </button>
    </div>
  </div>
</div>

{{-- Hidden form for submission --}}
<form id="exam-submit-form" method="POST" action="{{ route('student.exams.submit', $exam['id']) }}" style="display:none">
  @csrf
</form>

<div id="toast-wrap"></div>
<script src="{{ asset('assets/js/student.js') }}"></script>
</body>
</html>
