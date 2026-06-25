<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('teacher.dashboard') }}"><i class="ti ti-layout-grid"></i><span>Dashboard</span></a>
                </li>

                <li class="{{ request()->routeIs('teacher.ca-scores.*') ? 'active' : '' }}">
                    <a href="{{ route('teacher.ca-scores.index') }}">
                        <i class="ti ti-clipboard-check"></i><span>Score Entry</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('teacher.attendance.index') ? 'active' : '' }}">
                    <a href="{{ route('teacher.attendance.index') }}">
                        <i class="ti ti-calendar-check"></i><span>Attendance</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('teacher.attendance.report') ? 'active' : '' }}">
                    <a href="{{ route('teacher.attendance.report') }}">
                        <i class="ti ti-chart-bar"></i><span>Attendance Report</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('teacher.timetable') ? 'active' : '' }}">
                    <a href="{{ route('teacher.timetable') }}">
                        <i class="ti ti-calendar"></i><span>My Timetable</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti ti-logout-2"></i><span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
