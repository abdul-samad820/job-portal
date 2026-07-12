<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Login</title>
    <meta name="description" content="Log in to your Job Hub account to search jobs, apply online, and manage your applications.">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/login-user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
</head>
<body>

<div class="auth-card">

    <!-- LEFT IMAGE -->
    <div class="auth-left">
        <img src="{{ asset('admins/dist/img/ai-generated.jpg') }}" alt="">
    </div>

    <!-- RIGHT FORM -->
    <div class="auth-right">

        <h3>Welcome Back</h3>
        <p class="subtitle">Sign in to your JobHub account</p>

        <form action="{{ route('user.login.submit') }}" method="POST">
            @csrf

            @if (session('error'))
                <div class="alert alert-danger small py-2 mb-3 u-radius-10px">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger small py-2 mb-3 u-radius-10px">{{ $errors->first() }}</div>
            @endif

            <label class="field-label" for="email">Email Address</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="you@example.com" autocomplete="email">
            </div>

            <label class="field-label" for="pw1">Password</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" name="password" id="pw1"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter password" autocomplete="current-password">
                <div class="input-group-append">
                    <span class="input-group-text" onclick="togglePw('pw1','icon1')">
                        <i class="fas fa-eye" id="icon1"></i>
                    </span>
                </div>
            </div>

            <div class="remember-row">
                <div class="check-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="small link-accent">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="divider">— OR —</div>

        <div class="social-row d-flex u-gap-10px">
            <button type="button" class="btn-social" disabled title="Coming soon"><i class="fab fa-google mr-1"></i> Google</button>
            <button type="button" class="btn-social" disabled title="Coming soon"><i class="fab fa-facebook mr-1"></i> Facebook</button>
        </div>

        <p class="text-center mt-3 mb-0 small">
            Don't have an account?
            <a href="{{ route('user.register.view') }}" class="link-accent">Register Now</a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePw(id, iconId) {
        var input = document.getElementById(id);
        var icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye','fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash','fa-eye');
        }
    }
</script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>
</html>