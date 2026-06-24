<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Fee Receipt — {{ $payment->receipt_number }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
    .receipt { max-width: 700px; margin: 0 auto; padding: 40px; }
    .header { text-align: center; border-bottom: 2px solid #1a5f2a; padding-bottom: 20px; margin-bottom: 25px; }
    .header img { max-height: 60px; margin-bottom: 8px; }
    .header h2 { font-size: 18px; color: #1a5f2a; margin-bottom: 4px; }
    .header p { font-size: 10px; color: #666; margin: 2px 0; }
    .receipt-title { text-align: center; font-size: 16px; font-weight: bold; color: #1a5f2a; margin: 20px 0; text-transform: uppercase; letter-spacing: 2px; }
    .info-grid { display: table; width: 100%; margin-bottom: 20px; }
    .info-row { display: table-row; }
    .info-cell { display: table-cell; width: 50%; padding: 4px 0; vertical-align: top; }
    .info-label { font-size: 9px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
    .info-value { font-size: 11px; font-weight: bold; color: #333; }
    table.items { width: 100%; border-collapse: collapse; margin: 15px 0; }
    table.items th { background: #1a5f2a; color: #fff; font-size: 10px; text-transform: uppercase; padding: 8px; text-align: left; }
    table.items td { padding: 8px; border-bottom: 1px solid #e0e0e0; font-size: 11px; }
    table.items td.amount { text-align: right; font-family: monospace; }
    table.items tfoot td { border-top: 2px solid #1a5f2a; font-weight: bold; font-size: 12px; }
    .total-row { background: #f8f9fa; }
    .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
    .signature-grid { display: table; width: 100%; margin-top: 30px; }
    .signature-cell { display: table-cell; width: 50%; vertical-align: bottom; }
    .signature-line { border-top: 1px solid #333; width: 80%; margin-top: 40px; padding-top: 4px; font-size: 10px; }
    .stamp-area { text-align: right; }
    .stamp-box { display: inline-block; border: 2px dashed #1a5f2a; padding: 15px 25px; color: #1a5f2a; font-size: 10px; font-weight: bold; opacity: 0.6; }
    .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 60px; color: rgba(26,95,42,0.06); font-weight: bold; pointer-events: none; z-index: 0; }
    .status-paid { display: inline-block; background: #1a5f2a; color: #fff; padding: 3px 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .mb-1 { margin-bottom: 4px; }
    .mb-2 { margin-bottom: 8px; }
    .mt-2 { margin-top: 8px; }
    .mt-3 { margin-top: 12px; }
    .text-muted { color: #888; }
    .font-mono { font-family: 'Courier New', monospace; }
  </style>
</head>
<body>
  <div class="watermark">{{ $school?->short_name ?? 'SCHOOL' }}</div>
  <div class="receipt">

    {{-- Header --}}
    <div class="header">
      @if($school?->logo)
        <img src="{{ storage_path('app/public/' . $school->logo) }}" alt="School Logo">
      @endif
      <h2>{{ $school?->name ?? 'School Name' }}</h2>
      <p>{{ $school?->address ?? '' }}</p>
      <p>Phone: {{ $school?->phone ?? 'N/A' }} · Email: {{ $school?->email ?? 'N/A' }}</p>
      @if($school?->waec_centre_number)
        <p>WAEC Centre: {{ $school->waec_centre_number }}</p>
      @endif
    </div>

    {{-- Title --}}
    <div class="receipt-title">
      <span class="status-paid">✓ Paid</span>
      <div class="mt-2">Official Fee Receipt</div>
    </div>

    {{-- Receipt Info --}}
    <div class="info-grid">
      <div class="info-row">
        <div class="info-cell">
          <div class="info-label">Receipt Number</div>
          <div class="info-value font-mono">{{ $payment->receipt_number }}</div>
        </div>
        <div class="info-cell text-right">
          <div class="info-label">Date of Payment</div>
          <div class="info-value">{{ $payment->paid_at?->format('d F Y') ?? 'N/A' }}</div>
        </div>
      </div>
      <div class="info-row">
        <div class="info-cell">
          <div class="info-label">Payment Reference</div>
          <div class="info-value font-mono">{{ $payment->payment_reference }}</div>
        </div>
        <div class="info-cell text-right">
          <div class="info-label">Academic Term</div>
          <div class="info-value">{{ $payment->term?->name ?? 'N/A' }} — {{ $payment->term?->session?->name ?? '' }}</div>
        </div>
      </div>
    </div>

    {{-- Student Info --}}
    <div style="background:#f8f9fa; padding:12px 15px; border-radius:4px; margin-bottom:20px;">
      <div class="info-grid" style="margin-bottom:0;">
        <div class="info-row">
          <div class="info-cell">
            <div class="info-label">Student Name</div>
            <div class="info-value">{{ $payment->student->user->full_name }}</div>
          </div>
          <div class="info-cell text-right">
            <div class="info-label">Admission Number</div>
            <div class="info-value font-mono">{{ $payment->student->admission_number }}</div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-cell">
            <div class="info-label">Class</div>
            <div class="info-value">{{ $payment->student->currentEnrollment?->classArm?->full_name ?? 'N/A' }}</div>
          </div>
          <div class="info-cell text-right">
            <div class="info-label">Payment Method</div>
            <div class="info-value">{{ $payment->payment_method }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Items Table --}}
    <table class="items">
      <thead>
        <tr>
          <th style="width:40px;">#</th>
          <th>Fee Category</th>
          <th class="amount">Amount (₦)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payment->allocations as $i => $alloc)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $alloc->ledger->feeStructure->feeCategory->name }}</td>
            <td class="amount">{{ number_format($alloc->amount_allocated, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td>1</td>
            <td>General Fee Payment</td>
            <td class="amount">{{ number_format($payment->amount, 2) }}</td>
          </tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="2" class="text-right">Total Amount Paid:</td>
          <td class="amount">{{ number_format($payment->amount, 2) }}</td>
        </tr>
      </tfoot>
    </table>

    @if($payment->notes)
      <div style="margin:15px 0; padding:10px; background:#fffbeb; border:1px dashed #f59e0b; font-size:10px;">
        <strong>Note:</strong> {{ $payment->notes }}
      </div>
    @endif

    {{-- Footer / Signatures --}}
    <div class="footer">
      <div class="signature-grid">
        <div class="signature-cell">
          <div class="signature-line">
            <strong>Bursar / Authorized Signatory</strong><br>
            {{ $payment->verifiedBy?->full_name ?? 'N/A' }}<br>
            <span class="text-muted">Date: {{ now()->format('d M Y') }}</span>
          </div>
        </div>
        <div class="signature-cell text-right">
          <div class="stamp-area">
            @if($school?->stamp)
              <img src="{{ storage_path('app/public/' . $school->stamp) }}" style="max-height:70px; opacity:0.7;">
            @else
              <div class="stamp-box">OFFICIAL STAMP</div>
            @endif
          </div>
        </div>
      </div>

      <div class="text-center mt-3" style="font-size:9px; color:#999;">
        <p>This is a computer-generated receipt and does not require a physical signature.</p>
        <p>Generated on {{ now()->format('d F Y, h:i A') }} · {{ $school?->name ?? '' }}</p>
      </div>
    </div>

  </div>
</body>
</html>
