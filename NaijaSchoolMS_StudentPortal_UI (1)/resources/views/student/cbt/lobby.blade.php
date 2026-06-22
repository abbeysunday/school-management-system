{{--
    CBT Exam Lobby — Full screen, no sidebar/topbar
    File: resources/views/student/cbt/lobby.blade.php
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exam Lobby — Biology CBT</title>
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/student.css') }}"> --}}
</head>
<body>

@php
// TODO: Replace with $exam from controller
$exam = [
    'id'          => 2,
    'subject'     => 'Biology',
    'type'        => 'Formal',
    'icon'        => '🔬',
    'term'        => 'Third Term · 2024/2025',
    'class_arm'   => 'SS2 Science',
    'questions'   => 60,
    'duration'    => 60,  // minutes
    'marks'       => 60,
    'start_datetime' => now()->addMinutes(4)->toIso8601String(), // change to DB value
    'instructions'=> [
        'This exam contains 60 multiple-choice questions.',
        'You have 60 minutes to complete all questions.',
        'Each question carries 1 mark. There is no negative marking.',
        'Answer as many questions as possible before time runs out.',
        'Do not close your browser or navigate away during the exam.',
        'Your answers are auto-saved every 30 seconds.',
        'The exam will submit automatically when time expires.',
    ],
];
$isLive = false; // TODO: $exam['start_datetime'] <= now()
@endphp

<div class="lobby-shell">

    {{-- Topbar --}}
    <div class="lobby-topbar">
        <a href="{{ route('student.exams.index') }}" style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.55);font-size:.82rem;font-weight:500;transition:color .2s" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.55)'">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Back to Exams
        </a>
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:.78rem;color:rgba(255,255,255,.35)">{{ $exam['class_arm'] }}</span>
            <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.2)"></span>
            <span style="font-size:.78rem;color:rgba(255,255,255,.35)">{{ $exam['term'] }}</span>
        </div>
    </div>

    {{-- Main content --}}
    <div class="lobby-wrap">
        <div class="lobby-card">

            <div class="lobby-icon">{{ $exam['icon'] }}</div>
            <div class="lobby-subj">{{ $exam['subject'] }}</div>
            <div class="lobby-type">{{ $exam['type'] }} Examination · {{ $exam['class_arm'] }}</div>

            {{-- Countdown / Status --}}
            <div class="lobby-cd-box">
                <div class="lobby-cd-lbl" id="cdLabel">Exam starts in</div>
                <div class="lobby-cd-time" id="cdTime">--:--:--</div>
            </div>

            {{-- Info row --}}
            <div class="lobby-info-row">
                <div class="lobby-info-item">
                    <div class="lobby-info-val">{{ $exam['questions'] }}</div>
                    <div class="lobby-info-lbl">Questions</div>
                </div>
                <div class="lobby-info-item">
                    <div class="lobby-info-val">{{ $exam['duration'] }}<span style="font-size:.65rem;font-weight:400">min</span></div>
                    <div class="lobby-info-lbl">Duration</div>
                </div>
                <div class="lobby-info-item">
                    <div class="lobby-info-val">{{ $exam['marks'] }}</div>
                    <div class="lobby-info-lbl">Total Marks</div>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="lobby-inst">
                <div class="lobby-inst-ttl">📋 Instructions</div>
                <ul>
                    @foreach($exam['instructions'] as $inst)
                    <li>{{ $inst }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Enter button --}}
            <form method="POST" action="{{ route('student.exams.start', $exam['id']) }}" id="startForm">
                @csrf
                <button type="submit"
                    id="enterBtn"
                    class="btn btn-gold btn-xl btn-block"
                    @if(!$isLive) disabled @endif>
                    @if($isLive)
                        🚀 Enter Exam Now
                    @else
                        ⏳ Waiting for Exam to Open...
                    @endif
                </button>
            </form>

            <p style="font-size:.72rem;color:rgba(255,255,255,.3);margin-top:12px;line-height:1.6">
                By clicking Enter, you confirm you are ready and have read all instructions. The timer starts immediately.
            </p>

        </div>
    </div>
</div>

<script src="{{ asset('js/student.js') }}"></script>
{{-- <script src="{{ asset('js/student.js') }}"></script> --}}
<script>
// Start countdown to exam start time
const startTime = "{{ $exam['start_datetime'] }}";
const cdEl      = document.getElementById('cdTime');
const cdLbl     = document.getElementById('cdLabel');
const enterBtn  = document.getElementById('enterBtn');

function tick() {
    const diff = new Date(startTime) - new Date();
    if (diff <= 0) {
        // EXAM IS LIVE
        cdLbl.textContent = 'Exam is now open!';
        cdEl.textContent  = 'Go!';
        cdEl.classList.add('live');
        enterBtn.disabled = false;
        enterBtn.textContent = '🚀 Enter Exam Now';
        clearInterval(timer);
        return;
    }
    const h = Math.floor(diff/3600000);
    const m = Math.floor((diff%3600000)/60000);
    const s = Math.floor((diff%60000)/1000);
    cdEl.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}

tick();
const timer = setInterval(tick, 1000);
</script>
</body>
</html>
