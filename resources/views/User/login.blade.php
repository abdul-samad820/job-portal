<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ── BASE ── */
        * { box-sizing: border-box; font-family: "Poppins", sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Glow blobs */
        body::before {
            content: "";
            position: fixed;
            width: 380px; height: 380px;
            background: rgba(118, 75, 162, 0.45);
            filter: blur(130px);
            top: -60px; right: -60px;
            z-index: 0; pointer-events: none;
        }
        body::after {
            content: "";
            position: fixed;
            width: 300px; height: 300px;
            background: rgba(102, 126, 234, 0.4);
            filter: blur(120px);
            bottom: -40px; left: -40px;
            z-index: 0; pointer-events: none;
        }

        /* ── GLASS CARD ── */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 860px;
            display: flex;
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 20px 60px rgba(0,0,0,0.30);
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            width: 45%;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
        }
        .auth-left img {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            padding: 48px 40px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-right h3 {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 6px;
            color: #fff;
        }
        .auth-right .subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.70);
            margin-bottom: 28px;
        }

        /* ── LABELS ── */
        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.80);
            margin-bottom: 6px;
        }

        /* ── INPUT GROUP ── */
        .input-group-text {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
        }
        .input-group > .input-group-prepend > .input-group-text { border-radius: 10px 0 0 10px; }
        .input-group > .form-control:last-child                  { border-radius: 0 10px 10px 0; }
        .input-group > .input-group-append > .input-group-text   { border-radius: 0 10px 10px 0; cursor: pointer; }
        .input-group-append .input-group-text:hover              { background: rgba(255,255,255,0.25); }

        /* ── INPUTS ── */
        .form-control {
            height: 44px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-size: 14px;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.55); }
        .form-control:focus {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.6);
            box-shadow: 0 0 0 3px rgba(255,255,255,0.12);
            color: #fff;
        }

        /* ── BUTTON ── */
        .btn-auth {
            width: 100%;
            height: 46px;
            background: linear-gradient(135deg, #ff7a18, #ffb347);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.03em;
            transition: 0.25s;
            cursor: pointer;
        }
        .btn-auth:hover {
            background: linear-gradient(135deg, #ff5f00, #ff9500);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255,122,24,0.45);
            color: #fff;
        }
        .btn-auth:active { transform: scale(.98); }

        /* ── SOCIAL BUTTONS ── */
        .btn-social {
            flex: 1;
            height: 42px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
            cursor: pointer;
        }
        .btn-social:hover { background: rgba(255,255,255,0.20); color: #fff; }

        /* ── MISC ── */
        .divider {
            text-align: center;
            color: rgba(255,255,255,0.50);
            font-size: 13px;
            margin: 18px 0;
        }
        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }
        .check-group { display: flex; align-items: center; gap: 7px; }
        .check-group input[type="checkbox"] { width: 15px; height: 15px; accent-color: #ffb347; cursor: pointer; }
        .check-group label { margin: 0; font-size: 13px; color: rgba(255,255,255,0.85); cursor: pointer; }
        a { color: rgba(255,255,255,0.90); }
        a:hover { color: #fff; text-decoration: none; }
        .link-accent { color: #ffb347; font-weight: 600; }
        .link-accent:hover { color: #ffd280; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .auth-right { padding: 36px 28px; }
        }
        @media (max-width: 767px) {
            body { align-items: flex-start; padding: 12px; }
            .auth-card { flex-direction: column; border-radius: 16px; }
            .auth-left { width: 100%; height: 200px; position: relative; }
            .auth-left img { position: absolute; }
            .auth-right { padding: 28px 22px 32px; }
            .auth-right h3 { font-size: 20px; }
            .social-row { display: flex; gap: 10px; }
        }
        @media (max-width: 420px) {
            .auth-left { height: 170px; }
            .auth-right { padding: 22px 16px 26px; }
            .social-row { flex-direction: column; gap: 8px; }
            .btn-social { width: 100%; }
        }
    </style>
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

            @if ($errors->any())
                <div class="alert alert-danger small py-2 mb-3" style="border-radius:10px;">{{ $errors->first() }}</div>
            @endif

            <label class="field-label">Email Address</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="you@example.com" autocomplete="email">
            </div>

            <label class="field-label">Password</label>
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

        <div class="social-row d-flex" style="gap:10px;">
            <button class="btn-social"><i class="fab fa-google mr-1"></i> Google</button>
            <button class="btn-social"><i class="fab fa-facebook mr-1"></i> Facebook</button>
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
</body>
</html>