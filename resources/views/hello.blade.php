@extends('layout.app')

@section('title', 'Welcome | StudentHub')

@section('content')
<div class="editorial-wrapper animate-fade-in">

    {{-- Page Header --}}
    <div class="page-header-row animate-fade-up">
        <div>
            <div class="section-label">
                <i class="bi bi-stars me-1"></i>
                <span>Getting Started</span>
            </div>
            <h1 class="page-title">Welcome to StudentHub</h1>
            <p class="page-subtitle">Your central academic records dashboard and student information portal.</p>
        </div>
    </div>

    {{-- Welcome Card --}}
    <div class="editorial-body animate-fade-up">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="stat-icon-wrapper teal" style="width: 52px; height: 52px; font-size: 24px;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <h3 class="mb-1" style="font-family: var(--font-heading); font-weight: 700;">StudentHub Information System</h3>
                <p class="text-muted small mb-0">System status: Online &middot; Version 1.2</p>
            </div>
        </div>

        <p class="lead-text">
            Everything is configured and ready for your academic management operations. You can browse registered students, enroll new candidates, or organize course curriculums.
        </p>

        <div class="editorial-cta-box mt-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1" style="font-weight: 700; color: var(--ink-primary);">Explore the Student Directory</h5>
                    <p class="mb-0 text-muted small">View, search, filter, and export student dossiers.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('students.index') }}" class="btn-primary-custom hover-lift">
                        <i class="bi bi-people-fill me-1"></i>
                        <span>Go to Directory</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
