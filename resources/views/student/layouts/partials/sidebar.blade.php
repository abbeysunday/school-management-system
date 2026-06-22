{{-- ╔══════════════════════════════════════════════╗
     ║  NaijaSchoolMS — Student Sidebar             ║
     ║  Variables used: $student, $currentTerm,     ║
     ║  $upcomingExamCount, $unreadAnnouncements,   ║
     ║  $school — all provided by layouts/app.blade ║
     ╚══════════════════════════════════════════════╝ --}}
<aside class="sidebar" id="sidebar">

  {{-- ── Logo ──────────────────────────────────────── --}}
  <div class="flex items-center gap-2.5 px-4 py-5 border-b border-white/[.07] flex-shrink-0">
    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-forest-700 to-forest-500 flex items-center justify-center flex-shrink-0">
      <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6L23 9 12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
    </div>
    <div>
      <div class="text-xs font-bold text-white/90 font-display leading-tight">{{ $school->name ?? 'NaijaSchoolMS' }}</div>
      <div class="text-[10px] text-white/30 uppercase tracking-widest mt-0.5">Student Portal</div>
    </div>
  </div>

  {{-- ── Student card ─────────────────────────────── --}}
  <div class="mx-3 my-3 p-3 bg-white/[.05] border border-white/[.07] rounded-xl flex items-center gap-2.5 flex-shrink-0">
    <div class="w-9 h-9 rounded-full border-2 border-white/20 bg-forest-700 text-white/70 flex items-center justify-content-center font-bold text-sm flex-shrink-0 overflow-hidden" style="align-items:center;justify-content:center;display:flex">
      @if($student['photo'] ?? null)
        <img src="{{ asset('storage/' . $student['photo']) }}" alt="" class="w-full h-full object-cover">
      @else
        {{ strtoupper(substr($student['first_name'] ?? 'S', 0, 1)) }}
      @endif
    </div>
    <div class="min-w-0">
      <div class="text-xs font-semibold text-white/90 truncate">{{ ($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '') }}</div>
      <div class="text-[11px] text-white/30 mt-0.5 truncate">{{ $student['admission_number'] ?? 'ADM/2024/001' }}</div>
    </div>
  </div>

  {{-- ── Term badge ───────────────────────────────── --}}
  <div class="mx-3 mb-3 px-3 py-1.5 bg-gold-600/10 border border-gold-600/20 rounded-lg flex items-center gap-1.5 flex-shrink-0">
    <svg class="w-3 h-3 text-gold-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    <span class="text-[11px] text-yellow-400 font-semibold truncate">{{ $currentTerm }}</span>
  </div>

  {{-- ── Navigation ───────────────────────────────── --}}
  <nav class="flex-1 pb-4">

    <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-[.12em] text-white/25">Main</div>

    <a href="{{ route('student.dashboard') }}"
       class="sb-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <a href="{{ route('student.timetable.index') }}"
       class="sb-link {{ request()->routeIs('student.timetable.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      My Timetable
    </a>

    <a href="{{ route('student.results.index') }}"
       class="sb-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Results
    </a>

    <a href="{{ route('student.attendance.index') }}"
       class="sb-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Attendance
    </a>

    <div class="px-4 py-2 mt-1 text-[10px] font-bold uppercase tracking-[.12em] text-white/25">Exams</div>

    <a href="{{ route('student.exams.index') }}"
       class="sb-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      CBT Exams
      @if(($upcomingExamCount ?? 0) > 0)
        <span class="ml-auto bg-gold-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $upcomingExamCount }}</span>
      @endif
    </a>

    <div class="px-4 py-2 mt-1 text-[10px] font-bold uppercase tracking-[.12em] text-white/25">Communication</div>

    <a href="{{ route('student.announcements.index') }}"
       class="sb-link {{ request()->routeIs('student.announcements.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
      Announcements
      @if(($unreadAnnouncements ?? 0) > 0)
        <span class="ml-auto bg-gold-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadAnnouncements }}</span>
      @endif
    </a>

    <div class="px-4 py-2 mt-1 text-[10px] font-bold uppercase tracking-[.12em] text-white/25">Account</div>

    <a href="{{ route('student.profile.index') }}"
       class="sb-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      My Profile
    </a>

  </nav>

  {{-- ── Sign out ──────────────────────────────────── --}}
  <div class="px-3 pb-4 flex-shrink-0 border-t border-white/[.07] pt-3">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sb-link rounded-lg w-full">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>
