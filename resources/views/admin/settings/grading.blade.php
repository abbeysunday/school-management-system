@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Grading Scale</h5>
            <div class="list-btn">
                <ul>
                    <li><span class="badge bg-info text-dark">A1 = Excellent • F9 = Fail</span></li>
                </ul>
            </div>
        </div>

        <form action="{{ route('admin.settings.grading.update') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Grade</th>
                                    <th>Min Score (%)</th>
                                    <th>Max Score (%)</th>
                                    <th>Remark</th>
                                    <th>Pass?</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scales as $scale)
                                <tr>
                                    <td>
                                        <strong class="fs-5">{{ $scale->grade }}</strong>
                                        <input type="hidden" name="scales[{{ $loop->index }}][id]" value="{{ $scale->id }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="scales[{{ $loop->index }}][min_score]"
                                               class="form-control" value="{{ old('scales.'.$loop->index.'.min_score', $scale->min_score) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="scales[{{ $loop->index }}][max_score]"
                                               class="form-control" value="{{ old('scales.'.$loop->index.'.max_score', $scale->max_score) }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="scales[{{ $loop->index }}][remark]"
                                               class="form-control" value="{{ old('scales.'.$loop->index.'.remark', $scale->remark) }}" required>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="hidden" name="scales[{{ $loop->index }}][is_pass]" value="0">
                                            <input class="form-check-input" type="checkbox" name="scales[{{ $loop->index }}][is_pass]" value="1"
                                                {{ old('scales.'.$loop->index.'.is_pass', $scale->is_pass) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        @if($scale->is_pass)
                                            <span class="badge bg-success">{{ $scale->remark }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $scale->remark }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-cancel me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Grading Scale</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
