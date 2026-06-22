@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="content-page-header content-page-headersplit mb-3">
            <div>
                <h5 class="mb-0">{{ $exam->title }}</h5>
                <small class="text-muted">
                    {{ $exam->subject->name }} &bull;
                    {{ $exam->classArm->classLevel->name ?? '' }} {{ $exam->classArm->name }} &bull;
                    {{ $exam->term->name ?? '' }}
                </small>
            </div>
            <div class="list-btn">
                <ul>
                    <li>
                        <a href="{{ route('admin.cbt.exams.edit', $exam) }}" class="btn btn-light">
                            <i class="fe fe-edit me-1"></i> Edit Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cbt.exams.index') }}" class="btn btn-secondary">
                            <i class="fe fe-arrow-left me-1"></i> All Exams
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">

            {{-- Left: Exam info + attached questions --}}
            <div class="col-lg-8">

                {{-- Status bar --}}
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="row text-center">
                            <div class="col-3 border-end">
                                <small class="text-muted d-block">Status</small>
                                @php
                                    $badge = match($exam->status) {
                                        'Active'    => 'bg-success',
                                        'Scheduled' => 'bg-info',
                                        'Completed' => 'bg-secondary',
                                        'Cancelled' => 'bg-danger',
                                        default     => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} fs-6">{{ $exam->status }}</span>
                            </div>
                            <div class="col-3 border-end">
                                <small class="text-muted d-block">Questions</small>
                                <strong class="{{ $exam->questions->count() >= $exam->total_questions ? 'text-success' : 'text-warning' }}">
                                    {{ $exam->questions->count() }} / {{ $exam->total_questions }}
                                </strong>
                            </div>
                            <div class="col-3 border-end">
                                <small class="text-muted d-block">Duration</small>
                                <strong>{{ $exam->duration_minutes }} min</strong>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Total Marks</small>
                                <strong>{{ $exam->total_marks }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Attached Questions --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Questions in this Exam</h6>
                        @if($exam->questions->count() >= $exam->total_questions)
                            <span class="badge bg-success">Ready</span>
                        @else
                            <span class="badge bg-warning text-dark">
                                Need {{ $exam->total_questions - $exam->questions->count() }} more
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if($exam->questions->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fe fe-inbox" style="font-size:2rem;"></i>
                                <p class="mt-2">No questions added yet. Use the panel on the right to add questions.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Question</th>
                                        <th>Difficulty</th>
                                        <th>Answer</th>
                                        <th width="60"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->questions as $q)
                                    <tr>
                                        <td class="text-muted">{{ $q->pivot->question_order }}</td>
                                        <td>{{ Str::limit($q->question_text, 80) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $q->difficulty=='Easy'?'success':($q->difficulty=='Hard'?'danger':'warning') }}">
                                                {{ $q->difficulty }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $q->correct_option }}</strong></td>
                                        <td>
                                            <form action="{{ route('admin.cbt.exams.questions.detach', [$exam, $q]) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Remove question from exam?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Remove">
                                                    <i class="fe fe-x"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Question Selector --}}
            <div class="col-lg-4">

                {{-- Auto-Select --}}
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="fe fe-zap me-1"></i>Auto-Select Questions</h6></div>
                    <div class="card-body">
                        <form action="{{ route('admin.cbt.exams.auto-select', $exam) }}" method="POST">
                            @csrf
                            <p class="text-muted small mb-3">
                                Randomly picks from the question bank for <strong>{{ $exam->subject->name }}</strong>.
                                Available: Easy {{ $diffCounts['Easy'] }}, Medium {{ $diffCounts['Medium'] }}, Hard {{ $diffCounts['Hard'] }}.
                            </p>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <label class="form-label small mb-1 text-success">Easy</label>
                                    <input type="number" name="easy_count" class="form-control form-control-sm"
                                           value="0" min="0" max="{{ $diffCounts['Easy'] }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1 text-warning">Medium</label>
                                    <input type="number" name="medium_count" class="form-control form-control-sm"
                                           value="0" min="0" max="{{ $diffCounts['Medium'] }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1 text-danger">Hard</label>
                                    <input type="number" name="hard_count" class="form-control form-control-sm"
                                           value="0" min="0" max="{{ $diffCounts['Hard'] }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fe fe-zap me-1"></i> Auto-Select
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Manual Selection --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fe fe-list me-1"></i>Manual Selection</h6>
                    </div>
                    <div class="card-body">
                        @if($available->isEmpty())
                            <p class="text-muted small text-center mb-0">
                                No more questions available for this subject in the question bank.
                                <a href="{{ route('admin.questions.create') }}">Add questions</a> first.
                            </p>
                        @else
                        {{-- Difficulty filter tabs --}}
                        <div class="btn-group w-100 mb-3" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filterDiff('all', this)">All ({{ $available->count() }})</button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="filterDiff('Easy', this)">Easy ({{ $diffCounts['Easy'] }})</button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterDiff('Medium', this)">Med ({{ $diffCounts['Medium'] }})</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterDiff('Hard', this)">Hard ({{ $diffCounts['Hard'] }})</button>
                        </div>

                        <form action="{{ route('admin.cbt.exams.questions.attach', $exam) }}" method="POST" id="attach-form">
                            @csrf
                            <div style="max-height:380px; overflow-y:auto;" class="border rounded p-2 mb-3" id="q-list">
                                @foreach($available as $q)
                                <div class="form-check mb-2 q-item" data-diff="{{ $q->difficulty }}">
                                    <input class="form-check-input q-checkbox" type="checkbox"
                                           name="question_ids[]" value="{{ $q->id }}" id="q{{ $q->id }}">
                                    <label class="form-check-label small" for="q{{ $q->id }}">
                                        <span class="badge bg-{{ $q->difficulty=='Easy'?'success':($q->difficulty=='Hard'?'danger':'warning') }} me-1" style="font-size:0.65rem;">{{ $q->difficulty }}</span>
                                        {{ Str::limit($q->question_text, 60) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="selectAll(true)">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(false)">None</button>
                                </div>
                                <small class="text-muted"><span id="sel-count">0</span> selected</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fe fe-plus me-1"></i> Add Selected
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function filterDiff(diff, btn) {
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.q-item').forEach(el => {
        el.style.display = (diff === 'all' || el.dataset.diff === diff) ? '' : 'none';
    });
}

function selectAll(state) {
    document.querySelectorAll('.q-item:not([style*="display: none"]) .q-checkbox').forEach(cb => cb.checked = state);
    updateCount();
}

function updateCount() {
    document.getElementById('sel-count').textContent = document.querySelectorAll('.q-checkbox:checked').length;
}

document.querySelectorAll('.q-checkbox').forEach(cb => cb.addEventListener('change', updateCount));

document.getElementById('attach-form')?.addEventListener('submit', function(e) {
    if (!document.querySelector('.q-checkbox:checked')) {
        e.preventDefault();
        alert('Select at least one question to add.');
    }
});
</script>

@endsection
