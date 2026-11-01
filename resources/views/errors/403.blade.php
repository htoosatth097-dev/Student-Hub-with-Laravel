@extends('layout.app')

@section('title', '403 Forbidden | StudentHub')

@section('content')
<div class="editorial-wrapper animate-fade-in" style="max-width: 680px; margin: 40px auto; text-align: center;">
    <div class="empty-state-clean animate-fade-up">
        <div class="empty-state-icon-large" style="background: var(--danger-subtle); color: var(--danger); border-color: var(--danger-border);">
            <i class="bi bi-shield-slash-fill" aria-hidden="true"></i>
        </div>
        <div class="badge text-bg-danger mb-3" style="font-size: 0.8rem; font-weight: 600; padding: 6px 14px; border-radius: 9999px;">
            Error 403 &middot; Access Forbidden
        </div>
        <h1 class="empty-state-title" style="font-size: 1.85rem;">Administrator Privileges Required</h1>
        <p class="empty-state-copy" style="max-width: 500px; margin: 0 auto 24px;">
            Your account does not have sufficient permissions to access the student directory or curriculum management controls. This area is reserved strictly for authenticated administrators.
        </p>

        <div class="d-flex flex-wrap gap-2 justify-content-center">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ auth()->user()->landingPath() }}" class="btn-primary-custom hover-lift">
                        <i class="bi bi-house-door me-1"></i>
                        <span>Go to My Dashboard</span>
                    </a>
                @endif
            @endauth
            <a href="{{ route('about.index') }}" class="btn-cancel-link hover-lift">
                <i class="bi bi-arrow-left me-1"></i>
                <span>Platform Overview</span>
            </a>
            <a href="{{ route('contact.index') }}" class="btn-cancel-link hover-lift">
                <i class="bi bi-chat-dots me-1"></i>
                <span>Contact Administrator</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-cancel-link hover-lift">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    <span>Switch Account</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

