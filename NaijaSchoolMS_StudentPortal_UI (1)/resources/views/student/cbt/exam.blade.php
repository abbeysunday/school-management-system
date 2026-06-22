{{--
    CBT Exam Interface — Full screen, no layout
    File: resources/views/student/cbt/exam.blade.php
    Security: Back-button locked via JS
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Biology CBT — In Progress</title>
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
</head>
<body>

@php
// TODO: Replace with $attempt and $exam from controller
$exam = ['id'=>2,'subject'=>'Biology','type'=>'Formal','duration_seconds'=>3600,'class_arm'=>'SS2 Science'];
$attempt = ['id'=>11];
$questions = [
    ['id'=>1,'text'=>'Which organelle is responsible for producing energy (ATP) in a cell?','options'=>['A'=>'Nucleus','B'=>'Ribosome','C'=>'Mitochondria','D'=>'Golgi apparatus'],'selected'=>'C','flagged'=>false],
    ['id'=>2,'text'=>'The process by which green plants manufacture food using sunlight is called:','options'=>['A'=>'Respiration','B'=>'Photosynthesis','C'=>'Fermentation','D'=>'Transpiration'],'selected'=>null,'flagged'=>true],
    ['id'=>3,'text'=>'Which of the following is NOT a characteristic of living organisms?','options'=>['A'=>'Reproduction','B'=>'Growth','C'=>'Combustion','D'=>'Excretion'],'selected'=>null,'flagged'=>false],
    ['id'=>4,'text'=>'The basic unit of life is the:','options'=>['A'=>'Tissue','B'=>'Organ','C'=>'Cell','D'=>'Organism'],'selected'=>'C','flagged'=>false],
    ['id'=>5,'text'=>'Osmosis is the movement of water molecules from a region of:','options'=>['A'=>'High solute concentration to low solute concentration','B'=>'Low water concentration to high water concentration','C'=>'High water concentration to low water concentration through a semi-permeable membrane','D'=>'Low solute concentration to high solute concentration through any membrane'],'selected'=>null,'flagged'=>false],
    // ... more questions loaded from DB
];
$currentQ   = 1;
$totalQ     = 60; // from exam config
$answeredQ  = 2;
@endphp

<div class="cbt-shell">

    {{-- ── TOPBAR ── --}}
    <div class="cbt-topbar">
        <div>
            <div class="cbt-ttitle">{{ $exam['subject'] }} CBT</div>
            <div class="cbt-tsubj">{{ $exam['class_arm'] }} · {{ $exam['type'] }}</div>
        </div>

        <div class="cbt-timer-box" id="examTimer">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span id="timerDisplay">01:00:00</span>
        </div>

        <button id="submitExamBtn" class="btn btn-danger btn-sm" type="button">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
            Submit Exam
        </button>
    </div>

    {{-- ── BODY ── --}}
    <div class="cbt-body">

        {{-- Question Panel --}}
        <div class="cbt-q-wrap" id="questionPanel">

            {{-- Progress --}}
            <div class="cbt-prog">
                <div class="cbt-prog-txt">
                    <span>Question <span id="currentQNum">{{ $currentQ }}</span> of {{ $totalQ }}</span>
                    <span>{{ $answeredQ }} answered</span>
                </div>
                <div class="cbt-prog-bar"><div class="cbt-prog-fill" id="cbtProgFill" style="width:{{ ($answeredQ/$totalQ)*100 }}%"></div></div>
            </div>

            {{-- Questions (all rendered, toggled by JS) --}}
            <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam['id']) }}">
                @csrf
                <input type="hidden" name="attempt_id" value="{{ $attempt['id'] }}">

                @foreach($questions as $i => $q)
                <div data-q="{{ $q['id'] }}" style="{{ $i > 0 ? 'display:none' : '' }}">
                    <div class="cbt-qnum">Question {{ $q['id'] }} of {{ $totalQ }}</div>
                    <div class="cbt-qtext">{{ $q['text'] }}</div>

                    <div class="cbt-options">
                        @foreach($q['options'] as $letter => $text)
                        <div class="cbt-opt {{ $q['selected'] === $letter ? 'sel' : '' }}"
                             data-option="{{ $letter }}"
                             onclick="selectOption(this, {{ $q['id'] }})">
                            <span class="cbt-opt-ltr">{{ $letter }}</span>
                            <span>{{ $text }}</span>
                            <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $letter }}"
                                   {{ $q['selected'] === $letter ? 'checked' : '' }}
                                   style="display:none">
                        </div>
                        @endforeach
                    </div>

                    {{-- Flag & navigation --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:22px;flex-wrap:wrap;gap:10px">
                        <button type="button" class="btn btn-ghost btn-sm {{ $q['flagged'] ? 'flagged' : '' }}"
                                data-flag-btn="{{ $q['id'] }}"
                                style="{{ $q['flagged'] ? 'background:rgba(245,158,11,.15);border-color:#F59E0B;color:#FCD34D' : '' }}">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path stroke-linecap="round" d="M3 3v18M3 6l10-3 4 4-10 3H3"/></svg>
                            {{ $q['flagged'] ? 'Flagged' : 'Flag' }}
                        </button>

                        <div style="display:flex;gap:8px">
                            @if($i > 0)
                            <button type="button" class="btn btn-ghost btn-sm" onclick="goToQ({{ $q['id'] - 1 }})">
                                ← Prev
                            </button>
                            @endif
                            @if($i < count($questions) - 1)
                            <button type="button" class="btn btn-primary btn-sm" onclick="goToQ({{ $q['id'] + 1 }})">
                                Next →
                            </button>
                            @else
                            <button type="button" class="btn btn-gold btn-sm" id="submitExamBtn2">
                                Finish & Submit
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </form>
        </div>

        {{-- Navigator Panel --}}
        <div class="cbt-nav-wrap">
            <div class="cbt-nav-hdr">Navigator</div>
            <div class="cbt-legend">
                <div class="cbt-leg-item"><div class="cbt-leg-dot" style="background:var(--primary)"></div>Answered</div>
                <div class="cbt-leg-item"><div class="cbt-leg-dot" style="background:#F59E0B"></div>Flagged</div>
                <div class="cbt-leg-item"><div class="cbt-leg-dot" style="background:rgba(255,255,255,.12)"></div>Not Answered</div>
            </div>
            <div class="cbt-grid">
                @for($n = 1; $n <= $totalQ; $n++)
                <button type="button" class="cbt-nb
                    {{ $n === $currentQ ? 'cur' : '' }}
                    @foreach($questions as $q)
                        @if($q['id'] === $n && $q['selected']) ans @break @endif
                        @if($q['id'] === $n && $q['flagged']) flag @break @endif
                    @endforeach
                    " data-q="{{ $n }}" onclick="goToQ({{ $n }})">{{ $n }}</button>
                @endfor
            </div>
            <div style="padding:0 13px 14px">
                <div style="background:rgba(255,255,255,.04);border-radius:8px;padding:10px;font-size:.72rem;color:rgba(255,255,255,.4)">
                    <div style="margin-bottom:5px">Answered: <strong style="color:#4ade80">{{ $answeredQ }}</strong></div>
                    <div style="margin-bottom:5px">Flagged: <strong style="color:#FCD34D">1</strong></div>
                    <div>Remaining: <strong style="color:rgba(255,255,255,.6)">{{ $totalQ - $answeredQ }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="cbt-foot">
        <div class="cbt-foot-info">
            <span>Auto-save enabled</span>
            <span style="margin:0 8px">·</span>
            <span>Do not close this window</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:.72rem;color:rgba(255,255,255,.3)">
                {{ $answeredQ }}/{{ $totalQ }} answered
            </span>
            <button id="submitExamBtn" type="button" class="btn btn-danger btn-sm">
                Submit Exam
            </button>
        </div>
    </div>

    {{-- ── SUBMIT CONFIRM MODAL ── --}}
    <div id="submitModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;align-items:center;justify-content:center;padding:20px">
        <div style="background:#0F1F15;border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:32px;max-width:420px;width:100%;text-align:center">
            <div style="font-size:2.5rem;margin-bottom:14px">⚠️</div>
            <h3 style="color:white;font-size:1.1rem;font-weight:700;margin-bottom:8px">Submit Exam?</h3>
            <p style="color:rgba(255,255,255,.55);font-size:.85rem;line-height:1.6;margin-bottom:8px">
                You have answered <strong style="color:white">{{ $answeredQ }}</strong> of <strong style="color:white">{{ $totalQ }}</strong> questions.
            </p>
            <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-bottom:24px">
                Unanswered questions will be marked as incorrect. This action cannot be undone.
            </p>
            <div style="display:flex;gap:10px">
                <button id="cancelSubmitBtn" type="button" class="btn btn-ghost btn-block" style="color:white;border-color:rgba(255,255,255,.2)">
                    Continue Exam
                </button>
                <button id="confirmSubmitBtn" type="button" class="btn btn-danger btn-block">
                    Yes, Submit Now
                </button>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/student.js') }}"></script>
<script>
// ── Initialize CBT ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Lock back button
    NaijaSchoolCBT.lockNavigation();

    // Start exam timer (duration_seconds from PHP)
    const timerDisplay = document.getElementById('timerDisplay');
    NaijaSchoolCBT.startTimer(
        {{ $exam['duration_seconds'] }},
        timerDisplay,
        () => {
            // Auto-submit when timer expires
            window.onbeforeunload = null;
            document.getElementById('examForm')?.submit();
        }
    );

    // Init option selection & flags
    NaijaSchoolCBT.initFlags();
    NaijaSchoolCBT.initSubmitButton();

    // Auto-save every 30 seconds
    NaijaSchoolCBT.initAutoSave({{ $attempt['id'] }});
});

// ── Option selection ─────────────────────────────────────────
function selectOption(el, qId) {
    const group = el.closest('.cbt-options');
    group?.querySelectorAll('.cbt-opt').forEach(o => o.classList.remove('sel'));
    el.classList.add('sel');
    el.querySelector('input[type=radio]').checked = true;

    // Update navigator button
    const nb = document.querySelector(`.cbt-nb[data-q="${qId}"]`);
    if (nb && !nb.classList.contains('flag')) { nb.classList.add('ans'); }
}

// ── Navigate to question ──────────────────────────────────────
function goToQ(qNum) {
    document.querySelectorAll('[data-q]').forEach(q => q.style.display = 'none');
    const target = document.querySelector(`[data-q="${qNum}"]`);
    if (target) target.style.display = 'block';
    document.querySelectorAll('.cbt-nb').forEach(nb => nb.classList.remove('cur'));
    const nb = document.querySelector(`.cbt-nb[data-q="${qNum}"]`);
    if (nb) { nb.classList.add('cur'); nb.scrollIntoView({block:'nearest'}); }
    const curEl = document.getElementById('currentQNum');
    if (curEl) curEl.textContent = qNum;
    const prog = document.getElementById('cbtProgFill');
    if (prog) prog.style.width = (qNum / {{ $totalQ }} * 100) + '%';
    window.scrollTo({top:0,behavior:'smooth'});
}
</script>
</body>
</html>
