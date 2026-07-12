<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <meta name="description" content="An error occurred on Job Hub. Please try again or return to the homepage.">
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
</head>

<body class="hold-transition error-page">

    <div class="d-flex flex-column align-items-center justify-content-center error-wrapper">

        <a href="{{ url('/') }}" class="error-brand mb-4 text-decoration-none">
            <i class="fas fa-briefcase mr-2"></i>Job Hub
        </a>

        <div class="error-card text-center">

            <h1 class="display-1 @yield('color') mb-3">
                @yield('code')
            </h1>

            <h4 class="mb-3">
                <i class="@yield('icon') mr-2"></i>
                @yield('heading')
            </h4>

            <p class="text-muted mb-4">
                @yield('message')
            </p>

            <a href="{{ url('/') }}" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>

            @yield('extra')

        </div>

    </div>

    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admins/dist/js/adminlte.min.js') }}"></script>

</body>
</html>