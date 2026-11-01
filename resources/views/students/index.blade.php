@extends('layout.app')

@section('title', 'Student Directory | StudentHub')

@section('content')
@php
    $totalCount         = $dashboardStats['totalCount'] ?? (isset($students) ? $students->total() : 0);
    $uniqueCoursesCount = $dashboardStats['uniqueCoursesCount'] ?? (isset($courses) ? $courses->count() : 0);
    $maleCount          = $dashboardStats['maleCount'] ?? 0;
    $femaleCount        = $dashboardStats['femaleCount'] ?? 0;
    $byCourse           = $dashboardStats['byCourse'] ?? collect();

    $avatarGradients = [
        'linear-gradient(135deg, #0d9488 0%, #14b8a6 100%)',
        'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
        'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)',
        'linear-gradient(135deg, #d97706 0%, #fbbf24 100%)',
        'linear-gradient(135deg, #e11d48 0%, #f43f5e 100%)',
        'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)',
    ];

    $topCourses = $courses->take(4);
    $courseBadges = [
        ['bg' => '#f59e0b', 'color' => '#ffffff'],
        ['bg' => '#f43f5e', 'color' => '#ffffff'],
        ['bg' => '#0d9488', 'color' => '#ffffff'],
        ['bg' => '#0284c7', 'color' => '#ffffff'],
    ];

    $adminUsers = \App\Models\User::whereIn('role', \App\Models\User::ADMIN_ROLES)->take(4)->get();
    if ($adminUsers->isEmpty()) {
        $adminUsers = \App\Models\User::take(4)->get();
    }
@endphp

