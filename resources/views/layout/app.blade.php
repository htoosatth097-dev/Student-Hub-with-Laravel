<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'StudentHub — Academic Information System')</title>

    <!-- Google Fonts: Space Grotesk (Headings) + Plus Jakarta Sans (Body) + JetBrains Mono (Codes) + Noto Sans Myanmar -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Noto+Sans+Myanmar:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Shared Application Design System Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ @filemtime(public_path('css/style.css')) ?: '2.1' }}">

    @stack('styles')
</head>
<body class="app-body">
    <script>
        (function () {
            try {
                if (localStorage.getItem('sh_theme') === 'dark') {
                    document.body.classList.add('theme-dark');
                }
            } catch (e) {}
        })();
    </script>

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div class="app-wrapper">
        <div class="app-main">
            <!-- Top bar (brand + navigation + search + user) -->
            @unless(request()->routeIs('login', 'password.request', 'password.otp.reset'))
                @include('partials.header')
            @endunless

            <!-- Main Content Area -->
            <main class="app-content" id="main-content">
                <div class="app-content-container">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <!-- Floating Toast Container -->
    <div id="shToastContainer" class="sh-toast-container" aria-live="polite" aria-atomic="true"></div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Global App UI Scripts -->
    <script>
        window.showToast = function (message, iconClass) {
            const container = document.getElementById('shToastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = 'sh-toast';
            const icon = document.createElement('i');
            icon.className = 'bi ' + (iconClass || 'bi-check-circle-fill text-teal');
            const span = document.createElement('span');
            span.textContent = message;
            toast.appendChild(icon);
            toast.appendChild(span);
            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 250);
            }, 2600);
        };

        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const sidebar = document.getElementById('appSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            const closeSidebar = function () {
                if (sidebar) sidebar.classList.remove('show');
                if (sidebarOverlay) sidebarOverlay.classList.remove('show');
                if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'false');
            };

            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function () {
                    const isShown = sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                    sidebarToggle.setAttribute('aria-expanded', isShown ? 'true' : 'false');
                });

                sidebarOverlay.addEventListener('click', closeSidebar);

                if (sidebarCloseBtn) {
                    sidebarCloseBtn.addEventListener('click', closeSidebar);
                }

                sidebar.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', closeSidebar);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && sidebar.classList.contains('show')) {
                        closeSidebar();
                        sidebarToggle.focus();
                    }
                });
            }

            // Theme toggle button (Light / Dark mode)
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeToggleIcon = document.getElementById('themeToggleIcon');
            const syncThemeIcon = function () {
                if (!themeToggleIcon) return;
                const isDark = document.body.classList.contains('theme-dark');
                themeToggleIcon.className = isDark ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars';
            };
            syncThemeIcon();

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const isDark = document.body.classList.toggle('theme-dark');
                    try {
                        localStorage.setItem('sh_theme', isDark ? 'dark' : 'light');
                    } catch (e) {}
                    syncThemeIcon();
                    window.showToast(isDark ? 'Dark theme enabled' : 'Light theme enabled', isDark ? 'bi-moon-stars-fill' : 'bi-sun-fill');
                });
            }

            // Copy student code buttons globally
            document.querySelectorAll('.btn-copy-code').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const code = this.dataset.code;
                    if (!code) return;

                    const indicateCopied = () => {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check2 text-success"></i>';
                        window.showToast('Copied ' + code + ' to clipboard');
                        setTimeout(() => { this.innerHTML = originalHtml; }, 1500);
                    };

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(code).then(indicateCopied).catch(() => {});
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = code;
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                        indicateCopied();
                    }
                });
            });

            // Keyboard shortcut '/' or 'Ctrl+K' to focus search input if available
            document.addEventListener('keydown', function (e) {
                if ((e.key === '/' || (e.ctrlKey && e.key.toLowerCase() === 'k')) && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                    const searchBox = document.getElementById('studentSearchInput') || document.getElementById('courseSearchInput') || document.getElementById('globalSearchInput');
                    if (searchBox) {
                        e.preventDefault();
                        searchBox.focus();
                        searchBox.select();
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
