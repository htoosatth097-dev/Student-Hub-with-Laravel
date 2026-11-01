@extends('layout.app')

@section('title', 'Courses & Programs | StudentHub')

@section('content')
@php
    $totalCourses          = isset($courses) ? count($courses) : 0;
    $totalStudentsEnrolled = isset($courses) ? $courses->sum('students_count') : 0;
    $activeCoursesCount    = isset($courses) ? $courses->where('students_count', '>', 0)->count() : 0;
@endphp

<section class="courses-index-page animate-fade-in">

    {{-- Page Heading Header --}}
    <div class="page-header-row animate-fade-up">
        <div>
            <div class="section-label">
                <i class="bi bi-journal-bookmark-fill me-1"></i>
                <span>Academic Programs</span>
            </div>
            <h1 class="page-title">Courses &amp; Academic Programs</h1>
            <p class="page-subtitle">Browse curriculum offerings and track active student enrollment across departments.</p>
        </div>
    </div>

    @include('partials.flash', ['showErrors' => true, 'errorTitle' => 'Please review the following input errors:'])

    {{-- Top Overview Stats Bar --}}
    <div class="stats-overview-grid animate-fade-up" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon-wrapper indigo">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Total Programs</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $totalCourses }}</span>
                    <span class="stat-trend positive">Active</span>
                </div>
                <span class="stat-hint">Registered curriculum tracks</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper teal">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Enrolled Students</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $totalStudentsEnrolled }}</span>
                    <span class="stat-trend neutral">Total</span>
                </div>
                <span class="stat-hint">Across all departments</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper amber">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="stat-details">
                <span class="stat-label">Active Batches</span>
                <div class="stat-number-row">
                    <span class="stat-number">{{ $activeCoursesCount }}</span>
                    <span class="stat-trend positive">Enrolling</span>
                </div>
                <span class="stat-hint">Courses with active students</span>
            </div>
        </div>
    </div>

    {{-- 2-Column Split: Add Course Form (Left) + Course Directory (Right) --}}
    <div class="courses-layout-grid animate-fade-up {{ auth()->user()->can('create', App\Models\courses::class) ? '' : 'courses-readonly-layout' }}">

        {{-- Course creation is available only to Super Admin and Course Admin. --}}
        @can('create', App\Models\courses::class)
        <div class="course-form-card" id="courseFormCard">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="section-number-pill"><i class="bi bi-plus-lg"></i></span>
                <h2 class="form-section-title mb-0">Add New Course</h2>
            </div>
            <p class="text-muted small mb-4">Create a new academic program that students can enroll in.</p>

            <form action="{{ route('courses.store') }}" method="POST" novalidate autocomplete="off">
                @csrf

                <div class="field-group mb-3">
                    <label for="course_name" class="field-label">
                        <span>Course Title</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-journal-text input-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            class="field-input with-icon @error('course_name') is-invalid @enderror"
                            id="course_name"
                            name="course_name"
                            value="{{ old('course_name') }}"
                            placeholder="e.g. Full-Stack Web Development"
                            required
                        >
                    </div>
                    @error('course_name')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="field-group mb-3">
                    <label for="course_code" class="field-label">
                        <span>Course Code</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-upc input-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            class="field-input with-icon"
                            id="course_code"
                            name="course_code"
                            value="Auto-generated (CS-XXX)"
                            readonly
                        >
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label for="duration_hours" class="field-label">
                                <span>Duration (hrs)</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="bi bi-clock input-icon" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    class="field-input with-icon @error('duration_hours') is-invalid @enderror"
                                    id="duration_hours"
                                    name="duration_hours"
                                    value="{{ old('duration_hours') }}"
                                    placeholder="e.g. 120"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="field-group">
                            <label for="course_fee" class="field-label">
                                <span>Course Fee (MMK)</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="bi bi-cash-stack input-icon" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    class="field-input with-icon @error('course_fee') is-invalid @enderror"
                                    id="course_fee"
                                    name="course_fee"
                                    value="{{ old('course_fee') }}"
                                    placeholder="e.g. 250000"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field-group mb-4">
                    <label for="description" class="field-label">
                        <span>Description</span>
                    </label>
                    <div class="input-with-icon textarea-icon-wrapper">
                        <i class="bi bi-card-text input-icon" aria-hidden="true"></i>
                        <textarea
                            class="field-textarea with-icon @error('description') is-invalid @enderror"
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Brief course objectives or key modules..."
                        >{{ old('description') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-primary-custom w-100 hover-lift">
                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                    <span>Save Course</span>
                </button>
            </form>
        </div>
        @endcan

        {{-- Right: Course Catalog Table & Search --}}
        <div class="course-catalog-card">
            <div class="course-catalog-header">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="mb-0" style="font-family: var(--font-heading); font-size: 16px; font-weight: 700; color: var(--ink-primary);">Course Catalog</h3>
                    <span class="students-count-badge" id="courseVisibleCount">{{ $totalCourses }}</span>
                </div>

                {{-- Quick Live Course Filter --}}
                <div class="search-field" style="max-width: 260px;">
                    <i class="bi bi-search search-icon" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="courseSearchInput"
                        class="search-input"
                        placeholder="Search courses..."
                        aria-label="Search courses"
                    >
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table course-catalog-table align-middle {{ auth()->user()->can('create', App\Models\courses::class) ? 'course-catalog-table-manage' : 'course-catalog-table-readonly' }}" id="coursesTable">
                    <thead>
                        <tr>
                            <th scope="col" class="align-middle">Course Name</th>
                            <th scope="col" class="align-middle">Code</th>
                            <th scope="col" class="align-middle">Duration</th>
                            <th scope="col" class="align-middle">Fee</th>
                            <th scope="col" class="text-center align-middle">Students</th>
                            @can('create', App\Models\courses::class)
                                <th scope="col" class="align-middle">Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody id="coursesTableBody">
                        @forelse($courses as $course)
                            @php
                                $formattedDuration = $course->formattedDuration();
                                $formattedFee      = $course->formattedFee();
                            @endphp
                            <tr class="course-row"
                                data-name="{{ strtolower($course->course_name) }}"
                                data-code="{{ strtolower($course->course_code ?? '') }}">
                                <td data-label="Course Name" class="align-middle">
                                    <div class="fw-bold text-dark">{{ $course->course_name }}</div>
                                    @if($course->description)
                                        <div class="course-description text-muted small" title="{{ $course->description }}">
                                            {{ $course->description }}
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Code" class="align-middle">
                                    @if($course->course_code)
                                        <span class="badge rounded-pill text-bg-light border course-code-badge">{{ $course->course_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Duration" class="align-middle">
                                    @if($formattedDuration)
                                        <span class="duration-pill">
                                            <i class="bi bi-clock" aria-hidden="true"></i>
                                            <span>{{ $formattedDuration }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Fee" class="align-middle">
                                    @if($formattedFee)
                                        <span class="fee-pill">
                                            <i class="bi bi-tag-fill" aria-hidden="true"></i>
                                            <span>{{ $formattedFee }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Students" class="text-center align-middle">
                                    <span class="students-count-badge"><i class="bi bi-person-fill" aria-hidden="true"></i><span>{{ $course->students_count }}</span></span>
                                </td>
                                @can('create', App\Models\courses::class)
                                    <td data-label="Actions" class="align-middle">
                                        <div class="course-actions">
                                            @can('update', $course)
                                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-cancel-link course-row-action">
                                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                                    <span>Edit</span>
                                                </a>
                                            @endcan
                                            @can('delete', $course)
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course? Courses with enrolled students cannot be deleted.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-destructive-link course-row-action course-row-action-delete">
                                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('create', App\Models\courses::class) ? 6 : 5 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                    <strong>No courses found.</strong>
                                    <p class="small text-muted mb-0">@can('create', App\Models\courses::class)Use the form to add your first course program.@else No courses have been added yet.@endcan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('courseSearchInput');
        const tableBody   = document.getElementById('coursesTableBody');
        const countBadge  = document.getElementById('courseVisibleCount');

        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows  = tableBody.querySelectorAll('.course-row');
                let visible = 0;

                rows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    const matches = query === '' || text.includes(query);
                    row.style.display = matches ? '' : 'none';
                    if (matches) visible++;
                });

                if (countBadge) {
                    countBadge.textContent = visible;
                }
            });
        }
    });
</script>
@endpush
