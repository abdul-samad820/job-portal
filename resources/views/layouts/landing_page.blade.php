<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Hub Homepage</title>
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing_page.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    @stack('styles')
</head>

<body>
    <nav id="jobiNav" class="navbar navbar-expand-lg navbar-light fixed-top py-2">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" style="height: 55px;">
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
                <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}"
                     style="height: 100px;">
            </div>
            <p class="text-muted small mb-3">
                Connecting talented professionals with top companies across India.
                Find your dream job or hire the best talent — all in one place.
            </p>

            {{-- Social Icons --}}
            <div class="d-flex justify-content-center justify-content-md-start" style="gap:10px;">
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
            <h6 class="font-weight-bold mb-3 text-uppercase"
                style="font-size:11px; letter-spacing:.12em;">
                Job Seekers
            </h6>
            <a href="{{ route('user.jobs') }}">Browse Jobs</a>
            <a href="{{ route('user.register') }}">Create Account</a>
            <a href="{{ route('user.login') }}">Login</a>
            <a href="{{ url('/#how-it-works') }}">How It Works</a>
        </div>

        {{-- For Companies --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase"
                style="font-size:11px; letter-spacing:.12em;">
                For Companies
            </h6>
            <a href="{{ route('admin.register.view') }}">Post a Job</a>
            <a href="{{ route('admin.login.view') }}">Admin Login</a>
            <a href="{{ url('/#faq') }}">FAQ's</a>
            <a href="{{ url('/#contact') }}">Contact Us</a>
        </div>

        {{-- Quick Links --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase"
                style="font-size:11px; letter-spacing:.12em;">
                Quick Links
            </h6>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/#about') }}">About</a>
            <a href="{{ url('/#faq') }}">FAQ</a>
            <a href="{{ url('/#contact') }}">Contact</a>
        </div>

        {{-- Contact --}}
        <div class="col-6 col-md-2 mb-3">
            <h6 class="font-weight-bold mb-3 text-uppercase"
                style="font-size:11px; letter-spacing:.12em;">
                Contact
            </h6>
            <p class="text-muted small mb-1">
                <i class="fas fa-envelope mr-1" style="color:#2563eb;"></i>
                support@jobhub.com
            </p>
            <p class="text-muted small mb-1">
                <i class="fas fa-map-marker-alt mr-1" style="color:#2563eb;"></i>
                New Delhi, India
            </p>
            <p class="text-muted small">
                <i class="fas fa-clock mr-1" style="color:#2563eb;"></i>
                Mon–Sat, 9AM–6PM
            </p>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div class="d-flex justify-content-between align-items-center
                mt-4 pt-3 border-top flex-wrap" style="gap:8px;">
        <span class="footer-copy">
            © {{ date('Y') }} <strong>JobHub</strong> — All rights reserved.
        </span>
    </div>
</footer>
    <!-- Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
</body>

</html>
