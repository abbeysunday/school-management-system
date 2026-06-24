@php
$user = auth()->user();
$parent = [
    'full_name'  => $user->full_name,
    'first_name' => $user->first_name,
    'email'      => $user->email,
    'phone'      => $user->phone,
    'photo'      => $user->photo,
];
$children = $user->children()->with(['user', 'currentEnrollment.classArm.classLevel'])->get()->map(fn($c) => [
    'id'             => $c->id,
    'name'           => $c->user->full_name,
    'first_name'     => $c->user->first_name,
    'class'          => $c->currentEnrollment?->classArm?->full_name ?? 'N/A',
    'admission_no'   => $c->admission_number,
    'gender'         => $c->gender,
    'photo'          => $c->user->photo,
    'attendance_pct' => 92,
    'fee_balance'    => \App\Models\StudentFeeLedger::where('student_id', $c->id)
                            ->where('term_id', \App\Models\Term::getCurrent()?->id)
                            ->sum('net_amount')
                      - \App\Models\StudentFeeLedger::where('student_id', $c->id)
                            ->where('term_id', \App\Models\Term::getCurrent()?->id)
                            ->sum('amount_paid'),
    'last_avg'       => 73.4,
    'position'       => '5th',
])->toArray();

$activeChild       = $children[0] ?? null;
$unreadAnnouncements = $unreadAnnouncements ?? 0;
$currentTerm       = \App\Models\Term::getCurrent();
$currentTermLabel  = $currentTerm ? $currentTerm->name . ' — ' . $currentTerm->session->name : 'Current Term';
$school            = \App\Models\SchoolProfile::first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Parent Portal') — {{ $school?->name ?? 'School Portal' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',500:'#22c55e',600:'#1a6e38',700:'#16582e',800:'#0f4a27',900:'#0d3b1f' },
                        gold:   { 50:'#fffbeb',100:'#fef3c7',400:'#fbbf24',500:'#f59e0b',600:'#d97706',700:'#b45309' },
                    },
                    fontFamily: {
                        display: ['"Fraunces"','serif'],
                        body:    ['"Plus Jakarta Sans"','sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/parent.css') }}">
    <style>
        /* Critical layout styles in case parent.css is missing */
        .sidebar { position: fixed; inset-y: 0; left: 0; width: 16rem; background: #0d3b1f; z-index: 50; display: flex; flex-direction: column; transform: translateX(-100%); transition: transform 0.3s; }
        @media (min-width: 1024px) { .sidebar { position: sticky; transform: translateX(0) !important; } }
        .sidebar-open { transform: translateX(0) !important; }
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 40; display: none; }
        .main-wrap { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .sb-link { display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 1rem; margin: 0 0.5rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: rgba(255,255,255,0.55); transition: all 0.15s; }
        .sb-link:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.9); }
        .sb-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .dropdown { position: relative; }
        .dropdown-menu { position: absolute; right: 0; top: calc(100% + 0.5rem); width: 14rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); opacity: 0; pointer-events: none; transform: translateY(-4px); transition: all 0.15s; z-index: 50; }
        .dropdown-menu.show { opacity: 1; pointer-events: auto; transform: translateY(0); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-700 font-body antialiased">

<div class="flex min-h-screen">

    @include('parent.layouts.partials.sidebar')
    <div class="sidebar-overlay lg:hidden" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="main-wrap">
        @include('parent.layouts.partials.header')

        <main class="flex-1 p-5 md:p-6">

            @if(session('success'))
                <div class="flex items-center gap-3 p-3.5 mb-5 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 p-3.5 mb-5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="flex items-center gap-3 p-3.5 mb-5 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/></svg>
                    {{ session('info') }}
                </div>
            @endif

            
        </main>

        @include('parent.layouts.partials.footer')
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('sidebar-open');
    const ov = document.getElementById('sidebar-overlay');
    ov.style.display = ov.style.display === 'block' ? 'none' : 'block';
}

// Dropdown toggles
document.querySelectorAll('[data-dropdown]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = document.getElementById(this.dataset.dropdown);
        const isOpen = menu.classList.contains('show');
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        if (!isOpen) menu.classList.add('show');
    });
});
document.addEventListener('click', () => document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show')));
</script>
@stack('scripts')
</body>
</html>
