<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable — {{ $classArm->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; line-height: 1.4; color: #333; }
        .report { padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #1a5f2a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { font-size: 16px; color: #1a5f2a; }
        .header p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #1a5f2a; color: #fff; font-size: 9px; text-transform: uppercase; padding: 6px; text-align: center; border: 1px solid #1a5f2a; }
        td { padding: 5px; border: 1px solid #ccc; font-size: 9px; vertical-align: top; }
        .period-cell { background: #f8f9fa; font-weight: bold; white-space: nowrap; }
        .entry-box { background: #e8f4f8; border-radius: 3px; padding: 3px; }
        .entry-subject { font-weight: bold; color: #1a5f2a; }
        .entry-teacher { color: #666; font-size: 8px; }
        .entry-room { color: #888; font-size: 8px; }
        .non-teaching { background: #fffbeb; text-align: center; font-weight: bold; color: #666; }
        .footer { margin-top: 15px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 8px; color: #999; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="report">
        <div class="header">
            <h2>{{ $school?->name ?? 'School' }}</h2>
            <p>Class Timetable — {{ $classArm->full_name }} — {{ $session->name }}</p>
            <p>Generated on {{ now()->format('d F Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:100px;">Period</th>
                    @foreach($days as $day)
                        <th style="width:16%;">{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    <tr>
                        <td class="period-cell">
                            <div>{{ $period->period_name }}</div>
                            <div style="font-size:8px;color:#666;">{{ $period->start_time }} - {{ $period->end_time }}</div>
                        </td>
                        @foreach($days as $day)
                            @php $entry = $timetableGrid[$day][$period->id] ?? null; @endphp
                            <td>
                                @if(!$period->isTeaching())
                                    <div class="non-teaching">{{ $period->period_type }}</div>
                                @elseif($entry)
                                    <div class="entry-box">
                                        <div class="entry-subject">{{ $entry->subject?->name ?? '—' }}</div>
                                        @if($entry->teacher)
                                            <div class="entry-teacher">{{ $entry->teacher->user->full_name }}</div>
                                        @endif
                                        @if($entry->room)
                                            <div class="entry-room">{{ $entry->room }}</div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center text-muted" style="font-size:8px;">—</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>{{ $school?->name ?? '' }} · {{ $school?->address ?? '' }} · {{ $school?->phone ?? '' }}</p>
        </div>
    </div>
</body>
</html>
