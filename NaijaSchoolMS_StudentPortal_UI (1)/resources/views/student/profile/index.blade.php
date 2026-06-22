{{-- File: resources/views/student/profile/index.blade.php --}}
@extends('student.layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account settings and personal information')

@section('content')
@php
$student = [
    'name'           => 'Adaeze Chioma Okonkwo',
    'first_name'     => 'Adaeze',
    'last_name'      => 'Okonkwo',
    'admission_no'   => 'ADM/2024/042',
    'class_arm'      => 'SS2 Science',
    'session'        => '2024/2025',
    'date_of_birth'  => '12 March 2008',
    'gender'         => 'Female',
    'religion'       => 'Christianity',
    'state'          => 'Anambra',
    'lga'            => 'Awka South',
    'blood_group'    => 'O+',
    'genotype'       => 'AA',
    'admission_date' => '8 September 2022',
    'status'         => 'Active',
    'email'          => 'adaeze.okonkwo@student.school.edu.ng',
    'photo'          => null,
];
@endphp

<div class="gcontent">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Profile Card --}}
        <div class="card">
            <div class="prof-cover">
                <div class="prof-av-wrap">
                    <div class="prof-av" id="profileAvatar">
                        @if($student['photo'])
                            <img src="{{ asset('storage/'.$student['photo']) }}" alt="profile">
                        @else
                            {{ strtoupper(substr($student['first_name'],0,1)) }}
                        @endif
                        <div class="prof-av-overlay" id="photoOverlay">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding-top:56px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
                    <div>
                        <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--text)">{{ $student['name'] }}</h3>
                        <div style="font-size:.8rem;color:var(--text-muted);margin-top:3px">{{ $student['admission_no'] }} · {{ $student['class_arm'] }}</div>
                    </div>
                    <span class="badge b-active"><span class="bd"></span>Active</span>
                </div>

                <div class="divider"></div>

                <div class="g2" style="gap:12px">
                    @foreach([
                        ['Session','2024/2025'],
                        ['Class Arm','SS2 Science'],
                        ['Admission Date',$student['admission_date']],
                        ['Gender',$student['gender']],
                        ['Religion',$student['religion']],
                        ['State of Origin',$student['state']],
                        ['Blood Group',$student['blood_group']],
                        ['Genotype',$student['genotype']],
                    ] as $field)
                    <div>
                        <div class="text-xs text-muted fw-semi" style="margin-bottom:2px">{{ $field[0] }}</div>
                        <div class="text-sm fw-semi">{{ $field[1] }}</div>
                    </div>
                    @endforeach
                </div>

                <div class="divider"></div>

                {{-- Photo upload form --}}
                <form method="POST" action="{{ route('student.profile.index') }}" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none">
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                        <div>
                            <div class="text-sm fw-semi">Profile Photo</div>
                            <div class="text-xs text-muted mt-1">JPG or PNG, max 2MB. Click photo to change.</div>
                        </div>
                        <button type="button" id="changePhotoBtn" class="btn btn-outline btn-sm"
                            onclick="document.getElementById('photoInput').click()">
                            Change Photo
                        </button>
                    </div>
                    <div id="photoUploadActions" style="display:none;margin-top:10px">
                        <button type="submit" class="btn btn-primary btn-sm">Save New Photo</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Change Password</div>
            </div>
            <div class="card-body">
                @if($errors->has('password'))
                    <div style="background:#FEE2E2;border-radius:8px;padding:10px 13px;margin-bottom:14px;font-size:.8rem;color:#991B1B">
                        {{ $errors->first('password') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('student.profile.index') }}" id="passwordForm">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" id="newPassword" class="form-control" placeholder="At least 8 characters">
                        <div style="height:4px;background:var(--border);border-radius:2px;margin-top:7px;overflow:hidden">
                            <div id="pwStrength" style="height:4px;border-radius:2px;width:0;transition:width .3s,background .3s"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-top:3px">
                            <span class="form-hint">Min 8 chars, mix of letters & numbers</span>
                            <span id="pwStrengthTxt" class="text-xs fw-semi"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                </form>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Student ID Card Preview --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Student ID Card</div></div>
            <div class="card-body">
                <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:14px;padding:20px;color:white;font-size:.8rem;position:relative;overflow:hidden">
                    <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.04)"></div>
                    <div style="position:absolute;bottom:-30px;right:30px;width:80px;height:80px;border-radius:50%;background:rgba(200,169,106,.08)"></div>
                    <div style="font-size:.65rem;opacity:.6;text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px">Student Identity Card</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                        <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700">A</div>
                        <div>
                            <div style="font-weight:700;font-size:.95rem">{{ $student['name'] }}</div>
                            <div style="opacity:.65;margin-top:2px">{{ $student['class_arm'] }}</div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid rgba(255,255,255,.15)">
                        <div><div style="opacity:.5;font-size:.65rem">ADM NO</div><div style="font-weight:600">{{ $student['admission_no'] }}</div></div>
                        <div style="text-align:right"><div style="opacity:.5;font-size:.65rem">SESSION</div><div style="font-weight:600">{{ $student['session'] }}</div></div>
                    </div>
                </div>
                <a href="#" class="btn btn-ghost btn-block mt-2 btn-sm">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download ID Card (PDF)
                </a>
            </div>
        </div>

        {{-- Account Details --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Account Details</div></div>
            <div class="card-body">
                @foreach([
                    ['Login Email', $student['email']],
                    ['Role', 'Student'],
                    ['Account Status', 'Active'],
                ] as $detail)
                <div style="padding:9px 0;border-bottom:1px solid var(--border-light)">
                    <div class="text-xs text-muted" style="margin-bottom:2px">{{ $detail[0] }}</div>
                    <div class="text-sm fw-semi">{{ $detail[1] }}</div>
                </div>
                @endforeach
                <div class="text-xs text-muted mt-2">
                    To update other personal information, please contact your Form Teacher or the Admin office.
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Quick Links</div></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:6px">
                <a href="{{ route('student.results.index') }}" class="btn btn-ghost btn-sm" style="justify-content:flex-start">📊 My Results</a>
                <a href="{{ route('student.exams.index') }}" class="btn btn-ghost btn-sm" style="justify-content:flex-start">📝 CBT Exams</a>
                <a href="{{ route('student.attendance.index') }}" class="btn btn-ghost btn-sm" style="justify-content:flex-start">✅ Attendance</a>
                <a href="{{ route('student.timetable.index') }}" class="btn btn-ghost btn-sm" style="justify-content:flex-start">🗓️ Timetable</a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Show save button after photo selected
document.getElementById('photoInput')?.addEventListener('change', function() {
    if (this.files?.[0]) {
        document.getElementById('photoUploadActions').style.display = 'block';
        // Preview in avatar
        const reader = new FileReader();
        reader.onload = e => {
            const av = document.getElementById('profileAvatar');
            if (av) av.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                <div class="prof-av-overlay" id="photoOverlay" onclick="document.getElementById('photoInput').click()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:20px;height:20px"><path stroke-linecap="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                </div>`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
@endsection
