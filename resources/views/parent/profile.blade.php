@extends('parent.layouts.app')

@php
$parent   = $parent   ?? ['full_name'=>'Mr. Emmanuel Okafor','first_name'=>'Emmanuel','email'=>'e.okafor@gmail.com','phone'=>'08087654321','photo'=>null];
$children = $children ?? [
    ['id'=>1,'name'=>'Chidinma Okafor','first_name'=>'Chidinma','class'=>'JSS 2B','admission_no'=>'EXC/JSS2/2024/047','gender'=>'Female','photo'=>null,'attendance_pct'=>92,'fee_balance'=>35000,'last_avg'=>73.4,'position'=>'5th'],
    ['id'=>2,'name'=>'Emeka Okafor',   'first_name'=>'Emeka',   'class'=>'SS 1A', 'admission_no'=>'EXC/SS1/2023/019','gender'=>'Male',  'photo'=>null,'attendance_pct'=>88,'fee_balance'=>0,    'last_avg'=>81.2,'position'=>'3rd'],
];
@endphp

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-sub', 'Manage your contact information')

@section('content')

<div class="max-w-2xl">

  {{-- Hero --}}
  <div class="relative bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 rounded-2xl p-5 md:p-7 text-white mb-5 overflow-hidden">
    <div class="absolute -right-6 -top-6 w-40 h-40 rounded-full bg-white/[.04] pointer-events-none"></div>
    <div class="flex flex-wrap items-center gap-5">
      <div class="w-16 h-16 rounded-full border-4 border-white/25 bg-white/10 flex items-center justify-content-center font-display font-bold text-2xl flex-shrink-0" style="display:flex;align-items:center;justify-content:center">
        {{ strtoupper(substr($parent['first_name'],0,1)) }}
      </div>
      <div>
        <h1 class="font-display text-xl font-bold mb-1">{{ $parent['full_name'] }}</h1>
        <p class="text-sm text-white/70 mb-2">Parent · Guardian</p>
        <div class="flex flex-wrap gap-2">
          <span class="text-xs text-white/80 bg-white/10 border border-white/15 px-3 py-1 rounded-full">{{ count($children) }} {{ Str::plural('child', count($children)) }} enrolled</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Profile form --}}
  <form method="POST" action="{{ route('parent.profile.update') }}">
    @csrf
    <div class="bg-white rounded-2xl border border-gray-200 p-5 md:p-7 mb-5">
      <h3 class="font-display font-semibold text-gray-900 mb-5">Personal Information</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">First Name</label>
          <input type="text" name="first_name" value="{{ $parent['first_name'] }}"
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Last Name</label>
          <input type="text" name="last_name" value="{{ explode(' ', $parent['full_name'])[1] ?? '' }}"
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Phone Number</label>
          <input type="tel" name="phone" value="{{ $parent['phone'] }}"
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email Address</label>
          <input type="email" name="email" value="{{ $parent['email'] }}"
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-forest-700 focus:ring-2 focus:ring-forest-700/10 transition-colors">
        </div>
      </div>

      <div class="mt-5">
        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-forest-700 text-white font-bold text-sm rounded-xl hover:bg-forest-900 transition-colors">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Save Changes
        </button>
      </div>
    </div>
  </form>

  {{-- Children info (read-only) --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-5 md:p-7">
    <h3 class="font-display font-semibold text-gray-900 mb-5">Enrolled Children</h3>
    <div class="space-y-4">
      @foreach($children as $child)
        <div class="flex flex-wrap items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
          <div class="w-10 h-10 rounded-full bg-forest-900 text-white flex items-center justify-content-center font-bold flex-shrink-0" style="display:flex;align-items:center;justify-content:center">
            {{ strtoupper(substr($child['first_name'],0,1)) }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900">{{ $child['name'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $child['admission_no'] }} · {{ $child['class'] }}</p>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('parent.children.results', $child['id']) }}" class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">Results</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('parent.children.attendance', $child['id']) }}" class="text-xs font-semibold text-forest-700 hover:text-gold-600 transition-colors">Attendance</a>
          </div>
        </div>
      @endforeach
    </div>
    <p class="text-xs text-gray-400 mt-4">To add or remove a child, please contact the school registrar's office.</p>
  </div>

</div>

@endsection
