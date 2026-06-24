<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Summary — {{ $term->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; line-height: 1.5; color: #333; }
        .report { padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #1a5f2a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { font-size: 16px; color: #1a5f2a; }
        .header p { font-size: 9px; color: #666; }
        .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
        .summary-row { display: table-row; }
        .summary-cell { display: table-cell; width: 25%; padding: 10px; text-align: center; border: 1px solid #e0e0e0; }
        .summary-label { font-size: 8px; text-transform: uppercase; color: #888; }
        .summary-value { font-size: 14px; font-weight: bold; color: #1a5f2a; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #1a5f2a; color: #fff; font-size: 9px; text-transform: uppercase; padding: 6px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #e0e0e0; font-size: 10px; }
        td.amount { text-align: right; font-family: monospace; }
        .section-title { font-size: 12px; font-weight: bold; color: #1a5f2a; margin: 20px 0 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="report">
        <div class="header">
            <h2>{{ $school?->name ?? 'School' }}</h2>
            <p>Financial Summary Report — {{ $term->name }} ({{ $term->session?->name }})</p>
            <p>Generated on {{ now()->format('d F Y') }}</p>
        </div>

        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Total Billed</div>
                    <div class="summary-value">₦{{ number_format($overall->total_billed ?? 0, 2) }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Collected</div>
                    <div class="summary-value">₦{{ number_format($overall->total_collected ?? 0, 2) }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Outstanding</div>
                    <div class="summary-value">₦{{ number_format($overall->total_outstanding ?? 0, 2) }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Collection Rate</div>
                    <div class="summary-value">{{ $overall->total_billed > 0 ? round(($overall->total_collected / $overall->total_billed) * 100, 1) : 0 }}%</div>
                </div>
            </div>
        </div>

        <div class="section-title">By Fee Category</div>
        <table>
            <thead><tr><th>Category</th><th class="text-right">Billed</th><th class="text-right">Collected</th><th class="text-right">Outstanding</th><th class="text-right">%</th></tr></thead>
            <tbody>
                @foreach($byCategory as $cat)
                    @php $rate = $cat->total_billed > 0 ? round(($cat->total_collected / $cat->total_billed) * 100, 1) : 0; @endphp
                    <tr>
                        <td>{{ $cat->category }}</td>
                        <td class="amount">₦{{ number_format($cat->total_billed, 2) }}</td>
                        <td class="amount">₦{{ number_format($cat->total_collected, 2) }}</td>
                        <td class="amount">₦{{ number_format($cat->total_outstanding, 2) }}</td>
                        <td class="amount">{{ $rate }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">By Class Level</div>
        <table>
            <thead><tr><th>Class Level</th><th class="text-right">Billed</th><th class="text-right">Collected</th><th class="text-right">Outstanding</th><th class="text-right">%</th></tr></thead>
            <tbody>
                @foreach($byClassLevel as $cl)
                    @php $rate = $cl->total_billed > 0 ? round(($cl->total_collected / $cl->total_billed) * 100, 1) : 0; @endphp
                    <tr>
                        <td>{{ $cl->class_level }}</td>
                        <td class="amount">₦{{ number_format($cl->total_billed, 2) }}</td>
                        <td class="amount">₦{{ number_format($cl->total_collected, 2) }}</td>
                        <td class="amount">₦{{ number_format($cl->total_outstanding, 2) }}</td>
                        <td class="amount">{{ $rate }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">By Payment Method</div>
        <table>
            <thead><tr><th>Method</th><th class="text-right">Transactions</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @foreach($byMethod as $m)
                    <tr><td>{{ $m->payment_method }}</td><td class="amount">{{ number_format($m->count) }}</td><td class="amount">₦{{ number_format($m->total, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>This is a computer-generated report. {{ $school?->name ?? '' }} · {{ $school?->phone ?? '' }}</p>
        </div>
    </div>
</body>
</html>
