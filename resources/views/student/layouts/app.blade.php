@php
/**
 * Shared dummy data — available on every student portal page.
 * Individual pages may override these in their own @php block.
 * Replace with Auth::user() + DB queries when wiring backend.
 */
$student = $student ?? [
    'first_name'       => 'Chidinma',
    'last_name'        => 'Okafor',
    'full_name'        => 'Chidinma Okafor',
    'admission_number' => 'EXC/JSS2/2024/047',
    'class'            => 'JSS 2B',
    'photo'            => null,
];
$currentTerm         = $currentTerm         ?? 'First Term — 2024/2025';
$upcomingExamCount   = $upcomingExamCount   ?? 2;
$unreadAnnouncements = $unreadAnnouncements ?? 3;
$school              = $school              ?? (object)['name' => 'Excellence Academy'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Student Portal') — {{ $school->name }}</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            forest: {
              50:  '#f0fdf4',
              100: '#dcfce7',
              200: '#bbf7d0',
              300: '#86efac',
              500: '#22c55e',
              600: '#1a6e38',
              700: '#16582e',
              800: '#0f4a27',
              900: '#0d3b1f',
            },
            gold: {
              50:  '#fffbeb',
              100: '#fef3c7',
              400: '#fbbf24',
              500: '#f59e0b',
              600: '#d97706',
              700: '#b45309',
            },
          },
          fontFamily: {
            display: ['"Fraunces"', 'serif'],
            body:    ['"Plus Jakarta Sans"', 'sans-serif'],
          },
        }
      }
    }
  </script>

  {{-- Custom CSS --}}
  <link rel="stylesheet" href="{{ asset('assets/css/student.css') }}">

  @stack('styles')
</head>
<body class="bg-gray-100 text-gray-700 font-body antialiased">

{{-- ── Portal shell ───────────────────────────────────────── --}}
<div class="flex min-h-screen">

  {{-- Sidebar --}}
  @include('student.layouts.partials.sidebar')
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  {{-- Main --}}
  <div class="main-wrap flex-1">

    {{-- Header --}}
    @include('student.layouts.partials.header')

    {{-- Content --}}
    <main class="flex-1 p-5 md:p-6">

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="flex items-center gap-3 p-3.5 mb-5 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
          <svg class="w-4 h-4 flex-shrink-0 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="flex items-center gap-3 p-3.5 mb-5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
          <svg class="w-4 h-4 flex-shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
          {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </main>

    {{-- Footer --}}
    @include('student.layouts.partials.footer')

  </div>
</div>

{{-- Toast container --}}
<div id="toast-wrap"></div>

{{-- Scripts --}}
<script src="{{ asset('assets/js/student.js') }}"></script>
@stack('scripts')

</body>
</html>
