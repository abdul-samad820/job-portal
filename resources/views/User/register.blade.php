<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Register</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; font-family: "Poppins", sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            width: 380px; height: 380px;
            background: rgba(118,75,162,0.45);
            filter: blur(130px);
            top: -60px; right: -60px;
            z-index: 0; pointer-events: none;
        }
        body::after {
            content: "";
            position: fixed;
            width: 300px; height: 300px;
            background: rgba(102,126,234,0.4);
            filter: blur(120px);
            bottom: -40px; left: -40px;
            z-index: 0; pointer-events: none;
        }

        /* ── GLASS CARD ── */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            display: flex;
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.22);
            box-shadow: 0 20px 60px rgba(0,0,0,0.30);
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            width: 38%;
            flex-shrink: 0;
            background: rgba(255,255,255,0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 28px;
            border-right: 1px solid rgba(255,255,255,0.12);
            text-align: center;
            color: #fff;
        }

        .auth-left .brand-icon {
            width: 72px; height: 72px;
            border-radius: 18px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .auth-left h4 { font-weight: 700; font-size: 20px; color: #fff; margin-bottom: 10px; }
        .auth-left p  { font-size: 13px; color: rgba(255,255,255,0.70); line-height: 1.7; }

        .auth-left .feature-list {
            list-style: none;
            padding: 0; margin: 20px 0 0;
            text-align: left;
            width: 100%;
        }
        .auth-left .feature-list li {
            font-size: 13px;
            color: rgba(255,255,255,0.80);
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .auth-left .feature-list li i { color: #ffb347; font-size: 12px; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            padding: 44px 40px;
            color: #fff;
        }

        .auth-right h3 {
            font-weight: 700;
            font-size: 22px;
            color: #fff;
            margin-bottom: 4px;
        }
        .auth-right .subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 26px;
        }

        /* ── LABELS ── */
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.80);
            margin-bottom: 5px;
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
        .input-group-append .input-group-text:hover { background: rgba(255,255,255,0.25); }

        /* ── INPUTS ── */
        .form-control {
            height: 42px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-size: 14px;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.50); }
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
            transition: 0.25s;
            cursor: pointer;
        }
        .btn-auth:hover {
            background: linear-gradient(135deg, #ff5f00, #ff9500);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255,122,24,0.45);
            color: #fff;
        }

        /* ── MISC ── */
        .check-group { display: flex; align-items: flex-start; gap: 8px; }
        .check-group input[type="checkbox"] { width: 15px; height: 15px; accent-color: #ffb347; cursor: pointer; margin-top: 2px; }
        .check-group label { margin: 0; font-size: 13px; color: rgba(255,255,255,0.80); cursor: pointer; }
        a { color: rgba(255,255,255,0.90); }
        a:hover { color: #fff; text-decoration: none; }
        .link-accent { color: #ffb347; font-weight: 600; }
        .link-accent:hover { color: #ffd280; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .auth-left { display: none; }
            .auth-right { padding: 36px 28px; }
        }
        @media (max-width: 767px) {
            body { align-items: flex-start; padding: 12px; }
            .auth-card { flex-direction: column; border-radius: 16px; }
            .auth-right { padding: 28px 20px 32px; }
            .auth-right h3 { font-size: 20px; }
        }
        @media (max-width: 420px) {
            .auth-right { padding: 22px 14px 26px; }
        }
    </style>
</head>
<body>

<div class="auth-card">

    <!-- LEFT PANEL -->
    <div class="auth-left">
        <div class="brand-icon"><i class="fas fa-briefcase"></i></div>
        <h4>Join JobHub</h4>
        <p>Connect with top companies and find your dream job in minutes.</p>
        <ul class="feature-list">
            <li><i class="fas fa-check-circle"></i> 1,200+ live job listings</li>
            <li><i class="fas fa-check-circle"></i> 350+ verified companies</li>
            <li><i class="fas fa-check-circle"></i> Free for job seekers</li>
            <li><i class="fas fa-check-circle"></i> Quick & easy apply</li>
        </ul>
    </div>

    <!-- RIGHT FORM -->
    <div class="auth-right">

        <h3><i class="fas fa-user-plus mr-2" style="font-size:20px;"></i>Create Account</h3>
        <p class="subtitle">Fill in your details to get started</p>

        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3" style="border-radius:10px;">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.register') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="field-label">Full Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Your full name">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label">Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" name="address" value="{{ old('address') }}"
                            class="form-control" placeholder="Your city / address">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label">Phone Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="+91 XXXXX XXXXX">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" id="pw1"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min 8 characters">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePw('pw1','i1')">
                                <i class="fas fa-eye" id="i1"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password_confirmation" id="pw2"
                            class="form-control" placeholder="Re-enter password">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePw('pw2','i2')">
                                <i class="fas fa-eye" id="i2"></i>
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="check-group mb-4">
                <input type="checkbox" id="agreeTerms" required>
                <label for="agreeTerms">
                    I agree to the <a href="#" class="link-accent">Terms & Conditions</a>
                </label>
            </div>

            <button type="submit" class="btn-auth">Create Account</button>

        </form>

        <p class="text-center mt-3 mb-0 small">
            Already have an account?
            <a href="{{ route('user.login') }}" class="link-accent">Sign In</a>
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