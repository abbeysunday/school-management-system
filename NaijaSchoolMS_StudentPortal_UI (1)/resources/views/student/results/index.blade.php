{{-- File: resources/views/student/results/index.blade.php --}}
@extends('student.layouts.app')
@section('title', 'My Results')
@section('page-title', 'My Results')
@section('page-subtitle', 'Academic performance across all terms')

@section('content')
@php
$sessions = [
    ['label'=>'2024/2025', 'terms'=>['First Term','Second Term','Third Term']],
    ['label'=>'2023/2024', 'terms'=>['First Term','Second Term','Third Term']],
];
$currentSession = '2024/2025';
$currentTerm    = 'First Term';

$results = [
    ['subject'=>'Mathematics',      'icon'=>'📐','ca'=>25,'exam'=>60,'total'=>85,'grade'=>'A1','pos'=>2,'avg'=>68.4,'highest'=>92,'lowest'=>41,'remark'=>'Excellent performance. Keep it up!'],
    ['subject'=>'English Language', 'icon'=>'📝','ca'=>22,'exam'=>53,'total'=>75,'grade'=>'A1','pos'=>5,'avg'=>64.2,'highest'=>82,'lowest'=>38,'remark'=>'Very good. Work on comprehension.'],
    ['subject'=>'Biology',          'icon'=>'🔬','ca'=>20,'exam'=>50,'total'=>70,'grade'=>'B2','pos'=>6,'avg'=>61.5,'highest'=>78,'lowest'=>35,'remark'=>'Good effort. Revise genetics.'],
    ['subject'=>'Chemistry',        'icon'=>'⚗️','ca'=>18,'exam'=>46,'total'=>64,'grade'=>'C4','pos'=>10,'avg'=>58.3,'highest'=>76,'lowest'=>30,'remark'=>'Average. More practice needed.'],
    ['subject'=>'Physics',          'icon'=>'⚡','ca'=>19,'exam'=>47,'total'=>66,'grade'=>'B3','pos'=>7,'avg'=>60.8,'highest'=>80,'lowest'=>32,'remark'=>'Good. Focus on calculations.'],
    ['subject'=>'Economics',        'icon'=>'📈','ca'=>23,'exam'=>55,'total'=>78,'grade'=>'A1','pos'=>4,'avg'=>63.1,'highest'=>85,'lowest'=>40,'remark'=>'Excellent grasp of concepts.'],
    ['subject'=>'Government',       'icon'=>'🏛️','ca'=>21,'exam'=>52,'total'=>73,'grade'=>'B2','pos'=>6,'avg'=>62.5,'highest'=>79,'lowest'=>37,'remark'=>'Very good performance.'],
    ['subject'=>'Further Maths',    'icon'=>'🔢','ca'=>17,'exam'=>43,'total'=>60,'grade'=>'C5','pos'=>12,'avg'=>55.2,'highest'=>74,'lowest'=>28,'remark'=>'Satisfactory. Practice more.'],
    ['subject'=>'Computer Studies', 'icon'=>'💻','ca'=>26,'exam'=>61,'total'=>87,'grade'=>'A1','pos'=>1,'avg'=>70.3,'highest'=>90,'lowest'=>45,'remark'=>'Outstanding! Best in class.'],
    ['subject'=>'Literature',       'icon'=>'📖','ca'=>20,'exam'=>49,'total'=>69,'grade'=>'B3','pos'=>8,'avg'=>60.0,'highest'=>77,'lowest'=>36,'remark'=>'Good. Read more widely.'],
];

$summary = [
    'total_obtainable' => 1000,
    'total_obtained'   => 727,
    'percentage'       => 72.7,
    'arm_position'     => 4,
    'class_position'   => 9,
    'arm_total'        => 32,
    'class_total'      => 96,
    'no_passed'        => 10,
    'no_failed'        => 0,
    'days_present'     => 60,
    'days_absent'      => 7,
    'total_days'       => 67,
    'published'        => true,
    'form_teacher_remark' => 'Adaeze is a diligent and focused student. She shows great potential, especially in the Sciences. I encourage her to pay more attention to Chemistry and Further Mathematics. Overall, a commendable term.',
    'principal_remark' => 'Excellent performance this term. We are proud of your progress. Keep striving for the top.',
];
@endphp

