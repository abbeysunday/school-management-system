<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Student Portal') — {{ $school->name ?? 'NaijaSchoolMS' }}</title>
  <meta name="description" content="Student portal for {{ $school->name ?? 'NaijaSchoolMS' }}">

  {{-- Favicon --}}
  <link rel="icon" href="{{ asset('favicon.ico') }}">

  {{-- Google Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  {{-- Base student stylesheet --}}
  <link rel="stylesheet" href="{{ asset('css/student.css') }}">

  @stack('styles')
</head>
<body>

<div class="portal-shell">

  {{-- Sidebar --}}
  @include('student.layouts.partials.sidebar')

  {{-- Main --}}
  <div class="main-wrapper">

    {{-- Header --}}
    @include('student.layouts.partials.header')

    {{-- Page Content --}}
    <main class="page-content">

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="result-publish-banner" style="margin-bottom:18px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span class="result-publish-text">{{ session('success') }}</span>
        </div>
      @endif
      @if(session('error'))
        <div class="result-publish-banner" style="margin-bottom:18px;background:#fee2e2;border-color:#fca5a5">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--clr-danger)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span style="color:#991b1b;font-size:13.5px">{{ session('error') }}</span>
        </div>
      @endif

      @yield('content')
    </main>

    {{-- Footer --}}
    @include('student.layouts.partials.footer')

  </div>
</div>

{{-- Toast container (shared) --}}
<div class="toast-container" id="toast-container"></div>

{{-- Scripts --}}
<script src="{{ asset('js/student.js') }}"></script>
@stack('scripts')

</body>
</html>
