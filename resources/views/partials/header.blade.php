{{-- Top bar: brand, primary navigation (partials.sidebar), search and user. --}}
<header class="app-topbar">
    <button type="button" class="header-menu-toggle" id="sidebarToggle"
            aria-label="Open navigation menu" aria-controls="appSidebar" aria-expanded="false">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <a href="{{ auth()->check() ? auth()->user()->landingPath() : route('about.index') }}" class="brand-link" title="StudentHub Academic Information System">
        <span class="brand-symbol"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
        <span class="brand-title">StudentHub</span>
    </a>

    @include('partials.sidebar')

    <div class="topbar-right">
        @auth
            @can('viewAny', App\Models\laravelstudents::class)
                @unless(auth()->user()->isStudentAdmin())
                <form class="topbar-search" action="{{ route('students.index') }}" method="GET" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" name="q" id="globalSearchInput" value="{{ request('q') }}"
                           placeholder="Search students..." aria-label="Search students" autocomplete="off">
                    <kbd aria-hidden="true">/</kbd>
                </form>
                @endunless
            @endcan

            @php
                $currentUser = auth()->user();
                $uName = $currentUser->name ?? 'User';
                $uInitials = $currentUser->initials('AD');
                $roleLabel = $currentUser->roleLabel();
                $showSubRole = strcasecmp(trim($uName), trim($roleLabel)) !== 0;
            @endphp

            <div class="topbar-action-icons d-none d-lg-flex align-items-center">
                <button type="button" class="topbar-icon-btn" id="themeToggleBtn" title="Toggle Dark / Light Theme" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
                </button>
            </div>

            <div class="topbar-user-dropdown dropdown">
                <button class="topbar-user btn-dropdown-toggle" type="button" id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Signed in as {{ $uName }} ({{ $currentUser->email }})">
                    <span class="topbar-user-avatar" aria-hidden="true">{{ $uInitials }}</span>
                    <span class="topbar-user-text">
                        <span class="topbar-user-name">{{ $uName }}</span>
                        @if($showSubRole)
                            <span class="topbar-user-sub-role">{{ $roleLabel }}</span>
                        @endif
                    </span>
                    <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 0.72rem;" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm topbar-dropdown-menu" aria-labelledby="topbarUserDropdown">
                    <li class="px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $uName }}</span>
                            <span class="role-pill {{ $currentUser->roleBadgeClass() }}" style="font-size: 10px; padding: 2px 7px;">{{ $roleLabel }}</span>
                        </div>
                        <div class="text-muted text-truncate mt-1" style="font-size: 11.5px;">{{ $currentUser->email }}</div>
                    </li>
                    @can('viewAny', App\Models\laravelstudents::class)
                        @unless($currentUser->isStudentAdmin())
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('students.index') }}" style="font-size: 13px;">
                                <i class="bi bi-grid-1x2-fill text-teal"></i><span>Student Dashboard</span>
                            </a>
                        </li>
                        @endunless
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('students.create') }}" style="font-size: 13px;">
                                <i class="bi bi-person-plus-fill text-teal"></i><span>Enroll New Student</span>
                            </a>
                        </li>
                    @endcan
                    @if($currentUser->canManageCourses())
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('courses.index') }}" style="font-size: 13px;">
                                <i class="bi bi-journal-bookmark-fill text-indigo"></i><span>Manage Courses</span>
                            </a>
                        </li>
                    @endif
                    @can('manage-roles')
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger fw-semibold" href="{{ route('admin.roles.index') }}" style="font-size: 13px;">
                                <i class="bi bi-shield-shaded"></i><span>Admin Role Management</span>
                            </a>
                        </li>
                    @endcan
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-primary-custom hover-lift" style="text-decoration: none; padding: 7px 16px; font-size: 13px;">
                <i class="bi bi-shield-lock-fill me-1" aria-hidden="true"></i>
                <span>Admin Login</span>
            </a>
        @endauth
    </div>
</header>