{{-- ── SESSION/TERM FILTER ── --}}
<div class="card" style="margin-bottom:18px">
    <div class="card-body-sm" style="display:flex;flex-wrap:wrap;align-items:center;gap:12px">
        <span class="text-sm fw-semi">Filter:</span>
        @foreach($sessions as $sess)
            @foreach($sess['terms'] as $term)
            <a href="?session={{ $sess['label'] }}&term={{ $term }}"
               style="padding:5px 13px;border-radius:100px;font-size:.78rem;font-weight:600;border:1.5px solid {{ ($currentSession===$sess['label'] && $currentTerm===$term) ? 'var(--primary)' : 'var(--border)' }};background:{{ ($currentSession===$sess['label'] && $currentTerm===$term) ? 'var(--primary)' : 'white' }};color:{{ ($currentSession===$sess['label'] && $currentTerm===$term) ? 'white' : 'var(--text-mid)' }};transition:all .2s">
                {{ $sess['label'] }} {{ $term }}
            </a>
            @endforeach
        @endforeach
    </div>
</div>

@if($summary['published'])

{{-- ── SUMMARY CARDS ── --}}
<div class="g4" style="margin-bottom:18px">
    <div class="stat-card">
        <div class="stat-ic ic-green">🏆</div>
        <div>
            <div class="stat-lbl">Total Score</div>
            <div class="stat-val green">{{ $summary['total_obtained'] }}/{{ $summary['total_obtainable'] }}</div>
            <div class="stat-meta up">{{ $summary['percentage'] }}%</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ic ic-gold">🥇</div>
        <div>
            <div class="stat-lbl">Position in Arm</div>
            <div class="stat-val gold">{{ $summary['arm_position'] }}{{ $summary['arm_position']===1?'st':($summary['arm_position']===2?'nd':($summary['arm_position']===3?'rd':'th')) }}</div>
            <div class="stat-meta">Out of {{ $summary['arm_total'] }} students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ic ic-blue">📊</div>
        <div>
            <div class="stat-lbl">Class Position</div>
            <div class="stat-val">{{ $summary['class_position'] }}{{ $summary['class_position']===1?'st':($summary['class_position']===2?'nd':($summary['class_position']===3?'rd':'th')) }}</div>
            <div class="stat-meta">Out of {{ $summary['class_total'] }} students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ic ic-green">✅</div>
        <div>
            <div class="stat-lbl">Subjects Passed</div>
            <div class="stat-val green">{{ $summary['no_passed'] }}/{{ count($results) }}</div>
            <div class="stat-meta up">{{ $summary['no_failed'] }} failed</div>
        </div>
    </div>
</div>

