{{-- Reusable Flash & Error Banner Partial --}}
@if(session('success'))
    <div class="banner-flash success animate-fade-up mb-4" role="alert" id="flashAlert">
        <div class="banner-icon-box">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        </div>
        <div class="banner-content">
            <div class="banner-title">{{ $successTitle ?? 'Success' }}</div>
            <div class="banner-text">{{ session('success') }}</div>
        </div>
        <button type="button" class="banner-close-btn" onclick="this.closest('.banner-flash').remove()" aria-label="Dismiss alert">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

@if(session('warning'))
    <div class="banner-flash warning animate-fade-up mb-4" role="alert" id="warningAlert">
        <div class="banner-icon-box">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        </div>
        <div class="banner-content">
            <div class="banner-title">{{ $warningTitle ?? 'Notice' }}</div>
            <div class="banner-text">{{ session('warning') }}</div>
        </div>
        <button type="button" class="banner-close-btn" onclick="this.closest('.banner-flash').remove()" aria-label="Dismiss alert">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

@if(!empty($showErrors) && isset($errors) && $errors->any())
    <div class="banner-flash danger mb-4 animate-fade-in" role="alert">
        <div class="banner-icon-box">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        </div>
        <div class="banner-content">
            <div class="banner-title">{{ $errorTitle ?? 'Please review the following input errors:' }}</div>
            <ul class="banner-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="banner-close-btn" onclick="this.closest('.banner-flash').remove()" aria-label="Dismiss alert">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

