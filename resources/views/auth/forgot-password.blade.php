@extends('layout.app')

@section('title', 'Forgot Password — Reset via OTP | StudentHub')

@section('content')
<div class="auth-page-container animate-fade-in">
    <div class="auth-card-wrapper animate-fade-up">

        @include('partials.flash', ['successTitle' => 'Code Sent'])

        @if($errors->any() && !$errors->has('email'))
            <div class="banner-flash danger mb-4" role="alert">
                <div class="banner-icon-box">
                    <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="banner-content">
                    <div class="banner-title">Error</div>
                    <ul class="banner-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="auth-card">
            <div class="auth-card-head">
                <a href="{{ route('about.index') }}" class="brand-link justify-content-center mb-3 text-decoration-none">
                    <span class="brand-symbol"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
                    <span class="brand-title">StudentHub</span>
                </a>
                <div class="auth-badge" style="background: rgba(13, 148, 136, 0.12); color: var(--accent-primary);">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    <span>Password Recovery</span>
                </div>
                <h1 class="auth-title">Reset Your Password</h1>
                <p class="auth-subtitle">Enter your registered account email address. We will send you a secure 6-digit verification code (OTP) to reset your password.</p>
            </div>

            <form action="{{ route('password.otp.send') }}" method="POST" novalidate class="auth-form" id="forgotPasswordForm">
                @csrf

                <div class="field-group mb-4">
                    <label for="email" class="field-label">
                        <span>Account Email Address</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-envelope-at input-icon" aria-hidden="true"></i>
                        <input
                            type="email"
                            class="field-input with-icon @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="e.g. admin@studenthub.local"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn-primary-custom w-100 hover-lift auth-submit-btn" id="submitBtn">
                        <i class="bi bi-send-fill me-1" aria-hidden="true"></i>
                        <span>Send 6-Digit OTP Code</span>
                    </button>
                </div>
            </form>

            <div class="auth-card-foot">
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('login') }}" class="btn-cancel-link" style="font-size: 0.85rem;">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span>Back to Login</span>
                    </a>
                    <a href="{{ route('about.index') }}" class="btn-cancel-link" style="font-size: 0.85rem;">
                        <span>About Platform</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
