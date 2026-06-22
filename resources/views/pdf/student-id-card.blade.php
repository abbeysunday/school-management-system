<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ID Card - {{ $student->admission_number }}</title>
<style>
    @page {
        margin: 0;
        size: 153.07pt 243.78pt;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        width: 153.07pt;
        height: 243.78pt;
        overflow: hidden;
        background: #ffffff;
        color: #1a1a2e;
    }

    /* ── Header band ─────────────────────────── */
    .header {
        width: 100%;
        background-color: #0d3b6e;
        text-align: center;
        padding: 7pt 6pt 5pt;
    }
    .header-inner {
        display: table;
        width: 100%;
    }
    .header-logo-cell {
        display: table-cell;
        width: 30pt;
        vertical-align: middle;
        text-align: center;
    }
    .header-text-cell {
        display: table-cell;
        vertical-align: middle;
        text-align: left;
        padding-left: 4pt;
    }
    .header-logo-cell img {
        width: 26pt;
        height: 26pt;
        object-fit: contain;
    }
    .header-logo-circle {
        width: 26pt;
        height: 26pt;
        background: #ffffff22;
        border-radius: 50%;
        display: inline-block;
        line-height: 26pt;
        text-align: center;
        font-size: 13pt;
        color: #fff;
    }
    .school-name {
        font-size: 7.5pt;
        font-weight: bold;
        color: #ffffff;
        line-height: 1.3;
        text-transform: uppercase;
    }
    .school-motto {
        font-size: 5.5pt;
        color: #b0c8e8;
        font-style: italic;
        margin-top: 1pt;
        line-height: 1.2;
    }

    /* ── Card-type stripe ────────────────────── */
    .card-type {
        background-color: #e8a020;
        text-align: center;
        font-size: 6pt;
        font-weight: bold;
        color: #ffffff;
        letter-spacing: 1.5pt;
        text-transform: uppercase;
        padding: 3pt 0;
    }

    /* ── Body ────────────────────────────────── */
    .body {
        padding: 8pt 10pt 5pt;
        text-align: center;
    }

    /* Photo */
    .photo-wrap {
        margin: 0 auto 5pt;
        width: 60pt;
        height: 60pt;
        border-radius: 50%;
        border: 2.5pt solid #0d3b6e;
        overflow: hidden;
        background: #dde5f0;
    }
    .photo-wrap img {
        width: 60pt;
        height: 60pt;
        object-fit: cover;
    }

    /* Name */
    .student-name {
        font-size: 9.5pt;
        font-weight: bold;
        color: #0d3b6e;
        text-transform: uppercase;
        line-height: 1.2;
        margin-bottom: 2pt;
    }
    .student-class {
        font-size: 7pt;
        color: #444;
        margin-bottom: 6pt;
        font-weight: bold;
        letter-spacing: .5pt;
    }

    /* Info table */
    .info-box {
        background: #f0f5fc;
        border: 0.75pt solid #c5d5ea;
        border-radius: 4pt;
        padding: 5pt 7pt;
        text-align: left;
        margin-bottom: 5pt;
    }
    .info-row {
        display: table;
        width: 100%;
        font-size: 6.2pt;
        line-height: 1.65;
        color: #333;
    }
    .info-label {
        display: table-cell;
        color: #5a7a9e;
        font-weight: bold;
        white-space: nowrap;
        width: 45pt;
        text-transform: uppercase;
        font-size: 5.8pt;
        letter-spacing: .3pt;
    }
    .info-value {
        display: table-cell;
        color: #1a1a2e;
        font-weight: bold;
    }

    /* Validity badge */
    .validity {
        font-size: 5.8pt;
        color: #666;
        text-align: center;
        margin-top: 3pt;
    }
    .validity strong {
        color: #0d3b6e;
    }

    /* ── Divider line ────────────────────────── */
    .divider {
        border: none;
        border-top: 0.5pt solid #c5d5ea;
        margin: 0 10pt 5pt;
    }

    /* ── Footer band ─────────────────────────── */
    .footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #0d3b6e;
        color: #b0c8e8;
        text-align: center;
        font-size: 5.5pt;
        padding: 4pt 6pt;
        line-height: 1.6;
    }
    .footer strong {
        color: #ffffff;
        font-size: 5.8pt;
    }
</style>
</head>
<body>

{{-- ── HEADER ─────────────────────────────────────── --}}
<div class="header">
    <div class="header-inner">
        <div class="header-logo-cell">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="Logo">
            @else
                <span class="header-logo-circle">&#127979;</span>
            @endif
        </div>
        <div class="header-text-cell">
            <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
            @if($school->motto)
                <div class="school-motto">"{{ $school->motto }}"</div>
            @endif
        </div>
    </div>
</div>

{{-- ── CARD-TYPE STRIPE ─────────────────────────── --}}
<div class="card-type">Student &nbsp;Identity &nbsp;Card</div>

{{-- ── BODY ──────────────────────────────────────── --}}
<div class="body">

    {{-- Photo --}}
    <div class="photo-wrap">
        @if($student->user->photo)
            <img src="{{ public_path('storage/' . $student->user->photo) }}" alt="Photo">
        @else
            <img src="{{ public_path('images/default-avatar.png') }}" alt="Photo">
        @endif
    </div>

    {{-- Name --}}
    <div class="student-name">{{ $student->user->full_name }}</div>
    <div class="student-class">
        {{ $student->currentArm()?->full_name ?? 'Class N/A' }}
    </div>

    {{-- Info box --}}
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Adm. No.</span>
            <span class="info-value">{{ $student->admission_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date of Birth</span>
            <span class="info-value">{{ $student->date_of_birth?->format('d M, Y') ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Gender</span>
            <span class="info-value">{{ $student->gender }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Blood / Geno</span>
            <span class="info-value">
                {{ $student->blood_group ?? 'N/A' }} &nbsp;/&nbsp; {{ $student->genotype ?? 'N/A' }}
            </span>
        </div>
    </div>

    <div class="validity">
        Session: <strong>{{ $student->currentEnrollment?->session?->name ?? '—' }}</strong>
        &nbsp;&bull;&nbsp;
        Term: <strong>{{ $student->currentEnrollment?->term?->name ?? '—' }}</strong>
    </div>

</div>

{{-- ── FOOTER ────────────────────────────────────── --}}
<div class="footer">
    @if($school->address)
        <strong>{{ $school->address }}</strong><br>
    @endif
    @if($school->phone)Tel: <strong>{{ $school->phone }}</strong>@endif
    @if($school->phone && $school->email) &nbsp;&bull;&nbsp; @endif
    @if($school->email)Email: <strong>{{ $school->email }}</strong>@endif
    @if(!$school->address && !$school->phone && !$school->email)
        If found, please return to the school.
    @endif
</div>

</body>
</html>
