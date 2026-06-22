@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="content-page-header">
                <h5>Edit Question</h5>
            </div>
            <div class="row">
                <div class="col-lg-8 m-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <select name="subject_id" class="form-control select @error('subject_id') is-invalid @enderror" required>
                                            <option value="">Select</option>
                                            @foreach($subjects as $id => $name)
                                                <option value="{{ $id }}" {{ old('subject_id', $question->subject_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Class Level</label>
                                        <select name="class_level_id" class="form-control select">
                                            <option value="">Any / All</option>
                                            @foreach($levels as $id => $name)
                                                <option value="{{ $id }}" {{ old('class_level_id', $question->class_level_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Question Text <span class="text-danger">*</span></label>
                                        <textarea name="question_text" rows="3" class="form-control @error('question_text') is-invalid @enderror" required>{{ old('question_text', $question->question_text) }}</textarea>
                                        @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Option A <span class="text-danger">*</span></label>
                                        <input type="text" name="option_a" class="form-control @error('option_a') is-invalid @enderror" value="{{ old('option_a', $question->option_a) }}" required>
                                        @error('option_a')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Option B <span class="text-danger">*</span></label>
                                        <input type="text" name="option_b" class="form-control @error('option_b') is-invalid @enderror" value="{{ old('option_b', $question->option_b) }}" required>
                                        @error('option_b')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Option C <span class="text-danger">*</span></label>
                                        <input type="text" name="option_c" class="form-control @error('option_c') is-invalid @enderror" value="{{ old('option_c', $question->option_c) }}" required>
                                        @error('option_c')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Option D <span class="text-danger">*</span></label>
                                        <input type="text" name="option_d" class="form-control @error('option_d') is-invalid @enderror" value="{{ old('option_d', $question->option_d) }}" required>
                                        @error('option_d')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Correct Option <span class="text-danger">*</span></label>
                                        <select name="correct_option" class="form-control select @error('correct_option') is-invalid @enderror" required>
                                            <option value="">Select</option>
                                            @foreach(['A','B','C','D'] as $opt)
                                                <option value="{{ $opt }}" {{ old('correct_option', $question->correct_option) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                        @error('correct_option')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Difficulty</label>
                                        <select name="difficulty" class="form-control select">
                                            @foreach(['Easy','Medium','Hard'] as $d)
                                                <option value="{{ $d }}" {{ old('difficulty', $question->difficulty) == $d ? 'selected' : '' }}>{{ $d }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Question Image</label>
                                        @if($question->image_path)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($question->image_path) }}" alt="Question image" class="img-thumbnail" style="max-height:80px;">
                                                <small class="d-block text-muted">Upload new to replace</small>
                                            </div>
                                        @endif
                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                        <small class="text-muted">Max 2MB. Resized to 800px.</small>
                                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Explanation (optional)</label>
                                        <textarea name="explanation" rows="2" class="form-control">{{ old('explanation', $question->explanation) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                            {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-path">
                                <a href="{{ route('admin.questions.index') }}" class="btn btn-cancel me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Question</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
