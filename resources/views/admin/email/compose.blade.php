@extends('admin.admin_layout')

@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="content-page-header content-page-headersplit">
            <h5>Send Email Reminders</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.fees.defaulters') }}">
                            <i class="fe fe-arrow-left me-2"></i>Back to Defaulters
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0 fw-bold">Compose Email</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.email.send') }}" method="POST" id="emailForm">
                            @csrf

                            {{-- Target Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Send To <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-check card p-3 border h-100 target-card" onclick="selectTarget('all_defaulters')">
                                            <input type="radio" name="target_type" id="target_all" value="all_defaulters" class="form-check-input" checked required>
                                            <label class="form-check-label w-100" for="target_all">
                                                <div class="fw-semibold">All Defaulters</div>
                                                <div class="text-muted small">Students with outstanding balance</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card p-3 border h-100 target-card" onclick="selectTarget('class_arm')">
                                            <input type="radio" name="target_type" id="target_class" value="class_arm" class="form-check-input" required>
                                            <label class="form-check-label w-100" for="target_class">
                                                <div class="fw-semibold">Class Arm</div>
                                                <div class="text-muted small">Specific class only</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card p-3 border h-100 target-card" onclick="selectTarget('specific_students')">
                                            <input type="radio" name="target_type" id="target_specific" value="specific_students" class="form-check-input" required>
                                            <label class="form-check-label w-100" for="target_specific">
                                                <div class="fw-semibold">Specific Students</div>
                                                <div class="text-muted small">Select individually</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Class Arm Select --}}
                            <div class="mb-4" id="classArmSelect" style="display:none;">
                                <label class="form-label fw-semibold">Select Class Arm</label>
                                <select name="class_arm_id" class="form-select">
                                    <option value="">— Select Class —</option>
                                    @foreach($classArms as $arm)
                                        <option value="{{ $arm->id }}">{{ $arm->classLevel->name }}{{ $arm->arm }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Specific Students --}}
                            @if(count($preselected) > 0)
                                <div class="mb-4" id="specificStudentsSelect">
                                    <label class="form-label fw-semibold">Select Students</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover border">
                                            <thead><tr><th style="width:40px;"><input type="checkbox" id="selectAllStudents" onchange="toggleAllStudents()"></th><th>Student</th><th>Class</th><th class="text-end">Balance</th></tr></thead>
                                            <tbody>
                                                @foreach($preselected as $s)
                                                    <tr>
                                                        <td><input type="checkbox" name="student_ids[]" value="{{ $s['id'] }}" class="student-checkbox" checked></td>
                                                        <td>{{ $s['name'] }}</td>
                                                        <td>{{ $s['class'] }}</td>
                                                        <td class="text-end fw-bold text-danger">₦{{ number_format($s['balance'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Use Parent Email --}}
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="use_parent_email" id="useParentEmail" value="1" class="form-check-input" checked>
                                    <label class="form-check-label" for="useParentEmail">Send to parent/guardian email (recommended)</label>
                                </div>
                            </div>

                            {{-- Template --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Template</label>
                                <select class="form-select" onchange="loadTemplate(this.value)">
                                    <option value="">— Select Template —</option>
                                    <option value="fee_reminder">Fee Reminder</option>
                                    <option value="payment_due">Payment Due</option>
                                    <option value="general">Custom</option>
                                </select>
                            </div>

                            {{-- Subject --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="emailSubject" class="form-control" required value="{{ old('subject') }}">
                            </div>

                            {{-- Body --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
                                <textarea name="body" id="emailBody" rows="6" class="form-control" required>{{ old('body') }}</textarea>
                                <div class="text-muted small mt-1">Variables: {{student_name}}, {{balance}}, {{class}}, {{term}}, {{school}}. HTML is supported.</div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-send me-1"></i> Send Emails
                                </button>
                                <a href="{{ route('admin.email.preview') }}" class="btn btn-outline-info" target="_blank" onclick="return previewEmail()">
                                    <i class="fe fe-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('admin.fees.defaulters') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Tips</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Emails support HTML formatting.</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Use variables to personalize each email.</li>
                            <li class="mb-2"><i class="fe fe-check-circle me-1 text-success"></i> Sending to parent email is recommended.</li>
                            <li><i class="fe fe-check-circle me-1 text-success"></i> Emails are queued and sent in batches.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const templates = @json($templates);

function selectTarget(type) {
    document.querySelectorAll('.target-card').forEach(card => card.classList.remove('border-primary'));
    event.currentTarget.classList.add('border-primary');
    document.getElementById('classArmSelect').style.display = (type === 'class_arm') ? 'block' : 'none';
    document.getElementById('specificStudentsSelect').style.display = (type === 'specific_students') ? 'block' : 'none';
}

function loadTemplate(key) {
    if (!key || !templates[key]) return;
    document.getElementById('emailSubject').value = templates[key].subject;
    document.getElementById('emailBody').value = templates[key].body;
}

function toggleAllStudents() {
    const checked = document.getElementById('selectAllStudents').checked;
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
}

function previewEmail() {
    const subject = document.getElementById('emailSubject').value;
    const body = document.getElementById('emailBody').value;
    if (!subject || !body) { alert('Please enter subject and body first.'); return false; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.email.preview') }}';
    form.target = '_blank';
    form.innerHTML = `@csrf<input type="hidden" name="subject" value="${subject}"><input type="hidden" name="body" value="${body}">`;
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    return false;
}
</script>

@endsection
