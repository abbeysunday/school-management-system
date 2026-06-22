@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Question Bank</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fe fe-upload me-2"></i>Bulk Import
                        </button>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.questions.create') }}">
                            <i class="fa fa-plus me-2"></i>Add Question
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <select name="subject_id" class="form-control select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}" {{ request('subject_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="class_level_id" class="form-control select">
                            <option value="">All Levels</option>
                            @foreach($levels as $id => $name)
                                <option value="{{ $id }}" {{ request('class_level_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="difficulty" class="form-control select">
                            <option value="">All Difficulty</option>
                            <option value="Easy" {{ request('difficulty')=='Easy'?'selected':'' }}>Easy</option>
                            <option value="Medium" {{ request('difficulty')=='Medium'?'selected':'' }}>Medium</option>
                            <option value="Hard" {{ request('difficulty')=='Hard'?'selected':'' }}>Hard</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Subject</th>
                                <th>Level</th>
                                <th>Difficulty</th>
                                <th>Answer</th>
                                <th>Used</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $q)
                            <tr>
                                <td>{{ $loop->iteration + $questions->firstItem() - 1 }}</td>
                                <td>
                                    {{ Str::limit($q->question_text, 60) }}
                                    @if($q->image_path)
                                        <i class="fe fe-image text-primary ms-1" title="Has image"></i>
                                    @endif
                                </td>
                                <td>{{ $q->subject->name }}</td>
                                <td>{{ $q->classLevel?->name ?? 'Any' }}</td>
                                <td>
                                    <span class="badge bg-{{ $q->difficulty=='Easy'?'success':($q->difficulty=='Hard'?'danger':'warning') }}">
                                        {{ $q->difficulty }}
                                    </span>
                                </td>
                                <td><strong>{{ $q->correct_option }}</strong></td>
                                <td>{{ $q->usage_count }}x</td>
                                <td>    
                                    @if($q->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions d-flex">
                                        <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#previewModal"
                                            onclick="loadPreview({{ $q->id }})" title="Preview">
                                            <i class="fe fe-eye"></i>
                                        </button>
                                        <a class="btn delete-table me-2" href="{{ route('admin.questions.edit', $q) }}">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button class="btn delete-table"><i class="fe fe-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted">No questions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $questions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center text-muted">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Import Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-control select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                        <small class="text-muted">
                            Headers: question_text, option_a, option_b, option_c, option_d, correct_option, difficulty
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadPreview(id) {
    fetch(`/admin/questions/${id}/preview`)
        .then(r => r.text())
        .then(html => document.getElementById('previewContent').innerHTML = html);
}
</script>

@endsection
