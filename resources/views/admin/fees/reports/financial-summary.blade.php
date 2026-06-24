@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper page-settings">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Financial Summary</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-success" href="{{ route('admin.fees.financial-summary.pdf') }}" target="_blank">
                            <i class="fe fe-printer me-2"></i>Export PDF
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
                            <div class="dash-widgetimg"><span><i class="fe fe-file-text"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($overall->total_billed ?? 0, 2) }}</h5>
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
                                <h5>₦{{ number_format($overall->total_collected ?? 0, 2) }}</h5>
                                <h6>Total Collected</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-warning"><i class="fe fe-alert-circle text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>₦{{ number_format($overall->total_outstanding ?? 0, 2) }}</h5>
                                <h6>Outstanding</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="dash-widget">
                            <div class="dash-widgetimg"><span class="bg-info"><i class="fe fe-percent text-white"></i></span></div>
                            <div class="dash-widgetcontent">
                                <h5>{{ $overall->total_billed > 0 ? round(($overall->total_collected / $overall->total_billed) * 100, 1) : 0 }}%</h5>
                                <h6>Collection Rate</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card"><div class="card-body text-center">
                    <h3 class="text-success">{{ number_format($overall->paid_count ?? 0) }}</h3>
                    <p class="text-muted mb-0">Fully Paid Items</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-body text-center">
                    <h3 class="text-warning">{{ number_format($overall->partial_count ?? 0) }}</h3>
                    <p class="text-muted mb-0">Partially Paid</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-body text-center">
                    <h3 class="text-danger">{{ number_format($overall->unpaid_count ?? 0) }}</h3>
                    <p class="text-muted mb-0">Unpaid Items</p>
                </div></div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fe fe-list me-2"></i>By Fee Category</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead><tr>
                            <th>Category</th><th>Type</th><th class="text-end">Total Billed</th>
                            <th class="text-end">Collected</th><th class="text-end">Outstanding</th><th class="text-end">Collection %</th>
                        </tr></thead>
                        <tbody>
                            @foreach($byCategory as $cat)
                                @php $rate = $cat->total_billed > 0 ? round(($cat->total_collected / $cat->total_billed) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $cat->category }}</td>
                                    <td><span class="badge bg-{{ $cat->is_compulsory ? 'danger' : 'secondary' }}">{{ $cat->is_compulsory ? 'Compulsory' : 'Optional' }}</span></td>
                                    <td class="text-end">₦{{ number_format($cat->total_billed, 2) }}</td>
                                    <td class="text-end text-success">₦{{ number_format($cat->total_collected, 2) }}</td>
                                    <td class="text-end text-danger">₦{{ number_format($cat->total_outstanding, 2) }}</td>
                                    <td class="text-end">{{ $rate }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fe fe-layers me-2"></i>By Class Level</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead><tr>
                            <th>Class Level</th><th>Category</th><th class="text-end">Students</th>
                            <th class="text-end">Total Billed</th><th class="text-end">Collected</th><th class="text-end">Outstanding</th><th class="text-end">Collection %</th>
                        </tr></thead>
                        <tbody>
                            @foreach($byClassLevel as $cl)
                                @php $rate = $cl->total_billed > 0 ? round(($cl->total_collected / $cl->total_billed) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $cl->class_level }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $cl->category }}</span></td>
                                    <td class="text-end">{{ number_format($cl->student_count) }}</td>
                                    <td class="text-end">₦{{ number_format($cl->total_billed, 2) }}</td>
                                    <td class="text-end text-success">₦{{ number_format($cl->total_collected, 2) }}</td>
                                    <td class="text-end text-danger">₦{{ number_format($cl->total_outstanding, 2) }}</td>
                                    <td class="text-end">{{ $rate }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fe fe-credit-card me-2"></i>By Payment Method</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead><tr><th>Method</th><th class="text-end">Transactions</th><th class="text-end">Total Amount</th></tr></thead>
                        <tbody>
                            @foreach($byMethod as $m)
                                <tr>
                                    <td><span class="badge bg-{{ $m->payment_method == 'Paystack' ? 'info' : 'success' }}">{{ $m->payment_method }}</span></td>
                                    <td class="text-end">{{ number_format($m->count) }}</td>
                                    <td class="text-end fw-bold">₦{{ number_format($m->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
