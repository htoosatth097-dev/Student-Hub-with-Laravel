@extends('layout.app')

@section('title', 'Admin Role Management | StudentHub')

@section('content')
@php
    $roleLabels = \App\Models\User::ROLE_LABELS;

    $avatarGradients = [
        'linear-gradient(135deg, #e11d48 0%, #f43f5e 100%)',
        'linear-gradient(135deg, #0d9488 0%, #14b8a6 100%)',
        'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
        'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)',
        'linear-gradient(135deg, #d97706 0%, #fbbf24 100%)',
        'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)',
    ];
@endphp

<section class="admin-roles-page animate-fade-in">

    {{-- Page Heading --}}
    <div class="page-header-row animate-fade-up">
        <div>
            <div class="section-label">
                <i class="bi bi-shield-shaded me-1 text-danger"></i>
                <span class="text-danger fw-semibold">Super Admin Security Gateway</span>
            </div>
            <h1 class="page-title">Administrator &amp; Role Management</h1>
            <p class="page-subtitle">Configure authorization levels and assign roles to sub-administrators (Course Admin, Student Admin) or standard accounts.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('students.index') }}" class="btn-cancel-link hover-lift">
                <i class="bi bi-arrow-left me-1"></i>
                <span>Student Dashboard</span>
            </a>
            <a href="{{ route('courses.index') }}" class="btn-cancel-link hover-lift">
                <i class="bi bi-journal-bookmark me-1"></i>
                <span>Courses</span>
            </a>
        </div>
    </div>

    @include('partials.flash', ['showErrors' => true, 'errorTitle' => 'Action Failed'])

    {{-- Role Overview Cards --}}
    <div class="stats-overview-grid animate-fade-up mb-4" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon-wrapper danger" style="background: rgba(225, 29, 72, 0.12); color: #e11d48;">
                <i class="bi bi-shield-shaded"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Super Admins</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $roleCounts[\App\Models\User::ROLE_SUPER_ADMIN] ?? 0 }}</span>
                    <span class="stat-trend positive" style="background: #ffe4e6; color: #e11d48;">System Owner</span>
                </div>
                <span class="stat-hint">Full management access</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper teal">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Student Admins</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $roleCounts[\App\Models\User::ROLE_STUDENT_ADMIN] ?? 0 }}</span>
                    <span class="stat-trend positive">Sub-Admin</span>
                </div>
                <span class="stat-hint">Manage student records</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper indigo">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Course Admins</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $roleCounts[\App\Models\User::ROLE_COURSE_ADMIN] ?? 0 }}</span>
                    <span class="stat-trend positive" style="background: #eef2ff; color: #4f46e5;">Sub-Admin</span>
                </div>
                <span class="stat-hint">Manage academic courses</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: #f1f5f9; color: #64748b;">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Standard Users</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $roleCounts[\App\Models\User::ROLE_USER] ?? 0 }}</span>
                    <span class="stat-trend neutral">Readers</span>
                </div>
                <span class="stat-hint">Read-only catalog</span>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Split: Create Admin Form (Left) + Users Role Table (Right) --}}
    <div class="courses-layout-grid animate-fade-up">

        {{-- Left: Create New Admin/User Card --}}
        <div class="course-form-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="section-number-pill" style="background: #fee2e2; color: #e11d48;"><i class="bi bi-person-plus-fill"></i></span>
                <h2 class="form-section-title mb-0">Create Sub-Admin</h2>
            </div>
            <p class="text-muted small mb-4">Provision a new account with immediate role-based authorization.</p>

            <form action="{{ route('admin.roles.store') }}" method="POST" novalidate autocomplete="off">
                @csrf

                <div class="field-group mb-3">
                    <label for="name" class="field-label">
                        <span>Full Name</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-person input-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            class="field-input with-icon @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Daw Khin Khin"
                            required
                        >
                    </div>
                    @error('name')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span></div>
                    @enderror
                </div>

                <div class="field-group mb-3">
                    <label for="email" class="field-label">
                        <span>Account Email</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-envelope-at input-icon" aria-hidden="true"></i>
                        <input
                            type="email"
                            class="field-input with-icon @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="e.g. courseadmin@studenthub.local"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span></div>
                    @enderror
                </div>

                <div class="field-group mb-3">
                    <label for="role" class="field-label">
                        <span>Assigned Role</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-shield-check input-icon" aria-hidden="true"></i>
                        <select name="role" id="role" class="field-select with-icon @error('role') is-invalid @enderror" required>
                            <option value="{{ \App\Models\User::ROLE_STUDENT_ADMIN }}" @selected(old('role') === \App\Models\User::ROLE_STUDENT_ADMIN)>Student Admin (Manage Students)</option>
                            <option value="{{ \App\Models\User::ROLE_COURSE_ADMIN }}" @selected(old('role') === \App\Models\User::ROLE_COURSE_ADMIN)>Course Admin (Manage Courses)</option>
                            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected(old('role') === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin (Full System Control)</option>
                            <option value="{{ \App\Models\User::ROLE_USER }}" @selected(old('role') === \App\Models\User::ROLE_USER)>Standard User (Catalog Read-Only)</option>
                        </select>
                    </div>
                    @error('role')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span></div>
                    @enderror
                </div>

                <div class="field-group mb-4">
                    <label for="password" class="field-label">
                        <span>Initial Password</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon position-relative">
                        <i class="bi bi-lock-fill input-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            class="field-input with-icon @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Minimum 8 characters"
                            required
                        >
                        <button type="button" class="password-toggle-btn" id="toggleRolePasswordBtn" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="toggleRolePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span></div>
                    @enderror
                </div>

                <button type="submit" class="btn-primary-custom w-100 hover-lift">
                    <i class="bi bi-person-check-fill me-1" aria-hidden="true"></i>
                    <span>Create &amp; Assign Role</span>
                </button>
            </form>
        </div>

        {{-- Right: Users Role Management Table --}}
        <div class="course-catalog-card">
            <div class="course-catalog-header flex-wrap gap-2">
                <div>
                    <h3 class="mb-0" style="font-family: var(--font-heading); font-size: 16px; font-weight: 700; color: var(--ink-primary);">User &amp; Sub-Admin Directory</h3>
                    <span class="text-muted small">Update role assignments in real time</span>
                </div>

                {{-- Search & Role Filter --}}
                <form method="GET" action="{{ route('admin.roles.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="search-field" style="width: 185px;">
                        <i class="bi bi-search search-icon" aria-hidden="true"></i>
                        <input
                            type="search"
                            name="search"
                            class="search-input"
                            value="{{ request('search') }}"
                            placeholder="Search name/email..."
                            style="min-height: 38px; padding-left: 36px;"
                        >
                    </div>
                    <select name="role" class="filter-select" style="min-height: 38px; font-size: 12.5px; padding-right: 28px;" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        @foreach($roleLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if(request()->filled('search') || request()->filled('role'))
                        <a href="{{ route('admin.roles.index') }}" class="btn-cancel-link" style="padding: 7px 10px; font-size: 12px;" title="Reset filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table" id="rolesTable">
                    <thead>
                        <tr>
                            <th scope="col" style="min-width: 200px;">User</th>
                            <th scope="col" style="min-width: 130px;">Current Role</th>
                            <th scope="col" style="min-width: 240px;">Assign Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $u)
                            @php
                                $gradient = $avatarGradients[$index % count($avatarGradients)];
                                $isSelf   = ($u->id === auth()->id());
                            @endphp
                            <tr>
                                <td>
                                    <div class="student-identity-cell">
                                        <div class="student-avatar" style="background: {{ $gradient }};" aria-hidden="true">{{ $u->initials('U') }}</div>
                                        <div class="student-identity-meta">
                                            <div class="fw-bold text-dark">
                                                {{ $u->name }}
                                                @if($isSelf)
                                                    <span class="badge bg-light text-dark border ms-1" style="font-size: 10px;">You</span>
                                                @endif
                                            </div>
                                            <div class="student-subtext text-muted" style="font-size: 11.5px;">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-pill {{ $u->roleBadgeClass() }}">
                                        {{ $u->roleLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.roles.update', $u) }}" method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select
                                            name="role"
                                            class="filter-select"
                                            style="min-height: 36px; font-size: 12px; padding: 4px 28px 4px 10px; width: 155px;"
                                            @disabled($isSelf)
                                            title="{{ $isSelf ? 'You cannot change your own Super Admin role.' : 'Select new role' }}"
                                        >
                                            @foreach($roleLabels as $rValue => $rName)
                                                <option value="{{ $rValue }}" @selected($u->role === $rValue)>{{ $rName }}</option>
                                            @endforeach
                                        </select>
                                        <button
                                            type="submit"
                                            class="btn-primary-custom"
                                            style="padding: 5px 12px; font-size: 12px; min-height: 36px;"
                                            @disabled($isSelf)
                                            title="{{ $isSelf ? 'Cannot demote self' : 'Save role change' }}"
                                        >
                                            <i class="bi bi-check2"></i>
                                            <span>Update</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">
                                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                    <strong>No accounts found matching criteria.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="list-card-foot mt-3 d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>

</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('toggleRolePasswordBtn');
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleRolePasswordIcon');

        if (btn && input && icon) {
            btn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            });
        }
    });
</script>
@endpush
