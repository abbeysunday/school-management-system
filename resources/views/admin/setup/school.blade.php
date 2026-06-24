@extends('admin.admin_layout')
@section('content')

<div class="page-wrapper page-settings">
    <div class="content-sidelink">
        <div class="content-sidelinkheading"><h6>Settings</h6></div>
        <div class="content-sidelinkmenu">
            <ul>
                <li><h5>General Settings</h5></li>
                <li><a href="#">Localization</a></li>
                <li><a href="#">Account Settings</a></li>
                <li><a href="#">Security</a></li>
                <li><a href="#">Notifications</a></li>
            </ul>
            <ul>
                <li><h5>School Settings</h5></li>
                <li><a href="{{ route('admin.setup.school') }}" class="active">School Profile</a></li>
                <li><a href="#">Academic Settings</a></li>
                <li><a href="#">Appearance</a></li>
            </ul>
            <ul>
                <li><h5>System Settings</h5></li>
                <li><a href="#">Email Settings</a></li>
                <li><a href="#">SMS Settings</a></li>
            </ul>
            <ul>
                <li><h5>Financial Settings</h5></li>
                <li><a href="#">Payment Gateways</a></li>
                <li><a href="#">Currencies</a></li>
            </ul>
        </div>
    </div>

    <div class="content w-100">
        <div class="content-page-header">
            <h5>School Profile</h5>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.setup.school.update') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            {{-- === School Identity === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>School Identity</h2></div></div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>School Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $school->name) }}" placeholder="Enter school name">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Short Name</label>
                                <input type="text" name="short_name" class="form-control"
                                       value="{{ old('short_name', $school->short_name) }}" placeholder="e.g. FGC">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Motto</label>
                                <input type="text" name="motto" class="form-control"
                                       value="{{ old('motto', $school->motto) }}" placeholder="School motto">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Principal Name</label>
                                <input type="text" name="principal_name" class="form-control"
                                       value="{{ old('principal_name', $school->principal_name) }}" placeholder="Principal's full name">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === Branding / Uploads === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>Branding</h2></div></div>

                        {{-- Logo --}}
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-groupheads"><h3>School Logo</h3></div>
                            <div class="upload-div">
                                <div class="upload-sets">
                                    <div class="upload-sets-btn">
                                        <input type="file" name="logo" id="logoInput" accept="image/*">
                                        <a href="javascript:void(0);" class="btn btn-upload" onclick="document.getElementById('logoInput').click()">Upload</a>
                                    </div>
                                    <p>Recommended: 300×100px. Max 2MB.</p>
                                    @error('logo')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="upload-img">
                                    <div class="upload-imgset">
                                        <img id="logoPreview"
                                             src="{{ $school->logo ? asset('storage/'.$school->logo) : asset('assets/img/icons/gallery.svg') }}"
                                             alt="Logo preview" style="max-height:100px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stamp / Seal --}}
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-groupheads"><h3>Official Stamp / Seal</h3></div>
                            <div class="upload-div">
                                <div class="upload-sets">
                                    <div class="upload-sets-btn">
                                        <input type="file" name="stamp" id="stampInput" accept="image/*">
                                        <a href="javascript:void(0);" class="btn btn-upload" onclick="document.getElementById('stampInput').click()">Upload</a>
                                    </div>
                                    <p>Recommended: 300×300px. Max 2MB.</p>
                                    @error('stamp')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="upload-img">
                                    <div class="upload-imgset">
                                        <img id="stampPreview"
                                             src="{{ $school->stamp ? asset('storage/'.$school->stamp) : asset('assets/img/icons/gallery.svg') }}"
                                             alt="Stamp preview" style="max-height:100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === Contact & Address === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>Contact & Address</h2></div></div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Contact Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $school->email) }}" placeholder="info@school.edu.ng">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone', $school->phone) }}" placeholder="080xxxxxxxx">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Website</label>
                                <input type="url" name="website" class="form-control"
                                       value="{{ old('website', $school->website) }}" placeholder="https://school.edu.ng">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" rows="2" class="form-control" placeholder="Full school address">{{ old('address', $school->address) }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control"
                                       value="{{ old('state', $school->state) }}" placeholder="e.g. Lagos">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>LGA</label>
                                <input type="text" name="lga" class="form-control"
                                       value="{{ old('lga', $school->lga) }}" placeholder="Local Govt Area">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control"
                                       value="{{ old('city', $school->city) }}" placeholder="City">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === Academic & Financial === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>Academic & Financial Defaults</h2></div></div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label>CA Weight (%)</label>
                                <input type="number" name="ca_weight" min="0" max="100" class="form-control"
                                       value="{{ old('ca_weight', $school->ca_weight) }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label>Exam Weight (%)</label>
                                <input type="number" name="exam_weight" min="0" max="100" class="form-control"
                                       value="{{ old('exam_weight', $school->exam_weight) }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label>Currency Symbol</label>
                                <input type="text" name="currency_symbol" class="form-control"
                                       value="{{ old('currency_symbol', $school->currency_symbol) }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label>Timezone</label>
                                <input type="text" name="timezone" class="form-control"
                                       value="{{ old('timezone', $school->timezone) }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>WAEC Centre No</label>
                                <input type="text" name="waec_centre_number" class="form-control"
                                       value="{{ old('waec_centre_number', $school->waec_centre_number) }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>NECO Centre No</label>
                                <input type="text" name="neco_centre_number" class="form-control"
                                       value="{{ old('neco_centre_number', $school->neco_centre_number) }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label>RC Number</label>
                                <input type="text" name="rc_number" class="form-control"
                                       value="{{ old('rc_number', $school->rc_number) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === Integrations === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>Payment & Messaging APIs</h2></div></div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Paystack Public Key</label>
                                <input type="text" name="paystack_public_key" class="form-control"
                                       value="{{ old('paystack_public_key', $school->paystack_public_key) }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Paystack Secret Key</label>
                                <input type="text" name="paystack_secret_key" class="form-control"
                                       value="{{ old('paystack_secret_key', $school->paystack_secret_key) }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Termii API Key</label>
                                <input type="text" name="termii_api_key" class="form-control"
                                       value="{{ old('termii_api_key', $school->termii_api_key) }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Termii Sender ID</label>
                                <input type="text" name="termii_sender_id" class="form-control"
                                       value="{{ old('termii_sender_id', $school->termii_sender_id) }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Mail From Address</label>
                                <input type="email" name="mail_from_address" class="form-control"
                                       value="{{ old('mail_from_address', $school->mail_from_address) }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Mail From Name</label>
                                <input type="text" name="mail_from_name" class="form-control"
                                       value="{{ old('mail_from_name', $school->mail_from_name) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === SMS Toggles === --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12"><div class="form-groupheads"><h2>SMS Auto-Notifications</h2></div></div>
