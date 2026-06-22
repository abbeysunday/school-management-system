@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Student Fee Ledger</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#generateModal">
                            <i class="fe fe-refresh-cw me-2"></i>Generate Ledgers
                        </button>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.fees.structure') }}">
                            <i class="fe fe-dollar-sign me-2"></i>Fee Structure
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
                        <select name="term_id" class="form-control select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $term?->id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->session->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="class_arm_id" class="form-control select">
                            <option value="">All Class Arms</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ request('class_arm_id')==$arm->id?'selected':'' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search name or admission no..." value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
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
                                <th>Student</th>
                                <th>Class</th>
                                <th class="text-end">Total Due</th>
                                <th class="text-end">Total Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                <td>
                                    <strong>{{ $student->user->full_name }}</strong><br>
                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                </td>
                                <td>{{ $student->currentEnrollment?->classArm?->full_name ?? 'N/A' }}</td>
                                <td class="text-end">₦{{ number_format($student->total_due, 2) }}</td>
                                <td class="text-end text-success">₦{{ number_format($student->total_paid, 2) }}</td>
                                <td class="text-end {{ $student->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    ₦{{ number_format($student->balance, 2) }}
                                </td>
                                <td>
                                    @if($student->balance <= 0)
                                        <span class="badge bg-success">Fully Paid</span>
                                    @elseif($student->total_paid > 0)
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.ledger.show', ['student' => $student, 'term_id' => $term?->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fe fe-eye me-1"></i>Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No students found.
                                    @if($term)
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#generateModal">Generate ledgers for this term</a>.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Generate Ledger Modal -->
<div class="modal fade" id="generateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Fee Ledgers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.ledger.generate.all') }}" method="POST" id="generateAllForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Generate for ALL students in term:</label>
                        <select name="term_id" class="form-control select" required>
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $term?->id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->session->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Generate All</button>
                </form>

                <hr class="my-4">

                <form action="{{ route('admin.ledger.generate.arm') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Or generate for specific class arm:</label>
                        <select name="class_arm_id" class="form-control select" required>
                            <option value="">Select Class Arm</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}">{{ $arm->classLevel->name }}{{ $arm->arm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="term_id" class="form-control select" required>
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $term?->id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->session->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info w-100">Generate for Class Arm</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
