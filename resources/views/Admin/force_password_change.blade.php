<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set Your Password | Job Hub</title>

    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-pro.css') }}">
    <style>
        html, body { margin: 0; padding: 0; min-height: 100%; overflow-x: hidden; }
        *, *::before, *::after { box-sizing: border-box; }
    </style>
</head>

<body>

    <div class="sa-auth" style="max-width:none;">
        <div class="card border-0 shadow-lg" style="border-radius:20px; max-width:440px; width:100%;">
            <div class="card-body p-5">

                <div class="text-center mb-4">
                    <div class="sa-auth-icon-badge mx-auto mb-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="font-weight-bold mb-1" style="font-family:'Montserrat',sans-serif;">Set Your Password</h5>
                    <p class="text-muted small mb-0">
                        For security, please set your own password before continuing. The password used to create this account was chosen by Super Admin.
                    </p>
                </div>

                @if (session('warning'))
                    <div class="alert alert-warning py-2 small">{{ session('warning') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.force_password.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="password">New Password</label>
                        <div class="sa-input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control pr-5" required>
                            <button type="button" class="sa-input-icon-toggle" data-toggle-target="password" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">At least 8 characters, with uppercase, lowercase, and a number.</small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation">Confirm New Password</label>
                        <div class="sa-input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control pr-5" required>
                            <button type="button" class="sa-input-icon-toggle" data-toggle-target="password_confirmation" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block sa-auth-submit">
                        Set Password &amp; Continue
                    </button>
                </form>

                <div class="text-center mt-3">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-muted">Logout instead</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
    <script>
        document.querySelectorAll('.sa-input-icon-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.toggleTarget);
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                this.querySelector('i').classList.toggle('fa-eye', showing);
                this.querySelector('i').classList.toggle('fa-eye-slash', !showing);
            });
        });
    </script>
</body>

</html>
