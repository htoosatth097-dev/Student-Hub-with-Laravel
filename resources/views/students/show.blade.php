@extends('layout.app')

@section('title', $student->student_name . ' — Student Dossier | StudentHub')

@section('content')
<div class="profile-view animate-fade-in">

    {{-- Top navigation link --}}
    <div class="mb-3 animate-fade-up">
        <a href="{{ route('students.index') }}" class="back-link">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Back to Student Directory</span>
        </a>
    </div>

    {{-- Main Profile Card --}}
    <div class="profile-card animate-fade-up">

        {{-- Profile Header Banner --}}
        <div class="profile-header-banner">
            <div class="profile-avatar-row">
                <div class="profile-avatar-large" aria-hidden="true">
                    {{ $student->initials('S') }}
                </div>
                <div class="profile-meta-text">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="profile-student-name">{{ $student->student_name }}</h1>
                        <span class="status-badge">
                            <span class="status-dot"></span>
                            <span>Active Enrollment</span>
                        </span>
                    </div>
                    <div class="profile-sub-badges">
                        <span class="cell-code">
                            <i class="bi bi-hash"></i>{{ $student->student_code }}
                        </span>
                        <button type="button" class="btn-copy-code" data-code="{{ $student->student_code }}" title="Copy Student Code">
                            <i class="bi bi-copy"></i>
                        </button>
                        @if($student->course?->course_name)
                            <span class="course-badge">
                                <i class="bi bi-mortarboard-fill me-1"></i>{{ $student->course?->course_name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="profile-quick-actions">
                <a href="{{ route('students.edit', $student->id) }}" class="btn-primary-custom hover-lift">
                    <i class="bi bi-pencil-fill me-1"></i>
                    <span>Edit Profile</span>
                </a>
                <button type="button" class="btn-cancel-link hover-lift" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>
                    <span class="d-none d-sm-inline">Print Dossier</span>
                </button>
            </div>
        </div>

        {{-- Information Details Grid --}}
        <div class="profile-info-grid">

            <div class="info-block">
                <div class="info-icon teal"><i class="bi bi-upc-scan"></i></div>
                <div class="info-content">
                    <span class="info-label">Institutional Code</span>
                    <span class="info-value font-mono">{{ $student->student_code }}</span>
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon indigo"><i class="bi bi-person-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">{{ $student->student_name }}</span>
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon sky"><i class="bi bi-envelope-at-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">Email Address</span>
                    @if($student->email)
                        <a href="mailto:{{ $student->email }}" class="info-value-link" title="Send email">
                            <span>{{ $student->email }}</span>
                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                    @else
                        <span class="text-muted">Not specified</span>
                    @endif
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon amber"><i class="bi bi-telephone-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">Contact Phone</span>
                    @if($student->phone_number)
                        <a href="tel:{{ $student->phone_number }}" class="info-value-link" title="Call student">
                            <span>{{ $student->phone_number }}</span>
                            <i class="bi bi-telephone-outbound ms-1 small"></i>
                        </a>
                    @else
                        <span class="text-muted">Not specified</span>
                    @endif
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon purple"><i class="bi bi-gender-ambiguous"></i></div>
                <div class="info-content">
                    <span class="info-label">Gender</span>
                    <span class="info-value">{{ $student->gender ?? 'Not specified' }}</span>
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon emerald"><i class="bi bi-journal-bookmark-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">Enrolled Program</span>
                    <span class="info-value">{{ $student->course?->course_name ?? 'General Enrollment' }}</span>
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon slate"><i class="bi bi-calendar-check"></i></div>
                <div class="info-content">
                    <span class="info-label">Registered On</span>
                    <span class="info-value">{{ $student->created_at ? $student->created_at->format('M d, Y · h:i A') : 'N/A' }}</span>
                </div>
            </div>

            <div class="info-block">
                <div class="info-icon slate"><i class="bi bi-clock-history"></i></div>
                <div class="info-content">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value">{{ $student->updated_at ? $student->updated_at->diffForHumans() : 'N/A' }}</span>
                </div>
            </div>

        </div>

        {{-- Footer actions --}}
        <div class="form-action-row mt-4">
            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Permanently remove {{ addslashes($student->student_name) }} from the registry? This action cannot be reversed.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-destructive-link hover-lift">
                    <i class="bi bi-trash3 me-1"></i>
                    <span>Delete Record</span>
                </button>
            </form>

            <div class="action-buttons-end">
                <button type="button" class="btn-cancel-link hover-lift" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>
                    <span>Print Dossier</span>
                </button>
                <a href="{{ route('students.index') }}" class="btn-cancel-link hover-lift">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span>Back to Directory</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
