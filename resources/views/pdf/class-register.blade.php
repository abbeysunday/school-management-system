<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Register — {{ $classArm->full_name }} — {{ $term->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; line-height: 1.3; color: #333; }
        .register { padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #1a5f2a; padding-bottom: 8px; margin-bottom: 10px; }
        .header h2 { font-size: 14px; color: #1a5f2a; margin-bottom: 2px; }
        .header p { font-size: 8px; color: #666; margin: 1px 0; }
        .meta { display: table; width: 100%; margin-bottom: 8px; font-size: 9px; }
        .meta-row { display: table-row; }
        .meta-cell { display: table-cell; width: 33%; }
        .meta-label { color: #888; font-size: 7px; text-transform: uppercase; }
        .meta-value { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th { background: #1a5f2a; color: #fff; font-size: 7px; padding: 3px 2px; text-align: center; border: 1px solid #1a5f2a; }
        td { padding: 2px 3px; border: 1px solid #ddd; font-size: 7px; text-align: center; }
        td.name { text-align: left; font-size: 8px; font-weight: 600; }
        td.adm { text-align: left; font-size: 7px; color: #666; }
        .status-P { background: #d4edda; color: #155724; font-weight: bold; }
        .status-A { background: #f8d7da; color: #721c24; font-weight: bold; }
        .status-L { background: #fff3cd; color: #856404; font-weight: bold; }
        .status-S { background: #d1ecf1; color: #0c5460; font-weight: bold; }
        .status-E { background: #e2e3e5; color: #383d41; font-weight: bold; }
        .status-na { color: #ccc; }
        .footer { margin-top: 10px; border-top: 1px solid #ddd; padding-top: 5px; font-size: 7px; color: #999; text-align: center; }
        .page-break { page-break-after: always; }
        .legend { margin-top: 5px; font-size: 7px; }
        .legend span { margin-right: 10px; }
        .legend .box { display: inline-block; width: 10px; height: 10px; margin-right: 2px; vertical-align: middle; border: 1px solid #999; }
    </style>
</head>
<body>
    @foreach($dayChunks as $chunkIndex => $chunkDays)
    <div class="register {{ $chunkIndex > 0 ? 'page-break' : '' }}">
        <div class="header">
            <h2>{{ $school?->name ?? 'School' }}</h2>
            <p>Attendance Register — {{ $classArm->classLevel->name }}{{ $classArm->arm }} — {{ $term->name }} ({{ $term->session?->name }})</p>
        </div>

        <div class="meta">
            <div class="meta-row">
                <div class="meta-cell">
                    <div class="meta-label">Class Arm</div>
                    <div class="meta-value">{{ $classArm->classLevel->name }}{{ $classArm->arm }}</div>
                </div>
                <div class="meta-cell">
                    <div class="meta-label">Term</div>
                    <div class="meta-value">{{ $term->name }}</div>
                </div>
                <div class="meta-cell">
                    <div class="meta-label">Total Students</div>
                    <div class="meta-value">{{ count($registerData) }}</div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:25px;">S/N</th>
                    <th style="width:120px;">Student Name</th>
                    <th style="width:50px;">Adm. No</th>
                    @foreach($chunkDays as $day)
                        <th style="width:22px;">{{ date('d', strtotime($day)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($registerData as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="name">{{ $row['name'] }}</td>
                        <td class="adm">{{ $row['admission_no'] }}</td>
                        @foreach($chunkDays as $day)
                            @php $status = $row['days'][$day] ?? null; @endphp
                            <td class="{{ $status ? 'status-' . substr($status, 0, 1) : 'status-na' }}">
                                {{ $status ? substr($status, 0, 1) : '—' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="legend">
            <span><span class="box" style="background:#d4edda;"></span> P = Present</span>
            <span><span class="box" style="background:#f8d7da;"></span> A = Absent</span>
            <span><span class="box" style="background:#fff3cd;"></span> L = Late</span>
            <span><span class="box" style="background:#d1ecf1;"></span> S = Sick</span>
            <span><span class="box" style="background:#e2e3e5;"></span> E = Excused</span>
            <span><span class="box" style="background:#fff;"></span> — = Not Marked</span>
        </div>

        <div class="footer">
            <p>Page {{ $chunkIndex + 1 }} of {{ count($dayChunks) }} · Generated on {{ now()->format('d F Y') }} · {{ $school?->name ?? '' }}</p>
        </div>
    </div>
    @endforeach
</body>
</html>
