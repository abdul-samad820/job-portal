@extends('layouts.landing_page')
@section('title', 'Find Your Next Career Opportunity')
@section('meta_description', 'Job Hub connects job seekers with employers. Browse thousands of job openings, apply online, and take the next step in your career.')
@section('meta_canonical', route('user.home'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

{{-- ============================================================
SECTION 1: HERO — id="home"
============================================================ --}}
<div id="home">

    {{-- Background Image --}}
    <img src="{{ asset('admins/dist/img/group_image.jpg') }}"
         class="hero-bg-img"
         alt="">

    {{-- Light Overlay --}}
    <div class="hero-overlay"></div>

    {{-- Hero Content --}}
    <div class="hero-inner">

        <h1>
            Find Your Dream Job <br>
            <span>Without Any Hassle</span>
        </h1>

        <p class="hero-desc">
            Explore thousands of verified job opportunities across India.
            Connect with top companies and grow your career faster.
        </p>

        {{-- Stats --}}
        <div class="d-flex flex-wrap justify-content-center u-gap-10px">
            <span class="stat-pill">
                <i class="fas fa-briefcase mr-1 stat-icon-accent"></i>
                {{ number_format($totalJobsCount) }}+ Live Jobs
            </span>
            <span class="stat-pill">
                <i class="fas fa-building mr-1 stat-icon-accent"></i>
                {{ number_format($verifiedCompaniesCount) }}+ Verified Companies
            </span>
            <span class="stat-pill">
                <i class="fas fa-check-circle mr-1 stat-icon-accent"></i>
                Free for Job Seekers
            </span>
        </div>

        {{-- CTA Buttons --}}
        <div class="hero-btns">
            <a href="{{ route('user.jobs') }}" class="btn-hero-primary">
                <i class="fas fa-search"></i> Browse Jobs
            </a>
            <a href="{{ route('admin.login.view') }}" class="btn-hero-outline">
                <i class="fas fa-plus"></i> Post a Job
            </a>
        </div>

    </div>
</div>

{{-- ============================================================
SECTION 2: HOW IT WORKS — id="how-it-works"
============================================================ --}}
<section id="how-it-works">
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4 section-heading-row">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                Most Demanding Categories
            </h2>
        </div>

        <div class="u-mt-50px"></div>

        {{-- Coverflow --}}
        <div class="coverflow-container">
            <span class="coverflow-arrow arrow-left">&lsaquo;</span>
            <span class="coverflow-arrow arrow-right">&rsaquo;</span>

            <div id="coverflowTrack" class="coverflow-track">
                @foreach ($all_categories as $cat)
                <div class="coverflow-item">
                    <div class="p-2 text-center">
                        <img src="{{ $cat->category_image
                                ? Storage::url($cat->category_image)
                                : asset('default/category.png') }}"
                            class="u-w-55px-h-55px-fit-cover-radius-6px"
                            alt="{{ $cat->name }}">
                        <h6 class="mt-2 font-weight-bold u-fs-0-933rem">
                            {{ $cat->name }}
                        </h6>
                        <small class="text-muted">{{ $cat->jobs_count }} Jobs</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Job Listings --}}
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5 section-heading-row">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                New Job Listing
            </h2>
            <a href="{{ route('user.jobs') }}" class="btn-hire font-weight-bold text-nowrap">
                Explore all jobs →
            </a>
        </div>

        <div class="job-list-wrapper">
            @forelse ($recentJobs as $job)
            <div class="job-card">
                <div class="row align-items-center">

                    <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center mb-3 mb-lg-0">
                        <img src="{{ $job->job_image
                                ? Storage::url($job->job_image)
                                : asset('default/logo.png') }}" class="job-img" alt="{{ $job->title ?? 'Job' }}">
                        <div class="ml-3">
                            <h6 class="job-title mb-1">{{ $job->title }}</h6>
                            <span class="job-badge">{{ $job->type }}</span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6 text-md-center mb-2 mb-md-0">
                        <div class="job-meta">{{ $job->created_at->format('d M Y') }}</div>
                        <div class="job-location">{{ $job->location }}</div>
                    </div>

                    <div class="col-lg-2 col-md-6 col-6 text-md-center">
                        <span class="job-category">
                            {{ $job->category->name ?? 'No Category' }}
                        </span>
                    </div>

                    <div class="col-lg-2 col-md-12 text-lg-right text-md-center mt-3 mt-lg-0">
                        <a href="{{ route('apply_form_job_application', ['id' => $job->id]) }}"
                            class="btn-apply-modern">
                            Apply Now
                        </a>
                    </div>

                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-2x text-muted mb-3"></i>
                <p class="text-muted mb-0">No job listings available right now. Check back soon!</p>
            </div>
            @endforelse
        </div>

    </div>
