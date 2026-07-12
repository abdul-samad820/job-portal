<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Reset Password</title>
    <meta name="description" content="Set a new password for your Job Hub account.">

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
            padding: 20px 16px;
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
            max-width: 820px;
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
            width: 40%;
            flex-shrink: 0;
            background: rgba(0,0,0,0.18);
            border-right: 1px solid rgba(255,255,255,0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            text-align: center;
            color: #fff;
        }

        .icon-circle {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin-bottom: 22px;
        }

        .auth-left h4 { font-weight: 700; font-size: 20px; color: #fff; margin-bottom: 10px; }
        .auth-left p  { font-size: 13px; color: rgba(255,255,255,0.68); line-height: 1.7; margin: 0; }

        .secured {
            margin-top: 48px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,0.12);
            width: 80%;
            font-size: 11px;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.40);
            text-transform: uppercase;
        }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            padding: 52px 44px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-right h3 { font-weight: 700; font-size: 24px; color: #fff; margin-bottom: 6px; }
        .auth-right .subtitle { font-size: 13px; color: rgba(255,255,255,0.65); margin-bottom: 28px; }

        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.80);
            margin-bottom: 6px;
        }

        .form-text-hint {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            margin-top: 5px;
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

        .form-control {
            height: 46px;
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

        .btn-auth {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #ff7a18, #ffb347);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            transition: 0.25s;
            cursor: pointer;
            margin-top: 6px;
        }
        .btn-auth:hover {
            background: linear-gradient(135deg, #ff5f00, #ff9500);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255,122,24,0.45);
            color: #fff;
        }

        a { color: rgba(255,255,255,0.90); }
        a:hover { color: #fff; text-decoration: none; }
        .link-accent { color: #ffb347; font-weight: 600; }
        .link-accent:hover { color: #ffd280; }

        /* ── RESPONSIVE ── */
        @media (max-width: 767px) {
            body { align-items: flex-start; padding: 12px; }
            .auth-card { flex-direction: column; border-radius: 16px; }
            .auth-left {
                width: 100%;
                padding: 24px 22px;
                flex-direction: row;
                gap: 16px;
                text-align: left;
            }
            .auth-left .icon-circle { margin-bottom: 0; flex-shrink: 0; width: 56px; height: 56px; font-size: 22px; }
            .secured { display: none; }
            .auth-right { padding: 28px 22px 36px; }
            .auth-right h3 { font-size: 20px; }
        }
        @media (max-width: 420px) {
            .auth-left { padding: 18px 14px; }
            .auth-right { padding: 22px 14px 28px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
</head>
<body>

<div class="auth-card">

    <!-- LEFT PANEL -->
    <div class="auth-left">
        <div class="icon-circle">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <h4>Set New Password</h4>
            <p>Choose a strong password to keep your account secure.</p>
            <div class="secured">Secured by JobHub</div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-right">

        <h3>Reset Password</h3>
        <p class="subtitle">Enter and confirm your new password below.</p>

        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3 u-radius-10px">
                {{ $errors->first() }}
                @if (str_contains($errors->first(), 'expired') || str_contains($errors->first(), 'invalid'))
                    <a href="{{ route('password.request') }}" class="d-block mt-1 font-weight-bold">Request a new reset link →</a>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <label class="field-label" for="pw1">New Password</label>
            <div class="input-group mb-1">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" name="password" id="pw1"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Min 8 characters" required>
                <div class="input-group-append">
                    <span class="input-group-text" onclick="togglePw('pw1','i1')">
                        <i class="fas fa-eye" id="i1"></i>
                    </span>
                </div>
            </div>
            <p class="form-text-hint mb-3">Must be 8+ characters with at least one uppercase letter and number.</p>

            <label class="field-label" for="pw2">Confirm Password</label>
            <div class="input-group mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" name="password_confirmation" id="pw2"
                    class="form-control" placeholder="Re-enter new password" required>
                <div class="input-group-append">
                    <span class="input-group-text" onclick="togglePw('pw2','i2')">
                        <i class="fas fa-eye" id="i2"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="fas fa-check-circle mr-2"></i>Reset Password
            </button>
        </form>

        <p class="text-center mt-4 mb-0 small">
            Remembered it?
            <a href="{{ route('user.login') }}" class="link-accent">Back to Sign In</a>
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