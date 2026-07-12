<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin | Login</title>
    <meta name="description" content="Super Admin login for Job Hub platform administration.">

    <!-- Bootstrap 4 comes bundled inside AdminLTE's own stylesheet in this project -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin-pro.css') }}">
    <style>
        html, body { margin: 0; padding: 0; min-height: 100%; overflow-x: hidden; }
        *, *::before, *::after { box-sizing: border-box; }
    </style>
</head>

<body>

    <div class="sa-auth">
        <div class="sa-auth-card">

            <!-- LEFT: LOGIN FORM -->
            <div class="sa-auth-form-side">

                <div class="sa-auth-icon-badge">
                    <i class="fas fa-user-shield"></i>
                </div>

                <h2 class="font-weight-bold mb-1" style="font-family:'Montserrat',sans-serif;">Super Admin Login</h2>
                <p class="text-muted mb-4">Welcome back! Please sign in to continue.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('superadmin.login') }}" method="POST">
                    @csrf

                    <!-- EMAIL -->
                    <div class="mb-3">
                        <label for="email">Email Address</label>
                        <div class="sa-input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Enter email">
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-4">
                        <label for="password">Password</label>
                        <div class="sa-input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control pr-5"
                                placeholder="Enter password" required>
                            <button type="button" class="sa-input-icon-toggle" id="togglePassword" aria-label="Show password" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- REMEMBER -->
                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label for="remember" class="form-check-label small">Remember Me</label>
                    </div>

                    <!-- LOGIN BUTTON -->
                    <button type="submit" class="btn btn-primary btn-block sa-auth-submit">
                        Sign In <i class="fas fa-arrow-right ml-2"></i>
                    </button>

                </form>
            </div>

            <!-- RIGHT: VISUAL SIDE -->
            <div class="sa-auth-visual-side">
                <div class="sa-auth-brand">
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" alt="Job Hub" style="width:28px;height:28px;">
                    Job Hub
                </div>
                <div>
                    <div class="sa-auth-visual-title">Full control of the platform, in one clean panel.</div>
                    <p class="sa-auth-visual-copy">Manage companies, moderate jobs, review reports, and keep Job Hub running smoothly — all from a single dashboard built for the people who run it.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 4 JS -->
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
    <script>
        (function () {
            var toggle = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            if (!toggle || !input) return;
            toggle.addEventListener('click', function () {
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                this.querySelector('i').classList.toggle('fa-eye', showing);
                this.querySelector('i').classList.toggle('fa-eye-slash', !showing);
                this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        })();
    </script>
</body>

</html>