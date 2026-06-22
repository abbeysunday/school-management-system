@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.questions.store') }}" method="POST" id="bulk-question-form">
            @csrf
            <div class="content-page-header content-page-headersplit mb-4">
                <h5>Add Questions to Bank</h5>
                <div class="list-btn">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fa fa-save me-1"></i> Save <span id="q-count">1</span> Question(s)
                    </button>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Shared Header --}}
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0">Shared Settings (applies to all questions below)</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" class="form-control select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">— Select Subject —</option>
                                    @foreach($subjects as $id => $name)
                                        <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Class Level <small class="text-muted">(optional)</small></label>
                                <select name="class_level_id" class="form-control select">
                                    <option value="">Any / All Levels</option>
                                    @foreach($levels as $id => $name)
                                        <option value="{{ $id }}" {{ old('class_level_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Question Rows --}}
            <div id="questions-container"></div>

            {{-- Add Row Button --}}
            <div class="text-center mb-4">
                <button type="button" class="btn btn-outline-primary" id="add-row-btn">
                    <i class="fe fe-plus me-1"></i> Add Another Question
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 0;
const OLD = @json(old('questions', []));

function createRow(defaults = {}) {
    const idx = rowIndex++;
    const num = idx + 1;

    const opts = ['A','B','C','D'];
    const diffs = ['Easy','Medium','Hard'];

    const optionInputs = opts.map(o => `
        <div class="col-lg-6">
            <div class="input-group mb-2">
                <span class="input-group-text fw-bold">${o}</span>
                <input type="text" name="questions[${idx}][option_${o.toLowerCase()}]"
                    class="form-control"
                    placeholder="Option ${o}"
                    value="${escHtml(defaults['option_' + o.toLowerCase()] ?? '')}"
                    required>
            </div>
        </div>`).join('');

    const correctOpts = opts.map(o =>
        `<option value="${o}" ${(defaults.correct_option ?? '') === o ? 'selected' : ''}>${o}</option>`
    ).join('');

    const diffOpts = diffs.map(d =>
        `<option value="${d}" ${(defaults.difficulty ?? 'Medium') === d ? 'selected' : ''}>${d}</option>`
    ).join('');

    const html = `
    <div class="card mb-3 question-row" id="row-${idx}" data-idx="${idx}">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 text-primary">Question <span class="row-num">${num}</span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" data-idx="${idx}">
                <i class="fe fe-trash-2"></i> Remove
            </button>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Question Text <span class="text-danger">*</span></label>
                <textarea name="questions[${idx}][question_text]" rows="2"
                    class="form-control" placeholder="Enter question..." required>${escHtml(defaults.question_text ?? '')}</textarea>
            </div>
            <div class="row">${optionInputs}</div>
            <div class="row mt-2">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Correct Answer <span class="text-danger">*</span></label>
                        <select name="questions[${idx}][correct_option]" class="form-control" required>
                            <option value="">Select</option>${correctOpts}
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Difficulty</label>
                        <select name="questions[${idx}][difficulty]" class="form-control">
                            ${diffOpts}
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Explanation <small class="text-muted">(optional)</small></label>
                        <textarea name="questions[${idx}][explanation]" rows="1"
                            class="form-control" placeholder="Why is this the correct answer?">${escHtml(defaults.explanation ?? '')}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.getElementById('questions-container').insertAdjacentHTML('beforeend', html);
    updateRowNumbers();
    updateCount();
}

function removeRow(idx) {
    const el = document.getElementById(`row-${idx}`);
    if (el) el.remove();
    updateRowNumbers();
    updateCount();
}

function updateRowNumbers() {
    document.querySelectorAll('.question-row').forEach((row, i) => {
        row.querySelector('.row-num').textContent = i + 1;
    });
}

function updateCount() {
    const n = document.querySelectorAll('.question-row').length;
    document.getElementById('q-count').textContent = n;
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

document.getElementById('add-row-btn').addEventListener('click', () => createRow());

document.getElementById('questions-container').addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-row-btn');
    if (btn) {
        if (document.querySelectorAll('.question-row').length <= 1) {
            alert('You must have at least one question.');
            return;
        }
        removeRow(btn.dataset.idx);
    }
});

document.getElementById('bulk-question-form').addEventListener('submit', function(e) {
    if (document.querySelectorAll('.question-row').length === 0) {
        e.preventDefault();
        alert('Add at least one question before saving.');
    }
});

// Restore old values on validation failure, else start with one blank row
document.addEventListener('DOMContentLoaded', function() {
    if (OLD.length > 0) {
        OLD.forEach(q => createRow(q));
    } else {
        createRow();
    }
});
</script>

@endsection
