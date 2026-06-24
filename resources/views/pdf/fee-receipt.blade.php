<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 210mm;
            min-height: 297mm;
            padding: 25mm 20mm;
            margin: 0 auto;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .school-address {
            font-size: 12px;
            color: #555;
            line-height: 1.5;
        }
        .receipt-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 25px 0 15px;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .receipt-number {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 25px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-grid td {
            padding: 6px 0;
            vertical-align: top;
        }
        .info-grid .label {
            font-weight: bold;
            width: 35%;
            color: #555;
        }
        .info-grid .value {
            width: 65%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .items-table .text-end {
            text-align: right;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 35px;
        }
        .summary-table td {
            padding: 8px 0;
        }
        .summary-table .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .summary-table .text-end {
            text-align: right;
        }
        .overpayment-box {
            background-color: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 25px;
        }
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }
        .signature-section td {
            width: 50%;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 80%;
            padding-top: 5px;
            margin-top: 40px;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
    </style>
</head>
<body>
    <div class="receipt">
        {{-- School Header --}}
        <div class="header">
            <div class="school-name">{{ $school?->name ?? 'School Name' }}</div>
            <div class="school-address">
                @if($school?->address)
                    {{ $school->address }}<br>
                @endif
                @if($school?->phone || $school?->email)
                    Phone: {{ $school?->phone ?? 'N/A' }} | Email: {{ $school?->email ?? 'N/A' }}<br>
                @endif
                @if($school?->motto)
                    <em>"{{ $school->motto }}"</em>
                @endif
            </div>
        </div>

        <div class="receipt-title">Fee Payment Receipt</div>
        <div class="receipt-number">Receipt No: {{ $payment->receipt_number }}</div>

        {{-- Student & Payment Info --}}
        @php
            $student = $payment->student;
            $enrollment = $student?->currentEnrollment;
            $classArm = $enrollment?->classArm;
            $allocatedTotal = $payment->allocations->sum('amount_allocated');
            $overpayment = max(0, $payment->amount - $allocatedTotal);
        @endphp

        <table class="info-grid">
            <tr>
                <td class="label">Student Name:</td>
                <td class="value">{{ $student?->user?->full_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Admission Number:</td>
                <td class="value">{{ $student?->admission_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Class / Arm:</td>
                <td class="value">{{ $classArm?->classLevel?->name ?? '' }} {{ $classArm?->arm ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Academic Term:</td>
                <td class="value">{{ $payment->term?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Payment Date:</td>
                <td class="value">{{ $payment->paid_at?->format('F j, Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Payment Method:</td>
                <td class="value">{{ $payment->payment_method }}</td>
            </tr>
            <tr>
                <td class="label">Payment Status:</td>
                <td class="value"><span class="badge badge-success">{{ $payment->status }}</span></td>
            </tr>
            <tr>
                <td class="label">Recorded By:</td>
                <td class="value">{{ $payment->verifiedBy?->full_name ?? 'System' }}</td>
            </tr>
            @if($payment->notes)
                <tr>
                    <td class="label">Notes:</td>
                    <td class="value">{{ $payment->notes }}</td>
                </tr>
            @endif
        </table>

        {{-- Allocated Fee Items --}}
        <h4 style="margin-bottom: 10px; color: #1e3a8a;">Fee Items Paid</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fee Category</th>
                    <th>Description</th>
                    <th class="text-end">Amount Allocated (₦)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payment->allocations as $index => $allocation)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $allocation->ledger?->feeStructure?->feeCategory?->name ?? 'N/A' }}</td>
                        <td>{{ $allocation->ledger?->feeStructure?->feeCategory?->description ?? '—' }}</td>
                        <td class="text-end">{{ number_format($allocation->amount_allocated, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No specific fee items were allocated.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Summary --}}
        <table class="summary-table">
            <tr>
                <td>Total Allocated to Fees</td>
                <td class="text-end">₦{{ number_format($allocatedTotal, 2) }}</td>
            </tr>
            @if($overpayment > 0)
                <tr>
                    <td>Overpayment (Unallocated)</td>
                    <td class="text-end">₦{{ number_format($overpayment, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Payment Received</td>
                <td class="text-end">₦{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </table>

        @if($overpayment > 0)
            <div class="overpayment-box">
                <strong>Overpayment Notice:</strong>
                ₦{{ number_format($overpayment, 2) }} could not be allocated to any outstanding fee item.
                This amount may be applied as credit against future fees.
            </div>
        @endif

        {{-- Signature --}}
        <table class="signature-section">
            <tr>
                <td>
                    <div class="signature-line">
                        <strong>Bursar / Accountant Signature</strong><br>
                        <small>Name: _________________________</small>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        <strong>Date</strong><br>
                        <small>{{ now()->format('F j, Y') }}</small>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            This is an official receipt of {{ $school?->name ?? 'the school' }}.<br>
            For enquiries, please contact the bursar's office.
        </div>
    </div>
</body>
</html>
