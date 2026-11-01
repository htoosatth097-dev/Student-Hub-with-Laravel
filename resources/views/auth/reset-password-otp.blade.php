@extends('layout.app')

@section('title', 'Verify OTP & Reset Password | StudentHub')

@section('content')
<div class="auth-page-container animate-fade-in">
    <div class="auth-card-wrapper animate-fade-up">

        @include('partials.flash', ['showErrors' => true, 'errorTitle' => 'Please check the errors below'])

        @if(session('demo_otp'))
            <div class="banner-flash info mb-4" role="alert" style="background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e;">
                <div class="banner-icon-box" style="background: #ccfbf1; color: #0d9488;">
                    <i class="bi bi-key-fill" aria-hidden="true"></i>
                </div>
                <div class="banner-content">
                    <div class="banner-title" style="color: #0f766e;">Local Testing Notice (Demo Mode)</div>
                    <div class="banner-text">
                        Your 6-digit OTP code is: <strong style="font-size: 1.15rem; letter-spacing: 2px; font-family: var(--font-mono); color: #0f766e;">{{ session('demo_otp') }}</strong>
                        <span class="d-block small text-muted mt-1">(Sent to storage/logs/laravel.log &amp; Mailer)</span>
                    </div>
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
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <span>OTP Verification</span>
                </div>
                <h1 class="auth-title">Enter Verification Code</h1>
                <p class="auth-subtitle">We have sent a 6-digit verification code (OTP) to your email address. Enter the code and choose a new password.</p>
            </div>

            <form action="{{ route('password.otp.update') }}" method="POST" novalidate class="auth-form" id="resetPasswordOtpForm">
                @csrf

                <div class="field-group mb-3">
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
                            value="{{ old('email', $email ?? '') }}"
                            placeholder="admin@studenthub.local"
                            required
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

                <div class="field-group mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="otp" class="field-label mb-0">
                            <span>6-Digit Verification Code (OTP)</span> <span class="req-star">*</span>
                        </label>
                        <span class="text-muted small">Expires in 15 mins</span>
                    </div>
                    <div class="input-with-icon mt-1">
                        <i class="bi bi-key input-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            class="field-input with-icon @error('otp') is-invalid @enderror"
                            id="otp"
                            name="otp"
                            value="{{ old('otp', session('demo_otp') ?? '') }}"
                            placeholder="123456"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autofocus
                            style="font-family: var(--font-mono); font-size: 1.25rem; letter-spacing: 6px; font-weight: 700; text-align: center;"
                        >
                    </div>
                    @error('otp')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="field-group mb-3">
                    <label for="password" class="field-label">
                        <span>New Password</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon position-relative">
                        <i class="bi bi-lock-fill input-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            class="field-input with-icon @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="At least 8 characters"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn1" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="togglePasswordIcon1"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="field-group mb-4">
                    <label for="password_confirmation" class="field-label">
                        <span>Confirm New Password</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon position-relative">
                        <i class="bi bi-shield-lock-fill input-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            class="field-input with-icon"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirm your new password"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn2" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="togglePasswordIcon2"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn-primary-custom w-100 hover-lift auth-submit-btn" id="submitBtn">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                        <span>Verify OTP &amp; Reset Password</span>
                    </button>
                </div>
            </form>

            <div class="auth-card-foot">
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('password.request') }}" class="btn-cancel-link" style="font-size: 0.85rem;">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        <span>Resend OTP Code</span>
                    </a>
                    <a href="{{ route('login') }}" class="btn-cancel-link" style="font-size: 0.85rem;">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        <span>Back to Login</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (btn && input && icon) {
                btn.addEventListener('click', function () {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.classList.toggle('bi-eye', !isPassword);
                    icon.classList.toggle('bi-eye-slash', isPassword);
                });
            }
        }

        setupToggle('togglePasswordBtn1', 'password', 'togglePasswordIcon1');
        setupToggle('togglePasswordBtn2', 'password_confirmation', 'togglePasswordIcon2');
    });
</script>
@endpush
