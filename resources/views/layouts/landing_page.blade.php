<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        // Sensible defaults so any page that forgets to set a section
        // still gets a real, non-generic <title>/description instead of
        // one static string site-wide (the old behaviour — this layout
        // never actually yielded the per-page @section('title') before).
        $seoTitle = trim($__env->yieldContent('title', 'Job Hub')) ?: 'Job Hub';
        $seoTitleFull = $seoTitle === 'Job Hub' ? 'Job Hub — Find Your Next Career Opportunity' : $seoTitle.' | Job Hub';
        $seoDescription = trim($__env->yieldContent('meta_description', 'Job Hub connects job seekers with employers. Browse job openings, apply online, and find your next career opportunity.'));
        $seoImage = trim($__env->yieldContent('meta_image', asset('admins/dist/img/Job_Hub_Logo_Design.png')));
        $seoCanonical = trim($__env->yieldContent('meta_canonical', url()->current()));
        $seoRobots = trim($__env->yieldContent('meta_robots', 'index, follow'));
    @endphp

    <title>{{ $seoTitleFull }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Job Hub">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoTitleFull }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitleFull }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing_page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    @stack('styles')

    {{-- Structured data (JSON-LD) — pages push their own via @push('schema') --}}
    @stack('schema')
</head>

<body>
    <nav id="jobiNav" class="navbar navbar-expand-lg navbar-light fixed-top py-2">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img class="u-h-55px" src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" alt="Job Hub logo">
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse mobile-menu" id="navMenu">

                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{route('user.home')}}">Home</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{ route('user.jobs')}} "> Browse Jobs</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{url('/#about')}}">  About</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{url('/#how-it-works')}} ">  How It Works</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{ url('/#testimonials') }}">  Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{ url('/#faq') }}"> FAQ</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="{{ url('/#contact') }}">  Contact</a></li>
                </ul>

                <div class="text-center text-lg-right mt-3 mt-lg-0">
                    <a href="{{ route('user.register') }}"
                        class="d-block d-lg-inline font-weight-bold text-dark mr-lg-2">Register</a>
                    <a href="{{ route('user.login') }}"
                        class="d-block d-lg-inline font-weight-bold text-dark mr-lg-3">Login</a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-hire mt-2 mt-lg-0">Go To Profile</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0 position-relative">
        @yield('content')
    </div>

    <footer class="container mt-5 pt-4 pb-4">
    <div class="row text-center text-md-left">

        {{-- Logo + Tagline --}}
        <div class="col-12 col-md-4 mb-4">
            <div class="footer-logo mb-3">
                <img class="u-h-100px" src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" alt="Job Hub logo">
            </div>
            <p class="text-muted small mb-3">
                Connecting talented professionals with top companies across India.
                Find your dream job or hire the best talent — all in one place.
            </p>

            {{-- Social Icons --}}
            <div class="d-flex justify-content-center justify-content-md-start u-gap-10px">
                <a href="#" class="social-icon">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://github.com/yourusername/job-portal"
                   target="_blank" class="social-icon">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>

        {{-- Job Seekers --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase u-fs-var-fs-xs-ls-12em">
                Job Seekers
            </h6>
            <a href="{{ route('user.jobs') }}">Browse Jobs</a>
            <a href="{{ route('user.register') }}">Create Account</a>
            <a href="{{ route('user.login') }}">Login</a>
            <a href="{{ route('user.saved.jobs') }}">Saved Jobs</a>
            <a href="{{ route('job.alert.index') }}">Job Alerts</a>
            <a href="{{ route('user.resume_builder') }}">Resume Builder</a>
            <a href="{{ route('user.salary_insights') }}">Salary Insights</a>
            <a href="{{ route('user.compare') }}">Compare Jobs</a>
            <a href="{{ url('/#how-it-works') }}">How It Works</a>
        </div>

        {{-- For Companies --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase u-fs-var-fs-xs-ls-12em">
                For Companies
            </h6>
            <a href="{{ route('admin.login.view') }}">Post a Job</a>
            <a href="{{ route('admin.login.view') }}">Employer Login</a>
            <a href="{{ url('/#faq') }}">FAQ's</a>
            <a href="{{ url('/#contact') }}">Contact Us</a>
        </div>

        {{-- Quick Links --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase u-fs-var-fs-xs-ls-12em">
                Quick Links
            </h6>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/#about') }}">About</a>
            <a href="{{ url('/#testimonials') }}">Testimonials</a>
            <a href="{{ url('/#faq') }}">FAQ</a>
            <a href="{{ url('/#contact') }}">Contact</a>
            <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
            <a href="{{ route('terms.conditions') }}">Terms & Conditions</a>
        </div>

        {{-- Platform Stats --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase u-fs-var-fs-xs-ls-12em">
                JobHub At a Glance
            </h6>
            <p class="text-muted small mb-1">
                <i class="fas fa-briefcase mr-1 u-color-2563eb"></i>
                {{ $totalJobsCount ?? 0 }}+ Live Jobs
            </p>
            <p class="text-muted small mb-1">
                <i class="fas fa-building mr-1 u-color-2563eb"></i>
                {{ $totalCompaniesCount ?? 0 }}+ Companies
            </p>
            <p class="text-muted small">
                <i class="fas fa-users mr-1 u-color-2563eb"></i>
                {{ $totalUsersCount ?? 0 }}+ Job Seekers
            </p>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap u-gap-8px">
        <span class="footer-copy">
            © {{ date('Y') }} <strong>JobHub</strong> — All rights reserved.
        </span>
        <div class="u-gap-8px">
            <a href="{{ route('privacy.policy') }}" class="footer-copy mr-3">Privacy Policy</a>
            <a href="{{ route('terms.conditions') }}" class="footer-copy">Terms & Conditions</a>
        </div>
    </div>
</footer>
    <!-- Bootstrap Scripts -->
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        window.addEventListener("scroll", function() {
            const nav = document.getElementById("jobiNav");

            if (window.innerWidth > 991) { // Desktop only
                if (window.scrollY > 80) {
                    nav.classList.add("nav-scrolled");
                } else {
                    nav.classList.remove("nav-scrolled");
                }
            }
        });
    </script>
    @stack('scripts')
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>

</html>