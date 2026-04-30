@extends('layouts.landing_page')

@push('styles')
<style>

    #home {
        padding-top: 76px; /* navbar height ke barabar */
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    /* Background image properly set ho */
    #home .hero-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        z-index: 0;
    }

    /* Overlay — image ke upar light overlay taaki text readable rahe */
    #home .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.55); /* white tint — adjust karo as needed */
        z-index: 1;
    }

    /* Hero content upar */
    #home .hero-inner {
        position: relative;
        z-index: 2;
        min-height: calc(100vh - 76px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 20px;
    }

    /* Hero heading */
    #home h1 {
        font-size: clamp(28px, 5vw, 52px);
        font-weight: 800;
        line-height: 1.15;
        color: #0d1b2a;
        margin-bottom: 16px;
        font-family: 'Montserrat', sans-serif;
        letter-spacing: -0.02em;
    }

    #home h1 span {
        color: #60a5fa;
    }

    /* Description */
    #home .hero-desc {
        max-width: 620px;
        font-size: clamp(15px, 2vw, 18px);
        color: #3a4a5c;
        line-height: 1.75;
        margin-bottom: 28px;
    }

    /* Stat pills */
    #home .stat-pill {
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid rgba(37, 99, 235, 0.18);
        backdrop-filter: blur(6px);
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        color: #1e3a8a;
    }

    /* CTA Buttons */
    #home .hero-btns {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 28px;
    }

    #home .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 12px 28px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        transition: 0.25s;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
    }

    #home .btn-hero-primary:hover {
        background: #1e40af;
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(37, 99, 235, 0.45);
        color: #fff;
        text-decoration: none;
    }

    #home .btn-hero-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 12px 28px;
        border: 2px solid #2563eb;
        color: #2563eb;
        border-radius: 50px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        transition: 0.25s;
    }

    #home .btn-hero-outline:hover {
        background: #2563eb;
        color: #fff;
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* ── NAVBAR: always solid on mobile, transparent on desktop ── */
    @media (max-width: 991px) {
        #jobiNav {
            background: #ffffff !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08) !important;
        }
    }

    /* ── MOBILE HERO ── */
    @media (max-width: 767px) {
        #home {
            padding-top: 70px;
        }

        #home .hero-inner {
            min-height: calc(100vh - 70px);
            padding: 150px 67px;
        }

        #home h1 {
            font-size: 28px;
        }

        #home .hero-desc {
            font-size: 15px;
        }

        #home .hero-btns {
            flex-direction: column;
            align-items: center;
        }

        #home .btn-hero-primary,
        #home .btn-hero-outline {
            width: 100%;
            max-width: 320px;
            justify-content: center;
        }

        #home .stat-pill {
            font-size: 12px;
            padding: 6px 14px;
        }
    }

    @media (max-width: 420px) {
        #home h1 { font-size: 24px; }
    }
</style>
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
        <div class="d-flex flex-wrap justify-content-center" style="gap:10px;">
            <span class="stat-pill">
                <i class="fas fa-briefcase mr-1" style="color:#2563eb;"></i>
                1,200+ Live Jobs
            </span>
            <span class="stat-pill">
                <i class="fas fa-building mr-1" style="color:#2563eb;"></i>
                350+ Verified Companies
            </span>
            <span class="stat-pill">
                <i class="fas fa-check-circle mr-1" style="color:#2563eb;"></i>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                Most Demanding Categories
            </h2>
        </div>

        <div style="margin-top:50px;"></div>

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
                            style="width:55px; height:55px; object-fit:cover; border-radius:6px;"
                            alt="{{ $cat->name }}">
                        <h6 class="mt-2 font-weight-bold" style="font-size:14px;">
                            {{ $cat->name }}
                        </h6>
                        <small class="text-muted">{{ $cat->jobs_count }} Jobs</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Job Listings --}}
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                New Job Listing
            </h2>
            <a href="{{ route('user.jobs') }}" class="btn-hire font-weight-bold text-nowrap">
                Explore all jobs →
            </a>
        </div>

        <div class="job-list-wrapper">
            @foreach ($recentJobs as $job)
            <div class="job-card">
                <div class="row align-items-center">

                    <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center mb-3 mb-lg-0">
                        <img src="{{ $job->job_image
                                ? Storage::url($job->job_image)
                                : asset('default/logo.png') }}" class="job-img">
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
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
SECTION 3: ABOUT — id="about"
============================================================ --}}
<section id="about" class="hero-section py-5">
    <div class="container">

        <div class="hero-box">
            <div class="row align-items-center no-gutters">

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
                <h2 class="counter" data-target="1200">0</h2>
                <p>Jobs Posted</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h2 class="counter" data-target="350">0</h2>
                <p>Companies Registered</p>
            </div>
            <div class="col-md-4">
                <h2 class="counter" data-target="5000">0</h2>
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
SECTION 5: TESTIMONIALS
============================================================ --}}
<section class="py-5" style="background:#f8fbff;">
    <div class="container">

        <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block mb-2">
            Trusted by Job Seekers & Companies
        </h2>
        <p class="text-muted mb-5">Real stories from people who found success on JobHub.</p>

        <div class="row">

            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                <div class="testimonial-card">
                    <img src="{{ asset('admins/dist/img/girl1.jpg') }}" alt="Priya Patel">
                    <h6>Priya Patel</h6>
                    <small>UI/UX Designer · Pune</small>
                    <p>"Got shortlisted within 3 days of applying. The job alert feature notified me the moment a matching role was posted. Best job portal I have used!"</p>
                    <div style="color:#f59e0b; font-size:13px; margin-top:8px;">★★★★★</div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                <div class="testimonial-card">
                    <img src="{{ asset('admins/dist/img/boy3.jpg') }}" alt="Rohit Sharma">
                    <h6>Rohit Sharma</h6>
                    <small>HR Manager · TechCorp India</small>
                    <p>"We hired 4 developers in 2 weeks using JobHub. The skill-matching feature saved us hours of manual screening. Interview scheduling is a game changer!"</p>
                    <div style="color:#f59e0b; font-size:13px; margin-top:8px;">★★★★★</div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                <div class="testimonial-card">
                    <img src="{{ asset('admins/dist/img/boy1.jpg') }}" alt="Amit Verma">
                    <h6>Amit Verma</h6>
                    <small>Backend Developer · Hyderabad</small>
                    <p>"The resume builder helped me create a professional PDF in minutes. Applied to 5 jobs and got 2 interview calls the very next day!"</p>
                    <div style="color:#f59e0b; font-size:13px; margin-top:8px;">★★★★☆</div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                <div class="testimonial-card">
                    <img src="{{ asset('admins/dist/img/girl2.jpg') }}" alt="Neha Singh">
                    <h6>Neha Singh</h6>
                    <small>Founder · DesignStudio Pune</small>
                    <p>"As a small agency, finding the right talent was tough. JobHub made it simple — quality candidates, easy application management, and great support."</p>
                    <div style="color:#f59e0b; font-size:13px; margin-top:8px;">★★★★★</div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================