<div class="col-md-4">
                <div class="form-check form-switch">
                    <input type="checkbox" name="email_on_absence" id="email_on_absence" value="1" class="form-check-input" {{ old('email_on_absence', $school?->email_on_absence) ? 'checked' : '' }}>
                    <label class="form-check-label" for="email_on_absence">
                        <strong>Email on Absence</strong>
                        <div class="text-muted small">Send email to parent when student is marked absent</div>
                    </label>
                </div>
            </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="sms_on_absence" value="0">
                                <input class="form-check-input" type="checkbox" name="sms_on_absence" value="1"
                                       id="sms_absence" {{ old('sms_on_absence', $school->sms_on_absence) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_absence">SMS on Absence</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="sms_on_payment" value="0">
                                <input class="form-check-input" type="checkbox" name="sms_on_payment" value="1"
                                       id="sms_payment" {{ old('sms_on_payment', $school->sms_on_payment) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_payment">SMS on Payment</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group form-check form-switch">
                                <input type="hidden" name="sms_on_result_publish" value="0">
                                <input class="form-check-input" type="checkbox" name="sms_on_result_publish" value="1"
                                       id="sms_result" {{ old('sms_on_result_publish', $school->sms_on_result_publish) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_result">SMS on Result Publish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="btn-path mb-4">
                    <a href="{{ url()->previous() }}" class="btn btn-cancel me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function previewImage(inputId, imgId) {
        document.getElementById(inputId).addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                document.getElementById(imgId).src = URL.createObjectURL(file);
            }
        });
    }
    previewImage('logoInput', 'logoPreview');
    previewImage('stampInput', 'stampPreview');
</script>

@endsection
