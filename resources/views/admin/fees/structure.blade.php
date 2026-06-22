@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Fee Structure</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-light" href="{{ route('admin.fees.categories') }}">
                            <i class="fe fe-list me-2"></i>Categories
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-0">
                            <label>Academic Session</label>
                            <select name="session_id" class="form-control select" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $id => $name)
                                    <option value="{{ $id }}" {{ $sessionId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-0">
                            <label>Term</label>
                            <select name="term_id" class="form-control select" required>
                                <option value="">Select Term</option>
                                @foreach($terms as $id => $name)
                                    <option value="{{ $id }}" {{ $termId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Load Grid</button>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#copyModal">
                            <i class="fe fe-copy me-1"></i>Copy Last Term
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($sessionId && $termId)
        <form action="{{ route('admin.fees.structure.store') }}" method="POST">
            @csrf
            <input type="hidden" name="session_id" value="{{ $sessionId }}">
            <input type="hidden" name="term_id" value="{{ $termId }}">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Fee Amounts Grid</h6>
                    <div>
                        <span class="badge bg-primary me-1">Rows = Fee Categories</span>
                        <span class="badge bg-secondary">Columns = Class Levels</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start" style="min-width:180px;">Fee Category</th>
                                    @foreach($classLevels as $level)
                                        <th style="min-width:120px;">{{ $level->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $cat)
                                <tr>
                                    <td class="text-start align-middle">
                                        <strong>{{ $cat->name }}</strong>
                                        @if(!$cat->is_compulsory)
                                            <span class="badge bg-secondary ms-1">Opt</span>
                                        @endif
                                    </td>
                                    @foreach($classLevels as $level)
                                    <td>
                                        <div class="form-group mb-1">
                                            <input type="number" step="0.01" min="0"
                                                   name="amounts[{{ $cat->id }}][{{ $level->id }}]"
                                                   class="form-control form-control-sm text-center"
                                                   placeholder="0.00"
                                                   value="{{ old('amounts.'.$cat->id.'.'.$level->id, $grid[$cat->id][$level->id] ?? '') }}">
                                        </div>
                                        <div class="form-group mb-0">
                                            <input type="date"
                                                   name="due_dates[{{ $cat->id }}][{{ $level->id }}]"
                                                   class="form-control form-control-sm text-center"
                                                   placeholder="Due"
                                                   value="{{ old('due_dates.'.$cat->id.'.'.$level->id, $dueDates[$cat->id][$level->id] ?? '') }}">
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="text-start"><strong>Total per Class</strong></td>
                                    @foreach($classLevels as $level)
                                    <td>
                                        <strong class="text-success" id="total-{{ $level->id }}">
                                            ₦{{ number_format(
                                                collect($categories)->sum(function($cat) use ($level, $grid) {
                                                    return $grid[$cat->id][$level->id] ?? 0;
                                                }), 2
                                            ) }}
                                        </strong>
                                    </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.fees.structure') }}" class="btn btn-cancel me-3">Reset</a>
                        <button type="submit" class="btn btn-primary">Save Fee Structure</button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="alert alert-info">
            <i class="fe fe-info me-2"></i> Select a <strong>Session</strong> and <strong>Term</strong> to view or edit the fee structure grid.
        </div>
        @endif
    </div>
</div>

<!-- Copy from Last Term Modal -->
<div class="modal fade" id="copyModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fees.structure.copy') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">This will copy all fee amounts from the most recent previous term into the selected term.</p>
                    <div class="form-group mb-3">
                        <label>Target Session <span class="text-danger">*</span></label>
                        <select name="target_session_id" class="form-control select" required>
                            @foreach($sessions as $id => $name)
                                <option value="{{ $id }}" {{ $sessionId == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Target Term <span class="text-danger">*</span></label>
                        <select name="target_term_id" class="form-control select" required>
                            @foreach($terms as $id => $name)
                                <option value="{{ $id }}" {{ $termId == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Copy from Last Term</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Live total calculation
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', calculateTotals);
});

function calculateTotals() {
    @foreach($classLevels as $level)
    let total{{ $level->id }} = 0;
    document.querySelectorAll('input[name^="amounts["][name$="[{{ $level->id }}]"]').forEach(el => {
        total{{ $level->id }} += parseFloat(el.value) || 0;
    });
    document.getElementById('total-{{ $level->id }}').textContent = '₦' + total{{ $level->id }}.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    @endforeach
}
</script>

@endsection
