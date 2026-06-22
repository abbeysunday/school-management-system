@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>CA Configuration</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <span class="badge {{ $currentTotal == $caWeight ? 'bg-success' : 'bg-danger' }}">
                            Total Active: {{ number_format($currentTotal, 2) }} / {{ $caWeight }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fe fe-info me-2"></i>
            Active CA components must sum to exactly <strong>{{ $caWeight }}%</strong> (set in School Profile).
            Exam weight is automatically <strong>{{ 100 - $caWeight }}%</strong>.
        </div>

        <form action="{{ route('admin.settings.ca-config.update') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Component Name</th>
                                    <th>Max Score</th>
                                    <th>Order</th>
                                    <th>Active</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($components as $component)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $component->component_name }}</strong>
                                        <input type="hidden" name="components[{{ $loop->index }}][id]" value="{{ $component->id }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="components[{{ $loop->index }}][max_score]"
                                               class="form-control" value="{{ old('components.'.$loop->index.'.max_score', $component->max_score) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="components[{{ $loop->index }}][order]"
                                               class="form-control" value="{{ old('components.'.$loop->index.'.order', $component->order) }}" required>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="hidden" name="components[{{ $loop->index }}][is_active]" value="0">
                                            <input class="form-check-input component-toggle" type="checkbox"
                                                   name="components[{{ $loop->index }}][is_active]" value="1"
                                                   data-score="{{ $component->max_score }}"
                                                   {{ old('components.'.$loop->index.'.is_active', $component->is_active) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        @if($component->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Active Total:</strong></td>
                                    <td colspan="4">
                                        <span id="liveTotal" class="fs-5 {{ $currentTotal == $caWeight ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($currentTotal, 2) }}
                                        </span>
                                        <span class="text-muted"> / {{ $caWeight }} required</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-path">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-cancel me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Configuration</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const caWeight = {{ $caWeight }};
    const toggles = document.querySelectorAll('.component-toggle');
    const liveTotal = document.getElementById('liveTotal');
    const saveBtn = document.getElementById('saveBtn');

    function calculateTotal() {
        let total = 0;
        toggles.forEach(toggle => {
            if (toggle.checked) {
                total += parseFloat(toggle.dataset.score) || 0;
            }
        });
        liveTotal.textContent = total.toFixed(2);
        if (Math.abs(total - caWeight) < 0.01) {
            liveTotal.classList.remove('text-danger');
            liveTotal.classList.add('text-success');
            saveBtn.disabled = false;
        } else {
            liveTotal.classList.remove('text-success');
            liveTotal.classList.add('text-danger');
            saveBtn.disabled = true;
        }
    }

    toggles.forEach(t => t.addEventListener('change', calculateTotal));
    calculateTotal();
})();
</script>

@endsection
