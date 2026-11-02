@extends('layout.app')

@section('title', 'Admin Login | StudentHub')

@section('content')
<div class="auth-page-container animate-fade-in">
    <div class="auth-card-wrapper animate-fade-up">

        @include('partials.flash', ['warningTitle' => 'Authentication Notice'])

        @if($errors->any() && !$errors->has('email') && !$errors->has('password') && !$errors->has('g-recaptcha-response'))
            <div class="banner-flash danger mb-4" role="alert">
                <div class="banner-icon-box">
                    <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="banner-content">
                    <div class="banner-title">Unable to Sign In</div>
                    <ul class="banner-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="auth-card">
            {{-- Card Header --}}
            <div class="auth-card-head">
                <a href="{{ route('about.index') }}" class="brand-link justify-content-center mb-3 text-decoration-none">
                    <span class="brand-symbol"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
                    <span class="brand-title">StudentHub</span>
                </a>
                <div class="auth-badge">
                    <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                    <span>Admin Security Gateway</span>
                </div>
                <h1 class="auth-title">Sign in to StudentHub</h1>
                <p class="auth-subtitle">Enter your authorized credentials to access your administrative workspace.</p>
            </div>

            {{-- Login Form --}}
            <form action="{{ route('login.submit') }}" method="POST" novalidate class="auth-form" id="loginForm">
                @csrf

                {{-- Email Input --}}
                <div class="field-group mb-3">
                    <label for="email" class="field-label">
                        <span>Administrator Email</span> <span class="req-star">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="bi bi-envelope-at input-icon" aria-hidden="true"></i>
                        <input
                            type="email"
                            class="field-input with-icon @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@studenthub.edu"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div class="field-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="field-label mb-0">
                            <span>Password</span> <span class="req-star">*</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password-link">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="input-with-icon position-relative">
                        <i class="bi bi-lock-fill input-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            class="field-input with-icon @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Enter your secure password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Remember Me & Options --}}
                <div class="auth-options-row mb-4">
                    <label class="custom-checkbox-label">
                        <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span class="custom-checkbox-text">Keep me signed in for 30 days</span>
                    </label>
                </div>

                {{-- Google reCAPTCHA v2 Checkbox --}}
                <div class="field-group mb-4">
                    <div class="captcha-card @error('g-recaptcha-response') has-error @enderror" id="captchaCard">
                        <div class="captcha-card-head">
                            <span class="captcha-card-icon" aria-hidden="true">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <div class="captcha-card-text">
                                <span class="captcha-card-title">Security Check</span>
                                <span class="captcha-card-sub">Confirm you're not a robot</span>
                            </div>
                            @if(config('services.recaptcha.site_key'))
                                <span class="captcha-status" id="captchaStatus" aria-live="polite">
                                    <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                                    <span>Pending</span>
                                </span>
                            @endif
                        </div>

                        @if(config('services.recaptcha.site_key'))
                            <div class="recaptcha-box">
                                <div class="recaptcha-responsive">
                                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaSuccess" data-expired-callback="onRecaptchaExpired" data-error-callback="onRecaptchaExpired" id="loginRecaptcha"></div>
                                </div>
                            </div>
                            <script>
                                (function () {
                                    var el = document.getElementById('loginRecaptcha');
                                    if (el && document.body.classList.contains('theme-dark')) {
                                        el.setAttribute('data-theme', 'dark');
                                    }
                                })();
                            </script>
                        @else
                            <div class="recaptcha-error" role="alert">
                                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                <span>reCAPTCHA is not configured. Set RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in your environment.</span>
                            </div>
                        @endif

                        <div class="recaptcha-error" id="captchaClientError" role="alert" hidden>
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <span>Please complete the security check before signing in.</span>
                        </div>

                        @error('g-recaptcha-response')
                            <div class="recaptcha-error">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="auth-actions">
                    <button type="submit" class="btn-primary-custom w-100 hover-lift auth-submit-btn" id="submitBtn">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        <span>Sign In</span>
                    </button>
                </div>
            </form>

            @unless(app()->isProduction())
                <div class="demo-autofill-box mt-4 pt-3 border-top">
                    <div class="demo-autofill-label">
                        <i class="bi bi-lightning-charge-fill text-teal"></i>
                        <span>Quick Fill Role Credentials</span>
                    </div>
                    <div class="demo-autofill-pills">
                        <button type="button" class="demo-role-btn" data-email="htoosatth097@gmail.com" data-pass="Asdf123$" data-label="Super Admin">
                            <span class="dot bg-danger"></span>Super Admin
                        </button>
                        <button type="button" class="demo-role-btn" data-email="htoosatt99@gmail.com" data-pass="Asdf123$" data-label="Student Admin">
                            <span class="dot bg-teal"></span>Student Admin
                        </button>
                        <button type="button" class="demo-role-btn" data-email="satth2087@gmail.com" data-pass="Asdf123$" data-label="Course Admin">
                            <span class="dot bg-indigo"></span>Course Admin
                        </button>
                    </div>
                </div>
            @endunless

            <div class="auth-card-foot text-center mt-3 pt-3 border-top">
                <a href="{{ route('about.index') }}" class="auth-return-link text-muted text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span>Back to About Platform</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@if(config('services.recaptcha.site_key'))
    <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
@endif
<script>
    function setCaptchaState(verified) {
        const card = document.getElementById('captchaCard');
        const status = document.getElementById('captchaStatus');
        if (card) {
            card.classList.toggle('is-verified', verified);
            if (verified) card.classList.remove('has-error');
        }
        if (status) {
            status.innerHTML = verified
                ? '<i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Verified</span>'
                : '<i class="bi bi-hourglass-split" aria-hidden="true"></i><span>Pending</span>';
        }
    }

    function onRecaptchaSuccess() {
        document.querySelectorAll('#captchaCard .recaptcha-error').forEach(box => box.hidden = true);
        setCaptchaState(true);
    }

    function onRecaptchaExpired() {
        setCaptchaState(false);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const captchaCard = document.getElementById('captchaCard');
        const captchaClientError = document.getElementById('captchaClientError');

        if (loginForm && document.getElementById('loginRecaptcha')) {
            loginForm.addEventListener('submit', function (e) {
                const token = window.grecaptcha && typeof grecaptcha.getResponse === 'function'
                    ? grecaptcha.getResponse()
                    : '';
                if (!token) {
                    e.preventDefault();
                    if (captchaClientError) captchaClientError.hidden = false;
                    if (captchaCard) {
                        captchaCard.classList.add('has-error', 'shake');
                        setTimeout(() => captchaCard.classList.remove('shake'), 450);
                        captchaCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('bi-eye', !isPassword);
                toggleIcon.classList.toggle('bi-eye-slash', isPassword);
            });
        }

        document.querySelectorAll('.demo-role-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (emailInput) emailInput.value = this.dataset.email || '';
                if (passwordInput) passwordInput.value = this.dataset.pass || '';
                if (window.showToast) {
                    window.showToast('Filled ' + (this.dataset.label || 'Admin') + ' credentials', 'bi-key-fill text-teal');
                }
            });
        });
    });
</script>
@endpush
