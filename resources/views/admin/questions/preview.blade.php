<div class="p-3">
    <div class="mb-3">
        <span class="badge bg-secondary">{{ $question->subject->name }}</span>
        <span class="badge bg-{{ $question->difficulty=='Easy'?'success':($question->difficulty=='Hard'?'danger':'warning') }}">{{ $question->difficulty }}</span>
    </div>

    <h6 class="mb-3">{{ $question->question_text }}</h6>

    @if($question->image_path)
        <div class="mb-3">
            <img src="{{ asset('storage/'.$question->image_path) }}" class="img-fluid rounded" style="max-height:300px;" alt="Question image">
        </div>
    @endif

    <div class="list-group mb-3">
        <div class="list-group-item d-flex justify-content-between align-items-center {{ $question->correct_option=='A'?'list-group-item-success':'' }}">
            <span><strong>A.</strong> {{ $question->option_a }}</span>
            @if($question->correct_option=='A')<span class="badge bg-success">Correct</span>@endif
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center {{ $question->correct_option=='B'?'list-group-item-success':'' }}">
            <span><strong>B.</strong> {{ $question->option_b }}</span>
            @if($question->correct_option=='B')<span class="badge bg-success">Correct</span>@endif
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center {{ $question->correct_option=='C'?'list-group-item-success':'' }}">
            <span><strong>C.</strong> {{ $question->option_c }}</span>
            @if($question->correct_option=='C')<span class="badge bg-success">Correct</span>@endif
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center {{ $question->correct_option=='D'?'list-group-item-success':'' }}">
            <span><strong>D.</strong> {{ $question->option_d }}</span>
            @if($question->correct_option=='D')<span class="badge bg-success">Correct</span>@endif
        </div>
    </div>

    @if($question->explanation)
        <div class="alert alert-info">
            <strong>Explanation:</strong> {{ $question->explanation }}
        </div>
    @endif
</div>  
