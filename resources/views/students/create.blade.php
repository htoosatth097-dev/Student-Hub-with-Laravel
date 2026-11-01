@extends('layout.app')

@section('title', 'Add New Student | StudentHub')

@section('content')
<div class="form-page-layout animate-fade-in">

    {{-- Left column: Title & Live Student ID Card Preview --}}
    <aside class="form-intro-column animate-fade-up">
        @unless(auth()->user()->isStudentAdmin())
            <a href="{{ route('students.index') }}" class="back-link">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>Back to Student Directory</span>
            </a>
        @endunless

        <div class="section-label">
            <i class="bi bi-person-plus-fill me-1"></i>
            <span>Enrollment Registry</span>
        </div>
        <h1 class="page-title">Add New Student</h1>
        <p class="page-instruction">Register a new student with their contact details and academic program assignment.</p>

        {{-- Interactive Live Student Card Preview --}}
        <div class="student-preview-card">
            <div class="preview-card-header">
                <span class="preview-card-chip"><i class="bi bi-person-vcard me-1"></i>Live ID Preview</span>
                <span class="status-badge" style="padding: 2px 8px; font-size: 11px;">
                    <span class="status-dot"></span> Active
                </span>
            </div>
            <div class="preview-card-body">
                <div class="preview-avatar" id="liveAvatar">ST</div>
                <div class="min-w-0">
                    <div class="preview-name text-truncate" id="liveName">Candidate Name</div>
                    <div class="preview-meta">
                        <span id="liveCode">Auto ID</span>
                        <span>&middot;</span>
                        <span id="liveGender">Gender</span>
                    </div>
                    <div class="preview-submeta text-truncate mt-1" id="liveEmail">student@example.com</div>
                </div>
            </div>
            <div class="preview-card-footer">
                <div class="text-truncate">
                    <i class="bi bi-mortarboard-fill me-1 text-teal"></i>
                    <span id="liveCourse">No Course Selected</span>
                </div>
                <div>
                    <i class="bi bi-patch-check-fill text-teal"></i>
                </div>
            </div>
        </div>
    </aside>

    {{-- Right column: Flash alerts + Form container --}}
    <div class="form-body-column animate-fade-up">

        @include('partials.flash', ['showErrors' => true, 'errorTitle' => 'Please correct the following input errors:'])

        <form action="{{ route('students.store') }}" method="POST" novalidate autocomplete="off" id="studentForm">
            @csrf

            {{-- Section 1: Identity & Academic Code --}}
            <section class="form-section">
                <div class="form-section-header">
                    <span class="section-number-pill">1</span>
                    <h2 class="form-section-title">Student Identity</h2>
                </div>
                <div class="form-fields-grid">

                    <div class="field-group">
                        <label for="student_code" class="field-label">
                            <span>Student Code</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-upc-scan input-icon" aria-hidden="true"></i>
                            <input
                                type="text"
                                class="field-input with-icon"
                                id="student_code"
                                value="Auto-generated (STU-XXX)"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="student_name" class="field-label">
                            <span>Full Name</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-person input-icon" aria-hidden="true"></i>
                            <input
                                type="text"
                                class="field-input with-icon @error('student_name') is-invalid @enderror"
                                id="student_name"
                                name="student_name"
                                value="{{ old('student_name') }}"
                                placeholder="e.g. Aung Aung / Su Su"
                                required
                                autofocus
                            >
                        </div>
                        @error('student_name')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- Section 2: Contact Details --}}
            <section class="form-section">
                <div class="form-section-header">
                    <span class="section-number-pill">2</span>
                    <h2 class="form-section-title">Contact Details</h2>
                </div>
                <div class="form-fields-grid">

                    <div class="field-group">
                        <label for="email" class="field-label">
                            <span>Email Address</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                            <input
                                type="email"
                                class="field-input with-icon @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="student@example.com"
                                required
                            >
                        </div>
                        @error('email')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="phone_number" class="field-label">
                            <span>Phone Number</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-telephone input-icon" aria-hidden="true"></i>
                            <input
                                type="tel"
                                class="field-input with-icon @error('phone_number') is-invalid @enderror"
                                id="phone_number"
                                name="phone_number"
                                value="{{ old('phone_number') }}"
                                placeholder="09123456789"
                                required
                            >
                        </div>
                        @error('phone_number')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- Section 3: Program & Demographics --}}
            <section class="form-section">
                <div class="form-section-header">
                    <span class="section-number-pill">3</span>
                    <h2 class="form-section-title">Program &amp; Demographics</h2>
                </div>
                <div class="form-fields-grid">

                    <div class="field-group">
                        <label for="gender" class="field-label">
                            <span>Gender</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-gender-ambiguous input-icon" aria-hidden="true"></i>
                            <select
                                class="field-select with-icon @error('gender') is-invalid @enderror"
                                id="gender"
                                name="gender"
                                required
                            >
                                <option value="">— Select Gender —</option>
                                @foreach(\App\Models\laravelstudents::GENDERS as $gOption)
                                    <option value="{{ $gOption }}" @selected(old('gender') === $gOption)>{{ $gOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('gender')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="course_id" class="field-label">
                            <span>Enrolled Course</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-mortarboard input-icon" aria-hidden="true"></i>
                            <select
                                class="field-select with-icon @error('course_id') is-invalid @enderror"
                                id="course_id"
                                name="course_id"
                                required
                            >
                                <option value="">— Select Course —</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('course_id')
                            <div class="field-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- Action Row at Bottom --}}
            <div class="form-action-row">
                <a href="{{ route('students.index') }}" class="btn-cancel-link hover-lift">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span>Cancel</span>
                </a>
                <div class="action-buttons-end">
                    <button type="submit" class="btn-primary-custom hover-lift">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                        <span>Register Student</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput    = document.getElementById('student_name');
        const emailInput   = document.getElementById('email');
        const genderSelect = document.getElementById('gender');
        const courseSelect = document.getElementById('course_id');

        const liveName   = document.getElementById('liveName');
        const liveEmail  = document.getElementById('liveEmail');
        const liveGender = document.getElementById('liveGender');
        const liveCourse = document.getElementById('liveCourse');
        const liveAvatar = document.getElementById('liveAvatar');

        function updateLivePreview() {
            const nameVal   = nameInput && nameInput.value.trim() ? nameInput.value.trim() : 'Candidate Name';
            const emailVal  = emailInput && emailInput.value.trim() ? emailInput.value.trim() : 'student@example.com';
            const genderVal = genderSelect && genderSelect.value ? genderSelect.value : 'Gender';
            const courseVal = courseSelect && courseSelect.selectedIndex > 0 ? courseSelect.options[courseSelect.selectedIndex].text : 'No Course Selected';

            if (liveName) liveName.textContent = nameVal;
            if (liveEmail) liveEmail.textContent = emailVal;
            if (liveGender) liveGender.textContent = genderVal;
            if (liveCourse) liveCourse.textContent = courseVal;

            if (liveAvatar) {
                const parts = nameVal.split(/\s+/);
                let initials = '';
                if (parts[0]) initials += parts[0].charAt(0);
                if (parts[1]) initials += parts[1].charAt(0);
                liveAvatar.textContent = (initials || 'ST').toUpperCase();
            }
        }

        if (nameInput) nameInput.addEventListener('input', updateLivePreview);
        if (emailInput) emailInput.addEventListener('input', updateLivePreview);
        if (genderSelect) genderSelect.addEventListener('change', updateLivePreview);
        if (courseSelect) courseSelect.addEventListener('change', updateLivePreview);

        updateLivePreview();
    });
</script>
@endpush
