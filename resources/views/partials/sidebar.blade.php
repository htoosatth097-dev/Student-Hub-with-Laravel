{{-- Primary navigation. Each management link is shown only when its ability is allowed. --}}
<nav class="topnav app-sidebar" id="appSidebar" aria-label="Primary navigation">
    <div class="topnav-head">
        <a href="{{ auth()->check() ? auth()->user()->landingPath() : route('about.index') }}" class="brand-link" title="StudentHub">
            <span class="brand-symbol"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
            <span class="brand-title">StudentHub</span>
        </a>
        <button type="button" class="topnav-close" id="sidebarCloseBtn" aria-label="Close navigation menu">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    @can('viewAny', App\Models\laravelstudents::class)
        @unless(auth()->user()->isStudentAdmin())
            <a href="{{ route('students.index') }}"
               class="topnav-link {{ request()->routeIs('students.index', 'students.show', 'students.edit') || request()->is('/') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i>
                <span>Student Dashboard</span>
            </a>
        @endunless
        @can('create', App\Models\laravelstudents::class)
            <a href="{{ route('students.create') }}" class="topnav-link {{ request()->routeIs('students.create') ? 'active' : '' }}">
                <i class="bi bi-person-plus-fill" aria-hidden="true"></i><span>Add Student</span>
            </a>
        @endcan
    @endcan

    @auth
        @can('viewAny', App\Models\courses::class)
            @unless(auth()->user()->isStudentAdmin())
                <a href="{{ route('courses.index') }}" class="topnav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i><span>Courses</span>
                </a>
            @endunless
        @endcan
        @can('manage-roles')
            <a href="{{ route('admin.roles.index') }}" class="topnav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-shaded" aria-hidden="true"></i><span>Admin Roles</span>
            </a>
        @endcan
    @endauth

    @unless(auth()->check() && auth()->user()->isStudentAdmin())
        <a href="{{ route('about.index') }}" class="topnav-link {{ request()->routeIs('about.*') || request()->is('about') ? 'active' : '' }}">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i><span>About</span>
        </a>
        <a href="{{ route('contact.index') }}" class="topnav-link {{ request()->routeIs('contact.*') || request()->is('contact*') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill" aria-hidden="true"></i><span>Support</span>
        </a>
    @endunless

    @guest
        <a href="{{ route('login') }}" class="topnav-link {{ request()->routeIs('login') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill" aria-hidden="true"></i><span>Admin Login</span>
        </a>
    @endguest

    <div class="topnav-foot">StudentHub v1.3 &mdash; Academic Registry</div>
</nav>