SECTION 6: CONTACT — id="contact"
============================================================ --}}
<section id="contact" class="py-5" style="background:#ffffff;">
    <div class="container">

        <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block mb-4">
            Get In Touch
        </h2>

        <div class="row">

            <div class="col-md-5 mb-4 mb-md-0">

                <p class="text-muted mb-4" style="font-size:15px; line-height:1.8;">
                    Have a question or need help? Reach out to us — we'd love to hear from you!
                </p>

                <div class="d-flex align-items-center mb-3">
                    <div class="contact-icon mr-3"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="contact-label">Email Us</div>
                        <div class="contact-value">support@jobhub.com</div>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="contact-icon mr-3"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="contact-label">Location</div>
                        <div class="contact-value">New Delhi, India</div>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="contact-icon mr-3"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="contact-label">Working Hours</div>
                        <div class="contact-value">Mon – Sat, 9 AM – 6 PM</div>
                    </div>
                </div>

                <div class="d-flex" style="gap:10px;">
                    <a href="#" class="contact-social"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="contact-social"><i class="fab fa-twitter"></i></a>
                    <a href="https://github.com/yourusername/job-portal" target="_blank" class="contact-social"><i class="fab fa-github"></i></a>
                </div>

            </div>

            <div class="col-md-7">
                <div class="contact-form-box">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="contact-form-label">Your Name</label>
                            <input type="text" class="contact-input" placeholder="e.g. Samad Khan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="contact-form-label">Email Address</label>
                            <input type="email" class="contact-input" placeholder="your@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="contact-form-label">Subject</label>
                        <input type="text" class="contact-input" placeholder="e.g. I have a question about job posting">
                    </div>

                    <div class="mb-4">
                        <label class="contact-form-label">Message</label>
                        <textarea class="contact-input" rows="4" placeholder="Write your message here..."></textarea>
                    </div>

                    <button type="button" class="contact-submit-btn" id="contactSubmitBtn">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Message
                    </button>

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

        if (track && items.length > 0) {
            let index = Math.floor(items.length / 2);

            function updateCoverflow() {
                items.forEach((item, i) => {
                    item.classList.remove("active", "left", "right");
                    if (i === index)     item.classList.add("active");
                    else if (i < index)  item.classList.add("left");
                    else                 item.classList.add("right");
                });
                const itemWidth = window.innerWidth < 768 ? 150 : 230;
                const offset = -(index * itemWidth) + (window.innerWidth / 2 - itemWidth / 2);
                track.style.transform = `translateX(${offset}px)`;
            }

            updateCoverflow();

            const leftBtn  = document.querySelector(".arrow-left");
            const rightBtn = document.querySelector(".arrow-right");

            if (leftBtn)  leftBtn.onclick  = () => { index = Math.max(0, index - 1); updateCoverflow(); };
            if (rightBtn) rightBtn.onclick = () => { index = Math.min(items.length - 1, index + 1); updateCoverflow(); };

            setInterval(() => { index = (index + 1) % items.length; updateCoverflow(); }, 3000);
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

    /* ── Contact Form ── */
    document.getElementById("contactSubmitBtn")?.addEventListener("click", function () {
        const name    = document.querySelector(".contact-input[placeholder*='Samad']")?.value;
        const email   = document.querySelector(".contact-input[placeholder*='email']")?.value;
        const message = document.querySelector("textarea.contact-input")?.value;

        if (!name || !email || !message) {
            alert("Please fill in all fields before sending.");
            return;
        }

        this.innerHTML = '<i class="fas fa-check mr-2"></i> Message Sent!';
        this.style.background = "#16a34a";
        this.disabled = true;
    });
</script>
@endpush