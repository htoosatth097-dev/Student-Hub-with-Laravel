<footer class="app-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span>&copy; {{ date('Y') }} <strong>StudentHub</strong></span>
            <span class="version-badge">v2.0</span>
            <span class="footer-sep">&middot;</span>
            <span class="footer-tagline">Academic Registry &amp; Student Information System</span>
        </div>
        @unless(auth()->check() && auth()->user()->isStudentAdmin())
            <nav class="footer-nav" aria-label="Footer links">
                @can('viewAny', App\Models\laravelstudents::class)
                    <a href="{{ route('students.index') }}">Directory</a>
                @endcan
                @auth
                    <a href="{{ route('courses.index') }}">Courses</a>
                @endauth
                <a href="{{ route('about.index') }}">About</a>
                <a href="{{ route('contact.index') }}">Support</a>
            </nav>
        @endunless
    </div>
</footer>
