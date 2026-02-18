<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Hub Homepage</title>
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
</head>
<body>
  <nav id="jobiNav" class="navbar navbar-expand-lg navbar-light fixed-top py-2">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}" style="height: 55px;">
        </a>
        <button class="navbar-toggler border-0" type="button"
                data-toggle="collapse" data-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse mobile-menu" id="navMenu">

            <ul class="navbar-nav mx-auto text-center">
                <li class="nav-item"><a class="nav-link font-weight-bold" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" href="{{ route('user.jobs') }}">Jobs</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" href="#">Company</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" href="#">Candidates</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" href="#">Pages</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" href="#">Blog</a></li>
            </ul>

            <div class="text-center text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('user.register') }}" class="d-block d-lg-inline font-weight-bold text-dark mr-lg-2">Register</a>
                <a href="{{ route('user.login') }}" class="d-block d-lg-inline font-weight-bold text-dark mr-lg-3">Login</a>
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
            <!-- Logo + Email -->
            <div class="col-12 col-md-3 mb-4">
                <div class="footer-logo"><img src="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}"
                        style="height: 120px;"></div>
            </div>
            <!-- Services -->
            <div class="col-6 col-md-3 mb-3">
                <h6 class="font-weight-bold mb-3">Services</h6>
                <a href="#">Browse Jobs</a>
                <a href="#">Companies</a>
                <a href="#">Candidates</a>
                <a href="#">Pricing</a>
            </div>

            <!-- Company -->
            <div class="col-6 col-md-3 mb-3">
                <h6 class="font-weight-bold mb-3">Company</h6>
                <a href="#">About us</a>
                <a href="#">FAQ’s</a>
                <a href="#">Privacy</a>
                <a href="#">Contact</a>
            </div>

            <!-- Blog -->
            <div class="col-6 col-md-3 mb-3">
                <h6 class="font-weight-bold mb-3">Blog</h6>
                <a href="#">Blog List</a>
                <a href="#">Blog Grid</a>
                <a href="#">Blog Full width</a>
                <a href="#">Blog Details</a>
            </div>

        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top text-nowrap w-100">

            <span class="mb-0">
                © {{ date('Y') }} JOB HUB — All rights reserved.
            </span>

            <span class="mb-0">
                <b>Version</b> 1.0.0
            </span>

        </div>


    </footer>


    <!-- Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


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
