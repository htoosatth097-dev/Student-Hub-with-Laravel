@extends('layout.app')

@section('title', 'Edit Course | StudentHub')

@section('content')
<section class="courses-index-page animate-fade-in">
    <div class="mb-3 animate-fade-up">
        <a href="{{ route('courses.index') }}" class="back-link">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Back to Course Catalog</span>
        </a>
    </div>

    <div class="page-header-row animate-fade-up mb-4">
        <div>
            <div class="section-label"><i class="bi bi-pencil-square me-1 text-teal"></i><span>Course Management</span></div>
            <h1 class="page-title">Edit Course</h1>
            <p class="page-subtitle">Update curriculum title, duration hours, tuition fee, and description for <strong>{{ $course->course_name }}</strong>.</p>
        </div>
    </div>

    @include('partials.flash', ['showErrors' => true, 'errorTitle' => 'Please review the following errors:'])

    <div class="course-form-card animate-fade-up" style="position: static; max-width: 760px;">
        <form action="{{ route('courses.update', $course) }}" method="POST" autocomplete="off" novalidate>
            @csrf
            @method('PUT')

            <div class="field-group mb-3">
                <label for="course_name" class="field-label">Course Title <span class="req-star">*</span></label>
                <div class="input-with-icon">
                    <i class="bi bi-journal-text input-icon" aria-hidden="true"></i>
                    <input type="text" class="field-input with-icon @error('course_name') is-invalid @enderror" id="course_name" name="course_name" value="{{ old('course_name', $course->course_name) }}" required maxlength="255">
                </div>
                @error('course_name')<div class="field-error"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $message }}</span></div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="field-group">
                        <label for="course_code" class="field-label">Course Code</label>
                        <div class="input-with-icon">
                            <i class="bi bi-upc input-icon" aria-hidden="true"></i>
                            <input type="text" class="field-input with-icon" id="course_code" value="{{ $course->course_code }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label for="duration_hours" class="field-label">Duration (hrs)</label>
                        <div class="input-with-icon">
                            <i class="bi bi-clock input-icon" aria-hidden="true"></i>
                            <input type="text" class="field-input with-icon" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" maxlength="100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="field-group mb-3">
                <label for="course_fee" class="field-label">Course Fee (MMK)</label>
                <div class="input-with-icon">
                    <i class="bi bi-cash-stack input-icon" aria-hidden="true"></i>
                    <input type="text" class="field-input with-icon" id="course_fee" name="course_fee" value="{{ old('course_fee', $course->course_fee) }}" maxlength="100">
                </div>
            </div>

            <div class="field-group mb-4">
                <label for="description" class="field-label">Description</label>
                <div class="input-with-icon textarea-icon-wrapper">
                    <i class="bi bi-card-text input-icon" aria-hidden="true"></i>
                    <textarea class="field-textarea with-icon" id="description" name="description" rows="4">{{ old('description', $course->description) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn-primary-custom hover-lift"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
                <a href="{{ route('courses.index') }}" class="btn-cancel-link hover-lift">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