</section>

{{-- ============================================================
SECTION 3: ABOUT — id="about"
============================================================ --}}
<section id="about" class="hero-section py-5">
    <div class="container">

        <div class="hero-box">
            <div class="row no-gutters">

                <div class="col-md-6">
                    <img src="{{ asset('admins/dist/img/photo.jpg') }}" class="img-fluid hero-img" alt="JobHub">
                </div>

                <div class="col-md-6 hero-content">
                    <h2>Get the job of your dreams quickly.</h2>
                    <p>Discover thousands of jobs and connect with top companies easily.</p>
                    <a href="{{ route('user.jobs') }}" class="hero-btn">
                        Find your job
                    </a>
                </div>

            </div>
        </div>

        {{-- Counters --}}
        <div class="row text-center mt-5">
            <div class="col-md-4 mb-4 mb-md-0">
                <h2 class="counter" data-target="{{ $totalJobsCount }}">0</h2>
                <p>Jobs Posted</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h2 class="counter" data-target="{{ $totalCompaniesCount }}">0</h2>
                <p>Companies Registered</p>
            </div>
            <div class="col-md-4">
                <h2 class="counter" data-target="{{ $totalUsersCount }}">0</h2>
                <p>Job Seekers Registered</p>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
SECTION 4: FAQ — id="faq"
============================================================ --}}
<section id="faq" class="faq-section py-5">
    <div class="container">

        <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block mb-4">
            Questions & Answers
        </h2>

        <div class="faq-wrapper mx-auto">
            <div id="faqAccordion">

                @foreach($faqs as $key => $faq)
                <div class="faq-item">
                    <div class="faq-question {{ $key != 0 ? 'collapsed' : '' }}"
                         data-toggle="collapse"
                         data-target="#faq{{ $key }}"
                         aria-expanded="{{ $key == 0 ? 'true' : 'false' }}">

                        {{ $faq->question }}

                        <span class="faq-icon">
                            <i class="fas fa-plus"></i>
                        </span>
                    </div>

                    <div id="faq{{ $key }}" class="collapse {{ $key == 0 ? 'show' : '' }}" data-parent="#faqAccordion">
                        <div class="faq-answer">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>

