<header class="bg-white border-b border-gray-200 h-[60px] flex items-center px-4 md:px-6 gap-3 sticky top-0 z-[100] shadow-sm flex-shrink-0">

    {{-- Mobile hamburger --}}
    <button id="hamburger-btn" onclick="toggleSidebar()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-500 hover:bg-gray-50 flex-shrink-0 transition-colors" aria-label="Open menu">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- Page title --}}
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

    {{-- Actions --}}
    <div class="flex items-center gap-2 flex-shrink-0">

        {{-- Quick fee pay shortcut --}}
        @php $totalOwed = collect($children ?? [])->sum('fee_balance'); @endphp
        @if($totalOwed > 0)
            <a href="{{ route('parent.fees.index') }}"
               class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Pay ₦{{ number_format($totalOwed) }} due
            </a>
        @endif

        {{-- Notifications --}}
        <div class="relative dropdown">
            <button data-dropdown="notif-menu"
                    class="w-9 h-9 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-forest-700 transition-colors relative">
                <svg class="w-[17px] h-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/></svg>
                @if(($unreadAnnouncements ?? 0) > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-gold-600 rounded-full border-2 border-white"></span>
                @endif
            </button>
            <div class="dropdown-menu" id="notif-menu">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">Notifications</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $unreadAnnouncements ?? 0 }} unread</p>
                </div>
                <div class="px-4 py-3 text-sm text-gray-400 italic">No new notifications</div>
                <div class="border-t border-gray-100">
                    <a href="{{ route('parent.announcements.index') }}" class="flex px-4 py-2.5 text-sm text-forest-700 font-semibold hover:bg-gray-50 transition-colors">
                        View all →
                    </a>
                </div>
            </div>
        </div>

        {{-- Avatar --}}
        <div class="relative dropdown">
            <button data-dropdown="user-menu"
                    class="w-8 h-8 rounded-full border-2 border-gray-200 bg-forest-900 text-white flex items-center justify-center text-xs font-bold hover:border-forest-300 transition-colors overflow-hidden flex-shrink-0">
                @if($parent['photo'] ?? null)
                    <img src="{{ asset('storage/'.$parent['photo']) }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($parent['first_name'] ?? 'P', 0, 1)) }}
                @endif
            </button>
            <div class="dropdown-menu" id="user-menu">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ $parent['full_name'] ?? 'Parent' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Parent · Guardian</p>
                </div>
                <a href="{{ route('parent.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    My Profile
                </a>
                <a href="{{ route('parent.fees.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Fees & Payments
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
