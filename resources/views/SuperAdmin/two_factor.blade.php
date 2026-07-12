<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Login | Job Hub</title>

    <!-- Bootstrap 4 comes bundled inside AdminLTE's own stylesheet in this project -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin-pro.css') }}">
</head>

<body>

    <div class="sa-auth" style="max-width:none;">
        <div class="card border-0 shadow-lg" style="border-radius:20px; max-width:420px; width:100%;">
            <div class="card-body p-5">

                <div class="text-center mb-4">
                    <div class="sa-auth-icon-badge mx-auto mb-3">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <h5 class="font-weight-bold mb-1" style="font-family:'Montserrat',sans-serif;">Check Your Email</h5>
                    <p class="text-muted small mb-0">
                        We sent a 6-digit code to your email address. It expires in 10 minutes.
                    </p>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    {{ $errors->first() }}
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success py-2 small">{{ session('success') }}</div>
                @endif

                <form action="{{ route('superadmin.2fa.verify') }}" method="POST">
                    @csrf
                    <input type="text" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                        class="form-control code-input mb-3" placeholder="------" required autofocus>
                    <button type="submit" class="btn btn-primary btn-block">Verify &amp; Continue</button>
                </form>

                <form action="{{ route('superadmin.2fa.resend') }}" method="POST" class="text-center mt-3">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm text-decoration-none">Resend code</button>
                </form>

                <div class="text-center">
                    <a href="{{ route('superadmin.login.view') }}" class="small text-muted">&larr; Back to login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>

</html>