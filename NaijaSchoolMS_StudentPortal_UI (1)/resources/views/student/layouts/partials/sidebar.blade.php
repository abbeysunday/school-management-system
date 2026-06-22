{{-- Student Portal Sidebar --}}
<aside class="sidebar" id="sidebar">

  {{-- Logo --}}
  <div class="sidebar-logo">
    <div class="sidebar-logo-mark">
      <svg viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6L23 9 12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
    </div>
    <div>
      <div class="sidebar-school-name">{{ $school->name ?? 'NaijaSchoolMS' }}</div>
      <div class="sidebar-school-sub">Student Portal</div>
    </div>
  </div>

  {{-- Student info card --}}
  <div class="sidebar-student-card">
    <div class="sidebar-avatar">
      @if($student['photo'] ?? null)
        <img src="{{ asset('storage/' . $student['photo']) }}" alt="Photo">
      @else
        {{ strtoupper(substr($student['first_name'] ?? 'S', 0, 1)) }}
      @endif
    </div>
    <div>
      <div class="sidebar-student-name">{{ ($student['first_name'] ?? 'Student') . ' ' . ($student['last_name'] ?? '') }}</div>
      <div class="sidebar-student-id">{{ $student['admission_number'] ?? 'ADM/2024/001' }}</div>
    </div>
  </div>

  {{-- Term badge --}}
  <div class="sidebar-term-badge">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    {{ $currentTerm ?? 'First Term 2024/2025' }}
  </div>

  {{-- Nav --}}
  <nav class="sidebar-nav">

    <div class="sidebar-section-label">Main</div>

    <a href="{{ route('student.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
      </svg>
      Dashboard
    </a>

    <a href="{{ route('student.timetable.index') }}"
       class="sidebar-link {{ request()->routeIs('student.timetable.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      My Timetable
    </a>

    <a href="{{ route('student.results.index') }}"
       class="sidebar-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
      </svg>
      Results
    </a>

    <div class="sidebar-section-label">Exams</div>

    <a href="{{ route('student.exams.index') }}"
       class="sidebar-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      CBT Exams
      @if(($upcomingExamCount ?? 0) > 0)
        <span class="nav-badge">{{ $upcomingExamCount }}</span>
      @endif
    </a>

    <div class="sidebar-section-label">Communication</div>

    <a href="{{ route('student.announcements.index') }}"
       class="sidebar-link {{ request()->routeIs('student.announcements.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/>
      </svg>
      Announcements
      @if(($unreadAnnouncements ?? 0) > 0)
        <span class="nav-badge">{{ $unreadAnnouncements }}</span>
      @endif
    </a>

    <a href="{{ route('student.attendance.index') }}"
       class="sidebar-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
      </svg>
      Attendance
    </a>

    <div class="sidebar-section-label">Account</div>

    <a href="{{ route('student.profile.index') }}"
       class="sidebar-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
      My Profile
    </a>

  </nav>

  {{-- Footer / logout --}}
  <div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sidebar-link" style="border-radius:var(--radius-sm)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebar-overlay"></div>
