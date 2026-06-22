{{-- Student Portal Header --}}
<header class="portal-header">

  {{-- Hamburger (mobile) --}}
  <button class="header-hamburger" aria-label="Toggle menu">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="6" x2="21" y2="6"/>
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>

  {{-- Page title --}}
  <div class="header-breadcrumb">
    <div class="header-page-title">@yield('page-title', 'Dashboard')</div>
    <div class="header-page-sub">@yield('page-sub', @yield('page-subtitle', date('l, F j, Y')))</div>
  </div>

  {{-- Actions --}}
  <div class="header-actions">

    {{-- Notifications --}}
    <div class="dropdown">
      <button class="header-icon-btn" data-dropdown="notif-menu" aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/>
        </svg>
        @if(($unreadAnnouncements ?? 0) > 0)
          <span class="notif-dot"></span>
        @endif
      </button>
      <div class="dropdown-menu" id="notif-menu">
        <div class="dropdown-user">
          <div class="dropdown-user-name">Notifications</div>
          <div class="dropdown-user-role">{{ $unreadAnnouncements ?? 0 }} unread</div>
        </div>
        @forelse($recentNotifications ?? [] as $notif)
          <a href="{{ route('student.announcements.index') }}" class="dropdown-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--clr-primary)">
              <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/>
            </svg>
            {{ Str::limit($notif['title'] ?? '', 35) }}
          </a>
        @empty
          <div class="dropdown-item" style="color:var(--text-dim);cursor:default">No new notifications</div>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="{{ route('student.announcements.index') }}" class="dropdown-item">View all →</a>
      </div>
    </div>

    {{-- Avatar / user menu --}}
    <div class="dropdown">
      <div class="header-avatar" data-dropdown="user-menu" style="cursor:pointer">
        @if(auth()->user()?->photo)
          <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="">
        @else
          {{ strtoupper(substr(auth()->user()?->first_name ?? 'S', 0, 1)) }}
        @endif
      </div>
      <div class="dropdown-menu" id="user-menu">
        <div class="dropdown-user">
          <div class="dropdown-user-name">{{ auth()->user()?->full_name ?? ($student['full_name'] ?? 'Student') }}</div>
          <div class="dropdown-user-role">{{ $student['class'] ?? 'JSS1A' }} &mdash; Student</div>
        </div>
        <a href="{{ route('student.profile.index') }}" class="dropdown-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          My Profile
        </a>
        <a href="{{ route('student.results.index') }}" class="dropdown-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          My Results
        </a>
        <div class="dropdown-divider"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sign Out
          </button>
        </form>
      </div>
    </div>

  </div>
</header>
