<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Timetable — {{ $classArmName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .page-header {
            text-align: center;
            border-bottom: 2px solid #1a5f2a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .school-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 8px;
        }
        .school-name {
            font-size: 18px;
            font-weight: 700;
            color: #1a5f2a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-address {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a5f2a;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .meta-item { display: flex; gap: 4px; }
        .meta-label { font-weight: 600; color: #555; }
        .meta-value { color: #1a5f2a; font-weight: 700; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #1a5f2a;
            color: #fff;
            font-weight: 600;
            text-align: center;
            padding: 8px 6px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #145022;
        }
        th:first-child {
            text-align: left;
            width: 130px;
            background: #145022;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: top;
            min-width: 110px;
            height: 60px;
        }
        td:first-child {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 10px;
        }
        .period-name { font-size: 10px; font-weight: 700; color: #333; }
        .period-time { font-size: 9px; color: #888; font-family: monospace; }
        .period-type {
            display: inline-block;
            font-size: 8px;
            padding: 1px 5px;
            border-radius: 3px;
            margin-top: 2px;
            font-weight: 600;
        }
        .type-break { background: #fff3cd; color: #856404; }
        .type-assembly { background: #d1ecf1; color: #0c5460; }
        .type-games { background: #d4edda; color: #155724; }
        .type-closing { background: #e2e3e5; color: #383d41; }
        .entry-box {
            border-radius: 4px;
            padding: 5px;
            height: 100%;
            border-left: 3px solid transparent;
        }
        .entry-subject {
            font-weight: 700;
            font-size: 10px;
            color: #222;
            line-height: 1.3;
        }
        .entry-teacher {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        .entry-room {
            font-size: 8px;
            color: #999;
            margin-top: 1px;
        }
        .free-period {
            color: #ccc;
            font-style: italic;
            font-size: 9px;
            text-align: center;
            padding-top: 10px;
        }
        .non-teaching-cell {
            text-align: center;
            color: #bbb;
            font-size: 9px;
            padding-top: 8px;
        }
        .legend {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .legend-title {
            font-weight: 700;
            font-size: 10px;
            color: #555;
            margin-right: 5px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 9px;
            padding: 2px 8px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
        }
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #888;
        }
        .signature-line {
            margin-top: 30px;
            display: flex;
            gap: 60px;
        }
        .signature-box {
            width: 180px;
        }
        .signature-line hr {
            border: none;
            border-top: 1px solid #333;
            margin-bottom: 4px;
        }
        .signature-label {
            font-size: 9px;
            color: #555;
            text-align: center;
        }
        @media print {
            body { padding: 10px; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
    </style>
</head>
<body>

    {{-- School Header --}}
    <div class="page-header">
        @if($school && $school->logo)
            <img src="{{ public_path('storage/' . $school->logo) }}" class="school-logo" alt="School Logo">
        @endif
        <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
        <div class="school-address">
            {{ $school->address ?? '' }}
            @if($school && $school->phone) | Tel: {{ $school->phone }} @endif
            @if($school && $school->email) | Email: {{ $school->email }} @endif
        </div>
        <div class="doc-title">Class Timetable</div>
    </div>

    {{-- Meta Info --}}
    <div class="meta-row">
        <div class="meta-item">
            <span class="meta-label">Class Arm:</span>
            <span class="meta-value">{{ $classArmName }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Session:</span>
            <span class="meta-value">{{ $session->name }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Term:</span>
            <span class="meta-value">{{ $currentTerm }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Student:</span>
            <span class="meta-value">{{ $student->user->full_name }}</span>
        </div>
    </div>

    {{-- Weekly Grid --}}
    <table>
        <thead>
            <tr>
                <th>Period / Day</th>
                @foreach($days as $day)
                    <th>{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($periods as $period)
                <tr>
                    {{-- Period column --}}
                    <td>
                        <div class="period-name">{{ $period->period_name }}</div>
                        <div class="period-time">
                            {{ \Carbon\Carbon::parse($period->start_time)->format('g:i') }} – {{ \Carbon\Carbon::parse($period->end_time)->format('g:i A') }}
                        </div>
                        @if(!$period->isTeaching())
                            <span class="period-type type-{{ strtolower($period->period_type) }}">{{ $period->period_type }}</span>
                        @endif
                    </td>

                    {{-- Day columns --}}
                    @foreach($days as $day)
                        @php
                            $entry = $timetableGrid[$day][$period->id] ?? null;
                        @endphp
                        <td>
                            @if(!$period->isTeaching())
                                <div class="non-teaching-cell">{{ $period->period_type }}</div>
                            @elseif($entry && $entry->subject)
                                @php $color = $subjectColors[$entry->subject->name] ?? '#9ca3af'; @endphp
                                <div class="entry-box" style="border-left-color: {{ $color }};">
                                    <div class="entry-subject">{{ $entry->subject->name }}</div>
                                    <div class="entry-teacher">{{ $entry->teacher?->user?->full_name ?? 'TBA' }}</div>
                                    @if($entry->room)
                                        <div class="entry-room">{{ $entry->room }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="free-period">Free</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Subject Legend --}}
    @if(count($subjectColors) > 0)
    <div class="legend">
        <span class="legend-title">Subjects:</span>
        @foreach($subjectColors as $subjectName => $color)
            <span class="legend-item">
                <span class="legend-dot" style="background: {{ $color }};"></span>
                {{ $subjectName }}
            </span>
        @endforeach
    </div>
    @endif

    {{-- Signature Lines --}}
    <div class="signature-line">
        <div class="signature-box">
            <hr>
            <div class="signature-label">Class Teacher's Signature</div>
        </div>
        <div class="signature-box">
            <hr>
            <div class="signature-label">Principal's Signature</div>
        </div>
        <div class="signature-box">
            <hr>
            <div class="signature-label">Date</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>Generated on {{ now()->format('F j, Y 	 g:i A') }}</span>
        <span>Page 1 of 1</span>
    </div>

    {{-- Print button (hidden when printing) --}}
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 24px; background: #1a5f2a; color: #fff; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
            🖨️ Print Timetable
        </button>
        <a href="{{ route('student.timetable.index') }}" style="margin-left: 10px; padding: 10px 24px; background: #f8f9fa; color: #333; border: 1px solid #dee2e6; border-radius: 6px; font-size: 12px; text-decoration: none; display: inline-block;">
            ← Back to Timetable
        </a>
    </div>

</body>
</html>
