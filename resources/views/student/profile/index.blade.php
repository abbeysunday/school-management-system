@extends('student.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-sub', 'Manage your personal information')

@php
$profile = [
    'first_name'       => 'Chidinma',
    'last_name'        => 'Okafor',
    'middle_name'      => 'Adaeze',
    'admission_number' => 'EXC/JSS2/2024/047',
    'class'            => 'JSS 2B',
    'date_of_birth'    => '2012-03-15',
    'gender'           => 'Female',
    'state_of_origin'  => 'Anambra',
    'religion'         => 'Christianity',
    'blood_group'      => 'O+',
    'address'          => '14 Adeola Odeku Street, Victoria Island, Lagos',
    'phone'            => '08012345678',
    'email'            => 'chidinma.okafor@gmail.com',
    'photo'            => null,
];
$guardian = [
    'name'         => 'Mr. Emmanuel Okafor',
    'relationship' => 'Father',
    'phone'        => '08087654321',
    'alt_phone'    => '09011223344',
    'email'        => 'e.okafor@gmail.com',
    'address'      => '14 Adeola Odeku Street, Victoria Island, Lagos',
    'occupation'   => 'Civil Engineer',
];
@endphp

@section('content')

{{-- Hero banner --}}
<div class="relative bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 rounded-2xl p-5 md:p-7 text-white mb-5 overflow-hidden">
  <div class="absolute -right-6 -top-6 w-40 h-40 rounded-full bg-white/[.04] pointer-events-none"></div>
  <div class="flex flex-wrap items-center gap-5">

    {{-- Photo --}}
    <div class="relative flex-shrink-0">
      <div class="w-20 h-20 rounded-full border-4 border-white/25 bg-white/10 flex items-center justify-center font-display font-bold text-3xl overflow-hidden">
        @if($profile['photo'])
          <img src="{{ asset('storage/'.$profile['photo']) }}" alt="" class="w-full h-full object-cover">
        @else
          {{ strtoupper(substr($profile['first_name'],0,1)) }}
        @endif
      </div>
      <button onclick="document.querySelector('[data-tab][data-tab=photo]')?.click()"
              class="absolute bottom-0 right-0 w-6 h-6 bg-gold-600 rounded-full border-2 border-white flex items-center justify-center hover:bg-gold-700 transition-colors">
        <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </button>
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
      <h1 class="font-display text-2xl font-bold mb-1">{{ $profile['first_name'] }} {{ $profile['middle_name'] }} {{ $profile['last_name'] }}</h1>
      <p class="text-sm text-white/70 mb-3">{{ $profile['admission_number'] }}</p>
      <div class="flex flex-wrap gap-2">
        @foreach([$profile['class'], $profile['gender'], $profile['blood_group']] as $badge)
          <span class="text-xs text-white/80 bg-white/10 border border-white/15 px-3 py-1 rounded-full">{{ $badge }}</span>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Tab nav --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden" data-tab-scope="profile">

  <div class="flex border-b border-gray-200 overflow-x-auto" data-tab-nav>
    @php
      $tabs = ['personal'=>'Personal Info','guardian'=>'Guardian Info','photo'=>'Change Photo','password'=>'Change Password'];
    @endphp
    @foreach($tabs as $key => $label)
      <button data-tab="{{ $key }}"
              class="px-4 md:px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-all flex-shrink-0
                     {{ $key==='personal' ? 'text-forest-700 border-forest-700' : 'text-gray-400 border-transparent hover:text-gray-600' }}">
        {{ $label }}
      </button>
    @endforeach
  </div>

  {{-- ── Personal Info ─────────────────────────────────── --}}
  <div data-tab-pane="personal" class="p-5 md:p-7">
    <p class="text-xs text-gray-400 mb-5 flex items-center gap-1.5">
      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Contact your class teacher to update personal information.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
      @foreach([
        'First Name'      => $profile['first_name'],
        'Last Name'       => $profile['last_name'],
        'Middle Name'     => $profile['middle_name'],
        'Admission No.'   => $profile['admission_number'],
        'Class'           => $profile['class'],
        'Date of Birth'   => date('F j, Y', strtotime($profile['date_of_birth'])),
        'Gender'          => $profile['gender'],
        'State of Origin' => $profile['state_of_origin'],
        'Religion'        => $profile['religion'],
        'Blood Group'     => $profile['blood_group'],
        'Phone'           => $profile['phone'],
        'Email'           => $profile['email'],
      ] as $label => $value)
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</p>
          <p class="text-sm font-semibold text-gray-900">{{ $value ?: '—' }}</p>
        </div>
      @endforeach
      <div class="md:col-span-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Home Address</p>
        <p class="text-sm font-semibold text-gray-900">{{ $profile['address'] }}</p>
      </div>
    </div>
  </div>

  {{-- ── Guardian Info ─────────────────────────────────── --}}
  <div data-tab-pane="guardian" class="p-5 md:p-7 hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
      @foreach([
        'Full Name'    => $guardian['name'],
        'Relationship' => $guardian['relationship'],
        'Phone'        => $guardian['phone'],
        'Alt. Phone'   => $guardian['alt_phone'],
        'Email'        => $guardian['email'],
        'Occupation'   => $guardian['occupation'],
      ] as $label => $value)
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</p>
          <p class="text-sm font-semibold text-gray-900">{{ $value ?: '—' }}</p>
        </div>
      @endforeach
      <div class="md:col-span-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Home Address</p>
        <p class="text-sm font-semibold text-gray-900">{{ $guardian['address'] }}</p>
      </div>
    </div>
  </div>

  {{-- ── Photo Upload ──────────────────────────────────── --}}
  <div data-tab-pane="photo" class="p-5 md:p-7 hidden">
    <form method="POST" action="{{ route('student.profile.photo') }}" enctype="multipart/form-data">
      @csrf
      <div class="max-w-md mx-auto">
        {{-- Preview --}}
        <div class="text-center mb-5">
          <img id="photo-preview" src="" alt="" class="w-24 h-24 rounded-full object-cover mx-auto mb-3 border-4 border-gray-200" style="display:none">
        </div>
        {{-- Drop zone --}}
        <div class="photo-zone mb-4" id="photo-zone">
          <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          <p class="text-sm font-semibold text-gray-600 mb-1">Drag & drop or click to upload</p>
          <p class="text-xs text-gray-400">JPG or PNG · Max 2MB · Passport-size photo preferred</p>
          <input type="file" id="photo-input" name="photo" accept="image/*" class="hidden">
        </div>
        <button type="submit" class="w-full py-3 bg-forest-700 text-white font-bold text-sm rounded-xl hover:bg-forest-900 transition-colors">
          Save Photo
        </button>
      </div>
    </form>
  </div>

  {{-- ── Change Password ───────────────────────────────── --}}
  <div data-tab-pane="password" class="p-5 md:p-7 hidden">
    <form method="POST" action="{{ route('student.profile.password') }}" class="max-w-md">
      @csrf
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password <span class="text-red-500">*</span></label>
        <input type="password" name="current_password" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
      </div>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password <span class="text-red-500">*</span></label>
        <input type="password" id="new_password" name="new_password" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
        {{-- Strength indicator --}}
        <div class="flex gap-1.5 mt-2">
          <div class="str-bar"></div>
          <div class="str-bar"></div>
          <div class="str-bar"></div>
          <div class="str-bar"></div>
        </div>
        <p id="str-label" class="text-xs mt-1 font-semibold text-gray-400"></p>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password <span class="text-red-500">*</span></label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
      </div>

      <button type="submit" class="w-full py-3 bg-forest-700 text-white font-bold text-sm rounded-xl hover:bg-forest-900 transition-colors">
        Update Password
      </button>
    </form>
  </div>

</div>

@endsection
