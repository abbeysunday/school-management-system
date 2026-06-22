{{-- ╔══════════════════════════════════════════════╗
     ║  NaijaSchoolMS — Student Header              ║
     ║  Fix: @hasSection instead of nested @yield   ║
     ║  Fix: uses $student variable, no auth()      ║
     ╚══════════════════════════════════════════════╝ --}}
<header class="bg-white border-b border-gray-200 h-[60px] flex items-center px-4 md:px-6 gap-3 sticky top-0 z-[100] shadow-sm flex-shrink-0">

  {{-- Hamburger (mobile only) --}}
  <button id="hamburger-btn"
          class="md:hidden w-9 h-9 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-500 hover:bg-gray-50 flex-shrink-0 transition-colors"
          aria-label="Open sidebar">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="6" x2="21" y2="6"/>
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>

  {{-- Page title + subtitle --}}
  <div class="flex-1 min-w-0">
    <h1 class="font-display text-base font-semibold text-gray-900 truncate leading-tight">
      @yield('page-title', 'Dashboard')
    </h1>
    <p class="text-xs text-gray-400 leading-tight mt-0.5">
      @hasSection('page-sub')

        @yield('page-sub')
      @else

        {{ now()->format('l, F j, Y') }}
    @endif


    </p>
  </div>

  {{-- Right actions --}}
  <div class="flex items-center gap-2 flex-shrink-0">

    {{-- Notifications bell --}}
    <div class="relative dropdown">
      <button data-dropdown="notif-menu"
              class="hdr-btn w-9 h-9 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-forest-700 hover:border-gray-300 transition-colors relative"
              aria-label="Notifications">
        <svg class="w-[17px] h-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/>
        </svg>
        @if(($unreadAnnouncements ?? 0) > 0)
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-gold-600 rounded-full border-2 border-white"></span>
        @endif
      </button>

      <div class="dropdown-menu" id="notif-menu">
        <div class="px-4 py-3 border-b border-gray-100">
          <p class="text-sm font-semibold text-gray-900">Notifications</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ $unreadAnnouncements ?? 0 }} unread messages</p>
        </div>
        <div class="px-4 py-3 text-sm text-gray-400 italic">No new notifications</div>
        <div class="border-t border-gray-100">
          <a href="{{ route('student.announcements.index') }}" class="flex items-center px-4 py-2.5 text-sm text-forest-700 font-semibold hover:bg-gray-50 transition-colors">
            View all announcements →
          </a>
        </div>
      </div>
    </div>

    {{-- Avatar / user dropdown --}}
    <div class="relative dropdown">
      <button data-dropdown="user-menu"
              class="w-8 h-8 rounded-full border-2 border-gray-200 bg-forest-900 text-white flex items-center justify-center text-xs font-bold cursor-pointer hover:border-forest-300 transition-colors overflow-hidden flex-shrink-0"
              aria-label="Account menu">
        @if($student['photo'] ?? null)
          <img src="{{ asset('storage/' . $student['photo']) }}" alt="" class="w-full h-full object-cover">
        @else
          {{ strtoupper(substr($student['first_name'] ?? 'S', 0, 1)) }}
        @endif
      </button>

      <div class="dropdown-menu" id="user-menu">
        <div class="px-4 py-3 border-b border-gray-100">
          <p class="text-sm font-semibold text-gray-900">{{ $student['full_name'] ?? ($student['first_name'] ?? 'Student') }}</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ $student['class'] ?? 'JSS1A' }} · Student</p>
        </div>
        <a href="{{ route('student.profile.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          My Profile
        </a>
        <a href="{{ route('student.results.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          My Results
        </a>
        <div class="border-t border-gray-100 mt-1">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>
