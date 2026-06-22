        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('backend/assets/img/logo.svg') }}" class="img-fluid logo" alt="Logo">
                    </a>
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('backend/assets/img/logo-small.svg') }}" class="img-fluid logo-small" alt="Logo">
                    </a>
                </div>
                <div class="siderbar-toggle">
                    <label class="switch" id="toggle_btn">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>

                        {{-- ── Home ── --}}
                        <li class="menu-title"><h6>Home</h6></li>
                        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fe fe-grid"></i> <span>Dashboard</span>
                            </a>
                        </li>

                        {{-- ── People ── --}}
                        <li class="menu-title"><h6>People</h6></li>

                        {{-- Students --}}
                        <li class="submenu {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fe fe-users"></i>
                                <span>Students</span>
                                <span class="menu-arrow"><i class="fe fe-chevron-right"></i></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('admin.students.index', 'admin.students.show', 'admin.students.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.students.index') }}">Directory</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.students.create') ? 'active' : '' }}">
                                    <a href="{{ route('admin.students.create') }}">Register Student</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.students.enrollment') ? 'active' : '' }}">
                                    <a href="{{ route('admin.students.enrollment') }}">Enrollment</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.students.import*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.students.import.form') }}">Bulk Import</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Parents --}}
                        <li class="{{ request()->routeIs('admin.parents.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.parents.index') }}">
                                <i class="fe fe-user-check"></i> <span>Parents</span>
                            </a>
                        </li>

                        {{-- ── Academic Setup ── --}}
                        <li class="menu-title"><h6>Academic Setup</h6></li>

                        {{-- Classes --}}
                        <li class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.classes.levels') }}">
                                <i class="fe fe-layers"></i> <span>Classes & Arms</span>
                            </a>
                        </li>

                        {{-- Subjects --}}
                        <li class="submenu {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fe fe-book"></i>
                                <span>Subjects</span>
                                <span class="menu-arrow"><i class="fe fe-chevron-right"></i></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('admin.subjects.index', 'admin.subjects.create', 'admin.subjects.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.subjects.index') }}">All Subjects</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.subjects.assignments') ? 'active' : '' }}">
                                    <a href="{{ route('admin.subjects.assignments') }}">Assignments</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Sessions --}}
                        <li class="{{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.sessions.index') }}">
                                <i class="fe fe-calendar"></i> <span>Academic Sessions</span>
                            </a>
                        </li>

                        {{-- Terms --}}
                        <li class="{{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.terms.index') }}">
                                <i class="fe fe-clock"></i> <span>Terms</span>
                            </a>
                        </li>

                        {{-- ── CBT ── --}}
                        <li class="menu-title"><h6>CBT</h6></li>

                        <li class="submenu {{ request()->routeIs('admin.cbt.*', 'admin.questions.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fe fe-monitor"></i>
                                <span>CBT Module</span>
                                <span class="menu-arrow"><i class="fe fe-chevron-right"></i></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('admin.cbt.exams.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.cbt.exams.index') }}">Exams</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.questions.index') }}">Question Bank</a>
                                </li>
                            </ul>
                        </li>

                        {{-- ── Fees ── --}}
                        <li class="menu-title"><h6>Fees</h6></li>

                        <li class="submenu {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fe fe-dollar-sign"></i>
                                <span>Fee Management</span>
                                <span class="menu-arrow"><i class="fe fe-chevron-right"></i></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('admin.fees.ledger', 'admin.fees.ledger.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.fees.ledger') }}">Ledger &amp; Scholarships</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.fees.categories') ? 'active' : '' }}">
                                    <a href="{{ route('admin.fees.categories') }}">Fee Categories</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.fees.structure') ? 'active' : '' }}">
                                    <a href="{{ route('admin.fees.structure') }}">Fee Structure Grid</a>
                                </li>
                            </ul>
                        </li>

                        {{-- ── Settings ── --}}
                        <li class="menu-title"><h6>Settings</h6></li>

                        <li class="{{ request()->routeIs('admin.setup.school') ? 'active' : '' }}">
                            <a href="{{ route('admin.setup.school') }}">
                                <i class="fe fe-home"></i> <span>School Profile</span>
                            </a>
                        </li>

                        <li class="submenu {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fe fe-settings"></i>
                                <span>Configurations</span>
                                <span class="menu-arrow"><i class="fe fe-chevron-right"></i></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('admin.settings.grading') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.grading') }}">Grading Scale</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.settings.ca-config') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.ca-config') }}">CA Configuration</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.settings.calendar') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.calendar') }}">School Calendar</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fe fe-log-out"></i> <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