<section class="students-index-page animate-fade-in">

    @include('partials.flash')

    {{-- Top LMS Hero Grid --}}
    <div class="lms-hero-grid animate-fade-up">
        {{-- Left Large Teal Banner --}}
        <div class="lms-banner-card">
            <div class="lms-banner-left">
                <span class="lms-banner-eyebrow">
                    <i class="bi bi-stars me-1"></i> Academic Workspace
                </span>
                <h1 class="lms-banner-title">Learn Effectively With Us!</h1>
                <p class="lms-banner-sub">Real-time visibility across academic programs, student enrollments, and institutional records.</p>
                <div class="lms-banner-pills">
                    <div class="lms-stat-pill">
                        <span class="lms-pill-icon red-icon"><i class="bi bi-mortarboard-fill"></i></span>
                        <div class="lms-pill-text">
                            <span class="lms-pill-label">Students</span>
                            <span class="lms-pill-val">{{ number_format($totalCount) }}+</span>
                        </div>
                    </div>
                    <div class="lms-stat-pill">
                        <span class="lms-pill-icon yellow-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                        <div class="lms-pill-text">
                            <span class="lms-pill-label">Active Programs</span>
                            <span class="lms-pill-val">{{ number_format($courses->count()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lms-banner-art-wrap" aria-hidden="true">
                <svg class="lms-graduates-svg" viewBox="0 0 320 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="28" cy="30" r="3" fill="#fde68a" opacity="0.8" />
                    <circle cx="80" cy="18" r="2.5" fill="#ffffff" opacity="0.7" />
                    <circle cx="160" cy="22" r="3.5" fill="#fde68a" opacity="0.9" />
                    <circle cx="290" cy="45" r="3" fill="#ffffff" opacity="0.8" />
                    <path d="M40 70 L46 76 M280 80 L288 86 M140 10 L146 16" stroke="#fde68a" stroke-width="2" stroke-linecap="round" />

                    <!-- Graduate 1 (Left) -->
                    <ellipse cx="68" cy="52" rx="10" ry="12" fill="#fed7aa" />
                    <path d="M59 50 C59 40 77 40 77 50" fill="#451a03" />
                    <polygon points="68,26 90,36 68,44 46,36" fill="#0f172a" />
                    <polygon points="68,36 82,42 68,46 54,42" fill="#1e293b" />
                    <circle cx="68" cy="35" r="2" fill="#f59e0b" />
                    <path d="M68 35 Q82 40 84 52" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round" />
                    <circle cx="84" cy="53" r="2" fill="#f59e0b" />
                    <path d="M58 64 L40 102 L52 140 L76 138 L92 98 L78 64 Z" fill="#0f172a" />
                    <path d="M62 64 L66 102 L70 102 L74 64 Z" fill="#f59e0b" />
                    <path d="M58 68 L36 48" stroke="#0f172a" stroke-width="7" stroke-linecap="round" />
                    <rect x="26" y="42" width="16" height="5" rx="2" fill="#ffffff" transform="rotate(-30 26 42)" />
                    <rect x="32" y="40" width="4" height="6" fill="#e11d48" transform="rotate(-30 32 40)" />
                    <path d="M56 138 L48 165 L40 162" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M72 136 L86 158 L98 152" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <ellipse cx="38" cy="163" rx="5" ry="3" fill="#334155" />
                    <ellipse cx="100" cy="153" rx="5" ry="3" fill="#334155" />

                    <!-- Graduate 2 (Center) -->
                    <ellipse cx="160" cy="48" rx="10" ry="12" fill="#fcd34d" />
                    <path d="M151 46 C151 36 169 36 169 46" fill="#172554" />
                    <polygon points="160,22 184,32 160,40 136,32" fill="#0f172a" />
                    <polygon points="160,32 176,38 160,42 144,38" fill="#1e293b" />
                    <circle cx="160" cy="31" r="2" fill="#f59e0b" />
                    <path d="M160 31 Q178 36 180 48" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round" />
                    <path d="M150 60 L132 110 L148 144 L172 144 L188 110 L170 60 Z" fill="#0f172a" />
                    <path d="M154 60 L157 106 L163 106 L166 60 Z" fill="#f59e0b" />
                    <path d="M150 66 L128 38" stroke="#0f172a" stroke-width="7" stroke-linecap="round" />
                    <path d="M170 66 L194 40" stroke="#0f172a" stroke-width="7" stroke-linecap="round" />
                    <circle cx="126" cy="36" r="4" fill="#fcd34d" />
                    <circle cx="196" cy="38" r="4" fill="#fcd34d" />
                    <path d="M148 144 L138 168 L146 172" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M172 144 L182 168 L174 172" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <ellipse cx="147" cy="173" rx="5" ry="3" fill="#334155" />
                    <ellipse cx="173" cy="173" rx="5" ry="3" fill="#334155" />

                    <!-- Graduate 3 (Right) -->
                    <ellipse cx="250" cy="54" rx="10" ry="12" fill="#fed7aa" />
                    <path d="M241 52 C241 42 259 42 259 52" fill="#78350f" />
                    <polygon points="250,28 272,38 250,46 228,38" fill="#0f172a" />
                    <polygon points="250,38 264,44 250,48 236,44" fill="#1e293b" />
                    <circle cx="250" cy="37" r="2" fill="#f59e0b" />
                    <path d="M250 37 Q236 42 234 54" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round" />
                    <path d="M240 66 L224 106 L238 142 L264 140 L278 102 L260 66 Z" fill="#0f172a" />
                    <path d="M245 66 L248 104 L253 104 L256 66 Z" fill="#f59e0b" />
                    <path d="M260 70 L284 50" stroke="#0f172a" stroke-width="7" stroke-linecap="round" />
                    <rect x="282" y="44" width="16" height="5" rx="2" fill="#ffffff" transform="rotate(30 282 44)" />
                    <rect x="288" y="46" width="4" height="6" fill="#e11d48" transform="rotate(30 288 46)" />
                    <path d="M240 70 L226 88" stroke="#0f172a" stroke-width="6" stroke-linecap="round" />
                    <path d="M242 142 L234 166 L226 163" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M260 140 L270 165 L278 162" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <ellipse cx="224" cy="164" rx="5" ry="3" fill="#334155" />
                    <ellipse cx="280" cy="163" rx="5" ry="3" fill="#334155" />
                </svg>
            </div>
        </div>

        {{-- Right Side Action & Metrics Card --}}
        <div class="lms-action-card">
            <div>
                <h3 class="lms-action-head">Ready to expand the registry?</h3>
                <p class="lms-action-sub">Register new candidates or manage academic programs in one click.</p>
                <div class="lms-action-btns">
                    <a href="{{ route('students.create') }}" class="lms-btn-action hover-lift">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Enroll New Student</span>
                    </a>
                    @if(auth()->user()->canManageCourses())
                        <a href="{{ route('courses.index') }}" class="lms-btn-action-secondary hover-lift" title="Manage Courses">
                            <i class="bi bi-plus-circle"></i>
                            <span>New Course</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="lms-quick-two">
                <div class="lms-tile-box">
                    <span class="lms-tile-label">Courses in Progress</span>
                    <span class="lms-tile-num">{{ $uniqueCoursesCount }}</span>
                </div>
                <div class="lms-tile-box">
                    <span class="lms-tile-label">Total Enrolled</span>
                    <span class="lms-tile-num">{{ $totalCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 3-Column LMS Middle Section (Popular Courses, Current Activity, Best Instructors) --}}
    <div class="lms-three-col-grid animate-fade-up">

        {{-- Column 1: Popular Courses --}}
        <div class="lms-panel-card">
            <div class="lms-card-head">
                <div>
                    <h3 class="lms-card-title">Popular Courses</h3>
                    <div class="lms-card-subtitle">By active student enrollment</div>
                </div>
                @if(auth()->user()->canManageCourses())
                    <a href="{{ route('courses.index') }}" class="lms-card-link">View All</a>
                @else
                    <span class="lms-card-link-muted">Top Picks</span>
                @endif
            </div>

            <div class="lms-courses-list">
                @forelse($topCourses as $idx => $c)
                    @php
                        $badgeDef = $courseBadges[$idx % count($courseBadges)];
                        $cCount = (int) $byCourse->get($c->course_name, 0);
                    @endphp
                    <div class="lms-course-item">
                        <div class="lms-course-badge" style="background-color: {{ $badgeDef['bg'] }}; color: {{ $badgeDef['color'] }};">
                            {{ $c->badgeLetter() }}
                        </div>
                        <div class="lms-course-info">
                            <h4 class="lms-course-name" title="{{ $c->course_name }}">{{ $c->course_name }}</h4>
                            <span class="lms-course-meta">
                                <i class="bi bi-people-fill me-1"></i>{{ $cCount }} {{ \Illuminate\Support\Str::plural('Student', $cCount) }}
                            </span>
                        </div>
                        <div class="lms-course-actions">
                            <a href="{{ route('students.index', ['course_id' => $c->id]) }}#studentList" class="lms-btn-pill-subtle">Filter</a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted p-3 text-center">No courses available.</div>
                @endforelse
            </div>
        </div>

        {{-- Column 2: Current Activity (Spline Progress Chart + 2 Vibrant Cards) --}}
        <div class="lms-panel-card lms-activity-panel">
            <div class="lms-card-head">
                <div>
                    <h3 class="lms-card-title">Current Activity</h3>
                    <div class="lms-card-subtitle">Monthly Enrollment Progress</div>
                </div>
                <span class="lms-calendar-btn" title="Academic Year {{ date('Y') }}">
                    <i class="bi bi-calendar4-week"></i>
                </span>
            </div>

            {{-- Spline Progress SVG Chart --}}
            <div class="lms-chart-box">
                <div class="lms-spline-wrap">
                    <svg class="lms-spline-svg" viewBox="0 0 380 120" preserveAspectRatio="none" role="img" aria-label="Monthly activity spline chart">
                        <defs>
                            <linearGradient id="lmsSplineGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0d9488" stop-opacity="0.30"/>
                                <stop offset="100%" stop-color="#0d9488" stop-opacity="0.01"/>
                            </linearGradient>
                        </defs>
                        <line x1="10" y1="20" x2="370" y2="20" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3,3" />
                        <line x1="10" y1="50" x2="370" y2="50" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3,3" />
                        <line x1="10" y1="80" x2="370" y2="80" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3,3" />
                        <line x1="10" y1="110" x2="370" y2="110" stroke="#e2e8f0" stroke-width="1" />

                        <path d="M 15 95 Q 60 70 100 85 T 180 50 T 260 65 T 330 25 L 365 35 L 365 110 L 15 110 Z" fill="url(#lmsSplineGrad)" />
                        <path d="M 15 95 Q 60 70 100 85 T 180 50 T 260 65 T 330 25 L 365 35" fill="none" stroke="#0d9488" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                        <circle cx="330" cy="25" r="4.5" fill="#0d9488" stroke="#ffffff" stroke-width="2.5" />
                        <rect x="292" y="2" width="76" height="18" rx="9" fill="#0f172a" />
                        <text x="330" y="14" fill="#ffffff" font-size="9" font-family="sans-serif" font-weight="600" text-anchor="middle">100% Active</text>
                    </svg>
                </div>
                <div class="lms-chart-x-labels">
                    <span>Jan</span>
                    <span>Mar</span>
                    <span>May</span>
                    <span>Jul</span>
                    <span>Sep</span>
                    <span>Nov</span>
                </div>
            </div>

            {{-- 2 Vibrant Metric Cards at Bottom of Col 2 --}}
            <div class="lms-vibrant-tiles">
                <div class="lms-vibrant-tile tile-amber">
                    <div class="vibrant-tile-top">
                        <span class="vibrant-num">{{ $totalCount }}</span>
                        <span class="vibrant-icon-circle">
                            <i class="bi bi-people-fill"></i>
                        </span>
                    </div>
                    <div class="vibrant-tile-bottom">
                        <div class="vibrant-title">Enrolled Students</div>
                        <div class="vibrant-sub">{{ $femaleCount }}F &middot; {{ $maleCount }}M registered</div>
                    </div>
                </div>

                <div class="lms-vibrant-tile tile-rose">
                    <div class="vibrant-tile-top">
                        <span class="vibrant-num">{{ $courses->count() }}</span>
                        <span class="vibrant-icon-circle">
                            <i class="bi bi-journal-check"></i>
                        </span>
                    </div>
                    <div class="vibrant-tile-bottom">
                        <div class="vibrant-title">Active Modules</div>
                        <div class="vibrant-sub">Open for registration</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Column 3: Administrative Team / Best Instructors --}}
        <div class="lms-panel-card">
            <div class="lms-card-head">
                <div>
                    <h3 class="lms-card-title">Administrative Team</h3>
                    <div class="lms-card-subtitle">Authorized personnel</div>
                </div>
                @can('manage-roles')
                    <a href="{{ route('admin.roles.index') }}" class="lms-card-link">Manage</a>
                @else
                    <a href="#studentList" class="lms-card-link">Directory</a>
                @endcan
            </div>

            <div class="lms-instructors-list">
                @foreach($adminUsers as $idx => $admin)
                    @php
                        $grad = $avatarGradients[$idx % count($avatarGradients)];
                    @endphp
                    <div class="lms-instructor-item">
                        <div class="lms-instructor-avatar" style="background: {{ $grad }};">
                            {{ $admin->initials('AD') }}
                        </div>
                        <div class="lms-instructor-info">
                            <h4 class="lms-instructor-name" title="{{ $admin->name }}">{{ $admin->name }}</h4>
                            <span class="lms-instructor-meta text-truncate d-block">{{ $admin->email }}</span>
                        </div>
                        <div class="lms-instructor-action">
                            <span class="role-pill {{ $admin->roleBadgeClass() }}" style="font-size: 10.5px; padding: 3px 9px;">
                                {{ $admin->roleLabel() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Student Directory Table Card --}}
    <div class="list-card animate-fade-up" id="studentList">
        <div class="list-card-head student-directory-head">
            <div class="d-flex align-items-center gap-2">
                <h2 class="dash-card-title">Student Directory</h2>
                <span class="students-count-badge">{{ $students->total() }}</span>
            </div>

            <div class="list-card-tools student-directory-tools">
                <form method="GET" action="{{ route('students.index') }}" class="student-directory-filters">
                    <div class="directory-filter-field directory-search-box">
                        <i class="bi bi-search filter-search-icon" aria-hidden="true"></i>
                        <input
                            type="search"
                            name="search"
                            id="studentSearchInput"
                            class="form-control"
                            value="{{ request()->filled('search') ? request('search') : request('q') }}"
                            placeholder="Search by name, ID or email..."
                            aria-label="Search students by name or code"
                        >
                    </div>
                    <div class="directory-filter-field">
                        <select name="course_id" id="courseFilterSelect" class="form-select" aria-label="Filter by course" onchange="this.form.submit()">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="directory-filter-field">
                        <select name="gender" id="genderFilter" class="form-select" aria-label="Filter by gender" onchange="this.form.submit()">
                            <option value="">All Genders</option>
                            @foreach(\App\Models\laravelstudents::GENDERS as $gOption)
                                <option value="{{ $gOption }}" @selected(request('gender') === $gOption)>{{ $gOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="directory-filter-field">
                        <select name="sort" id="sortFilter" class="form-select" aria-label="Sort students" onchange="this.form.submit()">
                            @foreach(\App\Models\laravelstudents::SORT_OPTIONS as $value => $label)
                                <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="directory-filter-actions">
                        <button type="submit" class="btn-primary-custom hover-lift" title="Apply filters">
                            <i class="bi bi-funnel-fill me-1" aria-hidden="true"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->filled('search') || request()->filled('q') || request()->filled('course_id') || request()->filled('gender') || (request()->filled('sort') && request('sort') !== 'newest'))
                            <a href="{{ route('students.index') }}" class="btn-cancel-link hover-lift" title="Reset all filters">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                                <span>Clear</span>
                            </a>
                        @endif
                    </div>
                </form>
                @can('export', App\Models\laravelstudents::class)
                    <button type="button" class="btn-cancel-link directory-export-btn hover-lift" id="exportCsvBtn">
                        <i class="bi bi-download me-1" aria-hidden="true"></i>
                        <span>Export CSV</span>
                    </button>
                @endcan
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="admin-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th scope="col">Student</th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Course</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        @forelse($students as $index => $student)
                            @php
                                $code     = $student->student_code ?? '—';
                                $name     = $student->student_name ?? 'Unnamed';
                                $email    = $student->email ?? '—';
                                $phone    = $student->phone_number ?? null;
                                $course   = $student->course?->course_name ?? 'N/A';
                                $gender   = $student->gender ?? 'Not specified';
                                $initials = $student->initials('S');
                                $gradient = $avatarGradients[$index % count($avatarGradients)];
                            @endphp
                            <tr class="student-row"
                                data-name="{{ strtolower($name) }}"
                                data-code="{{ strtolower($code) }}"
                                data-email="{{ strtolower($email) }}"
                                data-phone="{{ $phone }}"
                                data-course="{{ strtolower($course) }}"
                                data-gender="{{ strtolower($gender) }}"
                                data-export-name="{{ $name }}"
                                data-export-code="{{ $code }}"
                                data-export-email="{{ $email }}"
                                data-export-course="{{ $course }}"
                                data-export-gender="{{ $gender }}">
                                <td data-label="Student">
                                    <div class="student-identity-cell">
                                        <div class="student-avatar" style="background: {{ $gradient }};" aria-hidden="true">{{ $initials }}</div>
                                        <div class="student-identity-meta">
                                            <a href="{{ route('students.show', $student->id) }}" class="cell-student-name">{{ $name }}</a>
                                            <div class="student-subtext"><span>{{ $gender }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Student ID">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="cell-code" title="Student ID"><i class="bi bi-hash"></i>{{ $code }}</span>
                                        <button type="button" class="btn-copy-code" data-code="{{ $code }}" title="Copy student ID">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td data-label="Email">
                                    @if($email !== '—')
                                        <a href="mailto:{{ $email }}" class="cell-email" title="Email {{ $name }}">
                                            <i class="bi bi-envelope-at" aria-hidden="true"></i>
                                            <span>{{ $email }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Phone">
                                    @if($phone)
                                        <span class="cell-phone">{{ $phone }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Course">
                                    <span class="course-badge" title="{{ $course }}"><i class="bi bi-mortarboard me-1"></i>{{ $course }}</span>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge" title="Active enrollment">
                                        <span class="status-dot"></span>
                                        <span>Active</span>
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="action-buttons-group">
                                        <a href="{{ route('students.show', $student->id) }}" class="table-action-btn view-btn" title="View profile" aria-label="View profile">
                                            <i class="bi bi-eye-fill" aria-hidden="true"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="{{ route('students.edit', $student->id) }}" class="table-action-btn edit-btn" title="Edit student" aria-label="Edit student">
                                            <i class="bi bi-pencil-fill" aria-hidden="true"></i>
                                            <span>Edit</span>
                                        </a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete student {{ addslashes($name) }} ({{ $code }})?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="table-action-btn delete-btn" title="Delete student" aria-label="Delete student">
                                                <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                                    <strong>No students found matching your criteria.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="list-card-foot">
            <span>Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students</span>
            @if($students->hasPages())
                <div class="pagination-inline">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.getElementById('studentsTableBody');
        const exportBtn = document.getElementById('exportCsvBtn');

        if (exportBtn && tableBody) {
            exportBtn.addEventListener('click', function () {
                const esc = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
                const lines = [['Student ID', 'Name', 'Email', 'Phone', 'Course', 'Gender'].map(esc).join(',')];

                tableBody.querySelectorAll('.student-row').forEach(function (row) {
                    if (row.style.display === 'none') return;
                    const d = row.dataset;
                    lines.push([d.exportCode, d.exportName, d.exportEmail, d.phone, d.exportCourse, d.exportGender].map(esc).join(','));
                });

                const blob = new Blob(['\ufeff' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href = url;
                a.download = 'students.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                if (window.showToast) {
                    window.showToast('Exported student records to CSV', 'bi-file-earmark-spreadsheet-fill text-teal');
                }
            });
        }
    });
</script>
@endpush
