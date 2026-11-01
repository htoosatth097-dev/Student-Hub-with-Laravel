@extends('layout.app')

@section('title', 'Contact Support | StudentHub')

@section('content')
<div class="form-page-layout animate-fade-in">

    {{-- Left column: Intro & contact details & FAQs --}}
    <aside class="form-intro-column animate-fade-up">
        @can('viewAny', App\Models\laravelstudents::class)
            <a href="{{ route('students.index') }}" class="back-link">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>Back to Student Directory</span>
            </a>
        @else
            <a href="{{ route('about.index') }}" class="back-link">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>Back to Platform Overview</span>
            </a>
        @endcan

        <div class="section-label">
            <i class="bi bi-chat-dots-fill me-1"></i>
            <span>Support &amp; Inquiries</span>
        </div>
        <h1 class="page-title">Contact Support</h1>
        <p class="page-instruction">Reach out to academic registry personnel or report student data discrepancies.</p>

        <div class="contact-info-cards mt-4">
            <div class="contact-card-item">
                <div class="contact-icon-box teal">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                    <span class="contact-item-label">Registry Office</span>
                    <p class="contact-item-val">Academic Building A, Room 204</p>
                </div>
            </div>

            <div class="contact-card-item">
                <div class="contact-icon-box indigo">
                    <i class="bi bi-envelope-at-fill"></i>
                </div>
                <div>
                    <span class="contact-item-label">Direct Email</span>
                    <p class="contact-item-val font-mono">support@studenthub.local</p>
                </div>
            </div>

            <div class="contact-card-item">
                <div class="contact-icon-box amber">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <span class="contact-item-label">Working Hours</span>
                    <p class="contact-item-val">Mon – Fri · 09:00 – 17:00 MMT</p>
                </div>
            </div>
        </div>

        {{-- Quick Interactive FAQ Box --}}
        <div class="faq-box">
            <div class="faq-title">
                <i class="bi bi-question-circle-fill text-teal"></i>
                <span>Frequently Asked Questions</span>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('is-open'); this.nextElementSibling.classList.toggle('d-none');">
                    <span>How do I modify student records?</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="faq-answer d-none">
                    Navigate to Student Directory, locate the student row, and click the <strong>Edit</strong> button.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('is-open'); this.nextElementSibling.classList.toggle('d-none');">
                    <span>Can I print an official dossier?</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="faq-answer d-none">
                    Yes! Click <strong>View</strong> on any student, then click <strong>Print Dossier</strong> for a print-formatted official document.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('is-open'); this.nextElementSibling.classList.toggle('d-none');">
                    <span>How do I create a new course?</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="faq-answer d-none">
                    Go to <strong>Courses</strong> in the navigation bar to add a course program before assigning students to it.
                </div>
            </div>
        </div>
    </aside>

    {{-- Right column: Contact form --}}
    <div class="form-body-column animate-fade-up">

        <div id="contactSuccessMsg" class="banner-flash success mb-4 d-none animate-fade-in" role="alert">
            <div class="banner-icon-box">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            </div>
            <div class="banner-content">
                <div class="banner-title">Message Sent Successfully!</div>
                <div class="banner-text">Thank you for reaching out. The academic registry team will review your inquiry shortly.</div>
            </div>
            <button type="button" class="banner-close-btn" onclick="document.getElementById('contactSuccessMsg').classList.add('d-none')" aria-label="Dismiss">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="contactForm" action="#" method="POST" novalidate autocomplete="off">
            @csrf

            <section class="form-section">
                <div class="form-section-header">
                    <span class="section-number-pill">1</span>
                    <h2 class="form-section-title">Send Us a Direct Message</h2>
                </div>
                <div class="form-fields-grid">

                    <div class="field-group">
                        <label for="contact_name" class="field-label">
                            <span>Your Name</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-person input-icon" aria-hidden="true"></i>
                            <input
                                type="text"
                                class="field-input with-icon"
                                id="contact_name"
                                name="contact_name"
                                value="{{ auth()->user()->name ?? '' }}"
                                placeholder="e.g. Daw Mya"
                                required
                            >
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="contact_email" class="field-label">
                            <span>Your Email</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                            <input
                                type="email"
                                class="field-input with-icon"
                                id="contact_email"
                                name="contact_email"
                                value="{{ auth()->user()->email ?? '' }}"
                                placeholder="e.g. mya@example.com"
                                required
                            >
                        </div>
                    </div>

                    <div class="field-group full-width">
                        <label for="contact_subject" class="field-label">
                            <span>Subject</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="bi bi-chat-left-text input-icon" aria-hidden="true"></i>
                            <input
                                type="text"
                                class="field-input with-icon"
                                id="contact_subject"
                                name="contact_subject"
                                placeholder="e.g. Student record correction or transfer inquiry"
                                required
                            >
                        </div>
                    </div>

                    <div class="field-group full-width">
                        <label for="contact_message" class="field-label">
                            <span>Message Inquiry</span> <span class="req-star">*</span>
                        </label>
                        <div class="input-with-icon textarea-icon-wrapper">
                            <i class="bi bi-textarea-t input-icon" aria-hidden="true"></i>
                            <textarea
                                class="field-textarea with-icon"
                                id="contact_message"
                                name="contact_message"
                                rows="5"
                                placeholder="Describe your request or question in detail..."
                                required
                            ></textarea>
                        </div>
                    </div>

                </div>
            </section>

            <div class="form-action-row">
                @can('viewAny', App\Models\laravelstudents::class)
                    <a href="{{ route('students.index') }}" class="btn-cancel-link hover-lift">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span>Back to Directory</span>
                    </a>
                @else
                    <a href="{{ route('about.index') }}" class="btn-cancel-link hover-lift">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span>Back to Overview</span>
                    </a>
                @endcan
                <div class="action-buttons-end">
                    <button type="submit" class="btn-primary-custom hover-lift">
                        <i class="bi bi-send-fill me-1"></i>
                        <span>Send Message</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contactForm');
        const successBox = document.getElementById('contactSuccessMsg');

        if (form && successBox) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                successBox.classList.remove('d-none');
                form.reset();
                if (window.showToast) {
                    window.showToast('Support inquiry submitted!', 'bi-send-check-fill text-teal');
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
</script>
@endpush