<div class="gcontent">
    <div>
        {{-- ── RESULTS TABLE ── --}}
        <div class="card" style="margin-bottom:18px">
            <div class="card-header">
                <div>
                    <div class="card-title">Subject Results</div>
                    <div class="card-subtitle">{{ $currentTerm }} · {{ $currentSession }}</div>
                </div>
                {{-- Download report card button -- only shows when published --}}
                <a href="{{ route('student.results.report-card') }}?term=1" class="btn btn-primary btn-sm" download>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Download Report Card
                </a>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>CA /30</th>
                            <th>Exam /70</th>
                            <th>Total /100</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Class Avg</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $i => $r)
                        <tr>
                            <td class="text-muted">{{ $i+1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-size:1rem">{{ $r['icon'] }}</span>
                                    <span class="fw-semi">{{ $r['subject'] }}</span>
                                </div>
                            </td>
                            <td>{{ $r['ca'] }}</td>
                            <td>{{ $r['exam'] }}</td>
                            <td>
                                <div class="score-bar">
                                    <span class="fw-bold">{{ $r['total'] }}</span>
                                    <div class="score-track"><div class="score-fill" data-score="{{ $r['total'] }}"></div></div>
                                </div>
                            </td>
                            <td>
                                @php
                                $gc = match(true){
                                    in_array($r['grade'],['A1'])=>'g-a1',
                                    in_array($r['grade'],['B2','B3'])=>'g-b2',
                                    in_array($r['grade'],['C4','C5','C6'])=>'g-c4',
                                    in_array($r['grade'],['D7','E8'])=>'g-d7',
                                    default=>'g-f9'
                                };
                                @endphp
                                <span class="grade {{ $gc }}">{{ $r['grade'] }}</span>
                            </td>
                            <td>
                                <span class="pos-badge {{ $r['pos']===1?'p1':($r['pos']<=3?'p2':'pn') }}">
                                    {{ $r['pos'] }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $r['avg'] }}</td>
                            <td style="font-size:.76rem;color:var(--text-muted);max-width:200px">{{ Str::limit($r['remark'],40) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--primary-pale)">
                            <td colspan="4" class="fw-bold" style="color:var(--primary)">TOTAL</td>
                            <td class="fw-bold" style="color:var(--primary)">
                                {{ $summary['total_obtained'] }}/{{ $summary['total_obtainable'] }}
                                ({{ $summary['percentage'] }}%)
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Grading Key --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Grading Key</div></div>
            <div class="card-body">
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach([['A1','75–100','Excellent','g-a1'],['B2','70–74','Very Good','g-b2'],['B3','65–69','Good','g-b2'],['C4','60–64','Credit','g-c4'],['C5','55–59','Credit','g-c4'],['C6','50–54','Credit','g-c4'],['D7','45–49','Pass','g-d7'],['E8','40–44','Pass','g-d7'],['F9','0–39','Fail','g-f9']] as $gk)
                    <div style="display:flex;align-items:center;gap:7px;padding:6px 11px;border:1px solid var(--border);border-radius:8px;font-size:.76rem">
                        <span class="grade {{ $gk[3] }}" style="width:28px;height:28px;font-size:.7rem">{{ $gk[0] }}</span>
                        <span class="fw-semi">{{ $gk[1] }}</span>
                        <span class="text-muted">{{ $gk[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Attendance Summary --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Attendance Summary</div></div>
            <div class="card-body" style="display:flex;align-items:center;gap:16px">
                <div class="att-ring" data-percent="{{ round(($summary['days_present']/$summary['total_days'])*100) }}">
                    <svg width="76" height="76" viewBox="0 0 76 76">
                        <circle class="att-ring-bg" cx="38" cy="38" r="30"/>
                        <circle class="att-ring-fill" cx="38" cy="38" r="30"/>
                    </svg>
                    <div class="att-ring-txt">{{ round(($summary['days_present']/$summary['total_days'])*100) }}%</div>
                </div>
                <div>
                    <div class="text-sm fw-semi">{{ $summary['days_present'] }} of {{ $summary['total_days'] }} days</div>
                    <div class="text-xs text-muted mt-1">{{ $summary['days_absent'] }} absent</div>
                </div>
            </div>
        </div>

        {{-- Remarks --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Teacher Remarks</div></div>
            <div class="card-body">
                <div style="margin-bottom:16px">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--primary);margin-bottom:7px">Form Teacher</div>
                    <div style="font-size:.83rem;color:var(--text-mid);line-height:1.65;font-style:italic;padding:10px 13px;background:var(--primary-pale);border-left:3px solid var(--primary);border-radius:0 8px 8px 0">
                        "{{ $summary['form_teacher_remark'] }}"
                    </div>
                </div>
                <div>
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--gold-dark);margin-bottom:7px">Principal</div>
                    <div style="font-size:.83rem;color:var(--text-mid);line-height:1.65;font-style:italic;padding:10px 13px;background:var(--gold-light);border-left:3px solid var(--gold);border-radius:0 8px 8px 0">
                        "{{ $summary['principal_remark'] }}"
                    </div>
                </div>
            </div>
        </div>

        {{-- Download Report Card --}}
        <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:var(--radius-lg);padding:22px;text-align:center;color:white">
            <div style="font-size:2rem;margin-bottom:10px">📄</div>
            <div style="font-weight:700;font-size:.95rem;margin-bottom:6px">Official Report Card</div>
            <div style="font-size:.77rem;color:rgba(255,255,255,.65);margin-bottom:16px">Download your official report card PDF for this term.</div>
            <a href="{{ route('student.results.report-card') }}?term=1" class="btn btn-gold btn-block">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF
            </a>
        </div>

    </div>
</div>

@else
{{-- Results not yet published --}}
<div class="card">
    <div class="card-body" style="padding:48px 20px">
        <div class="empty-state">
            <div class="ei">📋</div>
            <div style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:6px">Results Not Yet Published</div>
            <p>Results for {{ $currentTerm }} · {{ $currentSession }} have not been published yet.<br>Please check back later or contact your Form Teacher.</p>
        </div>
    </div>
</div>
@endif

@endsection
