@extends('layout.app')

@section('title', 'About Platform | StudentHub')

@section('content')
<div class="editorial-wrapper animate-fade-in">

    {{-- Page Header --}}
    <div class="page-header-row animate-fade-up">
        <div>
            <div class="section-label">
                <i class="bi bi-info-circle-fill me-1"></i>
                <span>Platform Architecture</span>
            </div>
            <h1 class="page-title">About StudentHub</h1>
            <p class="page-subtitle">A purpose-built administrative platform engineered for student records, enrollment workflows, and academic registries.</p>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="editorial-body animate-fade-up">
        <p class="lead-text">
            <strong>StudentHub</strong> is an intuitive, high-performance academic information management system built for educational academies, training centers, and university departments. It replaces cumbersome spreadsheets and fragmented data with a unified, secure, and responsive web portal.
        </p>

        {{-- Highlight Cards Grid --}}
        <div class="about-grid mt-4 mb-4">
            <div class="about-card">
                <div class="stat-icon-wrapper teal mb-3">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4>Role-Based Security</h4>
                <p>Granular policies separating Super Admin, Student Admin, Course Admin, and Standard User permissions.</p>
            </div>

            <div class="about-card">
                <div class="stat-icon-wrapper indigo mb-3">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4>Data Integrity</h4>
                <p>Automatic sequential student (`STU-XXX`) and course (`CS-XXX`) codes with strict relational validation.</p>
            </div>

            <div class="about-card">
                <div class="stat-icon-wrapper amber mb-3">
                    <i class="bi bi-window-fullscreen"></i>
                </div>
                <h4>Adaptive Modern UI</h4>
                <p>Optimized for desktop workstations, tablets, and mobile devices with dark mode and print-ready dossiers.</p>
            </div>
        </div>

        <h3 class="mt-4" style="font-family: var(--font-heading); font-size: 18px; font-weight: 700; color: var(--ink-primary);">Technical Stack &amp; Architecture</h3>
        <ul class="detail-list mt-3">
            <li class="detail-item">
                <span class="detail-key"><i class="bi bi-code-slash me-1"></i> Framework</span>
                <span class="detail-val"><strong>Laravel 12 (PHP 8.2+)</strong> with FormRequests, Policies &amp; Blade</span>
            </li>
            <li class="detail-item">
                <span class="detail-key"><i class="bi bi-palette me-1"></i> Frontend UI</span>
                <span class="detail-val">Bootstrap 5.3.3 + Custom Modern Enterprise CSS Design System</span>
            </li>
            <li class="detail-item">
                <span class="detail-key"><i class="bi bi-database me-1"></i> Database</span>
                <span class="detail-val">Relational Eloquent ORM with Transaction-Safe Code Generation</span>
            </li>
            <li class="detail-item">
                <span class="detail-key"><i class="bi bi-type me-1"></i> Typography</span>
                <span class="detail-val">Space Grotesk · Plus Jakarta Sans · JetBrains Mono · Myanmar Unicode Support</span>
            </li>
        </ul>

        {{-- Management links are shown only to users authorized for student records. --}}
        @can('viewAny', App\Models\laravelstudents::class)
        <div class="editorial-cta-box mt-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1" style="font-weight: 700; color: var(--ink-primary);">Ready to manage student records?</h5>
                    <p class="mb-0 text-muted small">Access the directory or enroll a candidate directly into the registry.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('students.index') }}" class="btn-primary-custom hover-lift">
                        <i class="bi bi-people-fill me-1"></i>
                        <span>Student Directory</span>
                    </a>
                    <a href="{{ route('students.create') }}" class="btn-cancel-link hover-lift">
                        <i class="bi bi-plus-lg me-1"></i>
                        <span>Add Student</span>
                    </a>
                </div>
            </div>
        </div>
        @endcan

    </div>

</div>
@endsection
