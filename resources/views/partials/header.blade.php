<header class="sticky top-0 z-30 flex items-center justify-between h-20 px-6 bg-white border-b border-slate-200">
    <button @click="sidebarOpen = !sidebarOpen" class="p-2 mr-4 text-slate-500 rounded-lg lg:hidden hover:bg-slate-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>

    <div class="flex items-center justify-end w-full gap-4">
        <button class="relative p-2 text-slate-400 hover:text-slate-500">
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </button>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-semibold text-slate-900">John Doe</p>
                    <p class="text-xs text-slate-500">SS3 Science • ID: 2024/001</p>
                </div>
                <img src="https://ui-avatars.com/api/?name=John+Doe&background=e0e7ff&color=4f46e5" alt="User" class="w-10 h-10 rounded-full ring-2 ring-white shadow-sm">
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-xl shadow-xl border border-slate-100 z-50">
                <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">My Profile</a>
                <a href="{{ route('student.settings') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">Settings</a>
                <hr class="my-2 border-slate-100">
                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
            </div>
        </div>
    </div>
</header>