{{-- ============================================================
SECTION 5: TESTIMONIALS — id="testimonials"
============================================================ --}}
<section id="testimonials" class="py-5 u-bg-f8fbff">
    <div class="container">

        <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block mb-2">
            Trusted by Job Seekers & Companies
        </h2>
        <p class="text-muted mb-5">Real stories from people who found success on JobHub.</p>

        <div class="row">

            @forelse ($testimonials as $testimonial)
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                <div class="testimonial-card">
                    <img src="{{ $testimonial->image ? Storage::url($testimonial->image) : asset('admins/dist/img/user2-160x160.jpg') }}"
                        alt="{{ $testimonial->name }}">
                    <h6>{{ $testimonial->name }}</h6>
                    <small>
                        {{ $testimonial->designation }}
                        @if ($testimonial->designation && $testimonial->company) · @endif
                        {{ $testimonial->company }}
                    </small>
                    <p>"{{ $testimonial->review }}"</p>
                    <div class="star-rating">
                        {{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}
                    </div>
                </div>
            </div>
            @empty
            {{-- No testimonials added yet — keep the section from looking broken/empty --}}
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-quote-left fa-2x mb-3"></i>
                <p class="mb-0">Success stories will appear here soon.</p>
            </div>
            @endforelse

        </div>
    </div>
</section>

{{-- ============================================================
SECTION 6: CONTACT — id="contact"
============================================================ --}}
<section id="contact" class="py-5 u-bg-var-white">
    <div class="container">

        <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block mb-4">
            Get In Touch
        </h2>

        <div class="row">

            <div class="col-md-5 mb-4 mb-md-0">

                <p class="text-muted mb-4 u-fs-var-fs-base-lh-1-8">
                    Have a question or need help? Reach out to us — we'd love to hear from you!
                </p>

                <div class="d-flex align-items-center mb-3">
                    <div class="contact-icon mr-3"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="contact-label">Email Us</div>
                        <div class="contact-value">{{ $settings['contact_email'] }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="contact-icon mr-3"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="contact-label">Location</div>
                        <div class="contact-value">{{ $settings['contact_location'] }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="contact-icon mr-3"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="contact-label">Working Hours</div>
                        <div class="contact-value">{{ $settings['working_hours'] }}</div>
                    </div>
                </div>

                <div class="d-flex u-gap-10px">
                    @if($settings['linkedin_url'])
                        <a href="{{ $settings['linkedin_url'] }}" target="_blank" class="contact-social"><i class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if($settings['twitter_url'])
                        <a href="{{ $settings['twitter_url'] }}" target="_blank" class="contact-social"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if($settings['github_url'])
                        <a href="{{ $settings['github_url'] }}" target="_blank" class="contact-social"><i class="fab fa-github"></i></a>
                    @endif
                </div>

            </div>

            <div class="col-md-7">
                <div class="contact-form-box">

                    {{-- ✅ Success message --}}
                    @if(session('contact_success'))
                        <div class="alert alert-success d-flex align-items-center mb-4 u-radius-10px-bl-4px-solid-16a3" role="alert">
                            <i class="fas fa-check-circle mr-2 text-success"></i>
                            {{ session('contact_success') }}
                        </div>
                    @endif

                    {{-- ❌ Rate limit / server error --}}
                    @if(session('contact_error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4 u-radius-10px-bl-4px-solid-dc26" role="alert">
                            <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
                            {{ session('contact_error') }}
                        </div>
                    @endif

                    {{-- Validation errors summary --}}
                    @if($errors->any())
                        <div class="alert alert-danger mb-4 u-radius-10px-bl-4px-solid-dc26">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" id="contactForm" novalidate>
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_name" class="contact-form-label">Your Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    id="contact_name"
                                    name="name"
                                    class="contact-input @error('name') is-invalid @enderror"
                                    placeholder="e.g. Samad Khan"
                                    value="{{ old('name') }}"
                                    autocomplete="name"
                                    maxlength="100">
                                @error('name')
                                    <div class="invalid-feedback d-block form-feedback-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="contact-form-label">Email Address <span class="text-danger">*</span></label>
                                <input
                                    type="email"
                                    id="contact_email"
                                    name="email"
                                    class="contact-input @error('email') is-invalid @enderror"
                                    placeholder="your@email.com"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback d-block form-feedback-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_subject" class="contact-form-label">Subject <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="contact_subject"
                                name="subject"
                                class="contact-input @error('subject') is-invalid @enderror"
                                placeholder="e.g. I have a question about job posting"
                                value="{{ old('subject') }}"
                                maxlength="150">
                            @error('subject')
                                <div class="invalid-feedback d-block form-feedback-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="contact_message" class="contact-form-label">Message <span class="text-danger">*</span></label>
                            <textarea
                                id="contact_message"
                                name="message"
                                rows="4"
                                class="contact-input @error('message') is-invalid @enderror"
                                placeholder="Write your message here..."
                                maxlength="2000">{{ old('message') }}</textarea>
                            <div class="u-fs-0-8rem-color-9ca3af-ta-right-mt-4px">
                                <span id="charCount">0</span> / 2000
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block form-feedback-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="contact-submit-btn" id="contactSubmitBtn">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    /* ── Coverflow ── */
    document.addEventListener("DOMContentLoaded", function () {
        const track = document.getElementById("coverflowTrack");
        const items = document.querySelectorAll(".coverflow-item");
        const isMobileCoverflow = window.innerWidth < 768;

        if (track && items.length > 0 && !isMobileCoverflow) {
            let index = Math.floor(items.length / 2);

            function updateCoverflow() {
                items.forEach((item, i) => {
                    item.classList.remove("active", "left", "right");
                    if (i === index)     item.classList.add("active");
                    else if (i < index)  item.classList.add("left");
                    else                 item.classList.add("right");
                });
                const itemWidth = 230;
                const offset = -(index * itemWidth) + (window.innerWidth / 2 - itemWidth / 2);
                track.style.transform = `translateX(${offset}px)`;
            }

            updateCoverflow();

            const leftBtn  = document.querySelector(".arrow-left");
            const rightBtn = document.querySelector(".arrow-right");

            if (leftBtn)  leftBtn.onclick  = () => { index = Math.max(0, index - 1); updateCoverflow(); };
            if (rightBtn) rightBtn.onclick = () => { index = Math.min(items.length - 1, index + 1); updateCoverflow(); };

            setInterval(() => { index = (index + 1) % items.length; updateCoverflow(); }, 3000);
        } else if (track && items.length > 0 && isMobileCoverflow) {
            /* Mobile: no JS-driven transform/auto-rotate — the row is a
               native swipeable scroll-snap list (see landing_page.css),
               so the browser's own touch scrolling handles the UX. */
            track.style.transform = "none";
        }

        /* ── Scroll Animation ── */
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) entry.target.classList.add("show");
            });
        }, { threshold: 0.2 });

        document.querySelectorAll("[data-animate]").forEach(function (el) {
            observer.observe(el);
        });
    });

    /* ── Counter Animation ── */
    function formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + "M";
        if (num >= 1000)    return (num / 1000).toFixed(1) + "K+";
        return Math.floor(num);
    }

    let counterStarted = false;
    window.addEventListener("scroll", function () {
        const section = document.querySelector(".counter");
        if (!counterStarted && section && section.getBoundingClientRect().top < window.innerHeight) {
            counterStarted = true;
            document.querySelectorAll(".counter").forEach(counter => {
                const target = +counter.getAttribute("data-target");
                let count = 0;
                const speed = target / 100;
                const update = () => {
                    if (count < target) {
                        count += speed;
                        counter.innerText = formatNumber(count);
                        requestAnimationFrame(update);
                    } else {
                        counter.innerText = formatNumber(target);
                    }
                };
                update();
            });
        }
    });

    /* ── Contact Form — loading state + char counter ── */
    (function () {
        // Character counter for message textarea
        const msgArea  = document.getElementById('contact_message');
        const charCount = document.getElementById('charCount');
        if (msgArea && charCount) {
            // Set initial count if old() value is present
            charCount.textContent = msgArea.value.length;
            msgArea.addEventListener('input', function () {
                charCount.textContent = this.value.length;
            });
        }

        // Show loading spinner on submit
        const form = document.getElementById('contactForm');
        const btn  = document.getElementById('contactSubmitBtn');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            });
        }

        // Auto-scroll to contact section if there are errors/success
        @if($errors->any() || session('contact_success') || session('contact_error'))
            document.addEventListener('DOMContentLoaded', function () {
                const section = document.getElementById('contact');
                if (section) {
                    setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 200);
                }
            });
        @endif
    }());
</script>
@endpush