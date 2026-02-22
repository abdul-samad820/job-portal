<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">

    <style>
        body {
            background: #f4f6f9;
        }

        .error-wrapper {
            height: 100vh;
        }

        .error-card {
            width: 420px;
            padding: 40px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body class="hold-transition">

    <div class="d-flex align-items-center justify-content-center error-wrapper">

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
