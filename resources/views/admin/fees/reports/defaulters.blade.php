@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Fee Defaulters</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-primary" href="{{ route('admin.sms.compose', ['target' => 'defaulters']) }}">
                            <i class="fe fe-message-square me-2"></i>SMS Reminder
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-info" href="{{ route('admin.email.compose', ['target' => 'defaulters']) }}">
                            <i class="fe fe-mail me-2"></i>Email Reminder
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-success" href="{{ route('admin.fees.defaulters.export', request()->query()) }}">
                            <i class="fe fe-download me-2"></i>Export Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-danger"><i class="fe fe-users text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ number_format($summary['total_students']) }}</h5>
                                <h6>Defaulters</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span><i class="fe fe-dollar-sign"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($summary['total_due'], 2) }}</h5>
                                <h6>Total Billed</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-success"><i class="fe fe-check-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($summary['total_paid'], 2) }}</h5>
                                <h6>Total Paid</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-warning"><i class="fe fe-alert-triangle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($summary['total_owed'], 2) }}</h5>
                                <h6>Outstanding</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fees.defaulters') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Class Arm</label>
                        <select name="class_arm_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classArms as $arm)
                                <option value="{{ $arm->id }}" {{ $classArmId == $arm->id ? 'selected' : '' }}>
                                    {{ $arm->classLevel->name }}{{ $arm->arm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Min Balance</label>
                        <input type="number" name="min_balance" class="form-control" value="{{ $minBalance }}" min="1">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fe fe-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.fees.defaulters') }}" class="btn btn-outline-secondary w-100">
                            <i class="fe fe-refresh-cw me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @foreach($byClassArm as $className => $classDefaulters)
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fe fe-users me-2"></i>{{ $className }}
                        <span class="badge bg-danger ms-2">{{ count($classDefaulters) }} students</span>
                    </h5>
                    <span class="fw-bold text-danger">₦{{ number_format($classDefaulters->sum('balance'), 2) }} owed</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table datatable mb-0">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Student</th>
                                    <th>Adm. No</th>
                                    <th class="text-end">Total Billed</th>
                                    <th class="text-end">Total Paid</th>
                                    <th class="text-end">Balance</th>
                                    <th>Last Payment</th>
                                    <th>Contact</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classDefaulters as $i => $d)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php $student = \App\Models\Student::find($d['id']); @endphp
                                                <img src="{{ $student?->user?->photo_url ?? asset('images/default-avatar.png') }}" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                                                <div class="fw-semibold">{{ $d['name'] }}</div>
                                            </div>
                                        </td>
                                        <td class="font-monospace">{{ $d['admission_no'] }}</td>
                                        <td class="text-end">₦{{ number_format($d['total_due'], 2) }}</td>
                                        <td class="text-end text-success">₦{{ number_format($d['total_paid'], 2) }}</td>
                                        <td class="text-end fw-bold text-danger">₦{{ number_format($d['balance'], 2) }}</td>
                                        <td>
                                            <span class="text-muted">{{ $d['last_payment'] }}</span>
                                            @if($d['last_payment_amt'] > 0)
                                                <div class="text-success" style="font-size:11px;">₦{{ number_format($d['last_payment_amt'], 2) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($d['parent_phone'])
                                                <div class="text-muted" style="font-size:11px;"><i class="fe fe-phone me-1"></i>{{ $d['parent_phone'] }}</div>
                                            @endif
                                            @if($d['parent_email'])
                                                <div class="text-muted" style="font-size:11px;"><i class="fe fe-mail me-1"></i>{{ $d['parent_email'] }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.payments.create', ['student_id' => $d['id']]) }}" class="btn btn-sm btn-success" title="Record Payment">
                                                <i class="fe fe-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        @if(count($defaulters) === 0)
            <div class="card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fe fe-check-circle text-success" style="font-size:48px;"></i>
                    <h5 class="mt-3">No Defaulters Found!</h5>
                    <p class="text-muted">All students have settled their fees for {{ $term->name }}.</p>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
