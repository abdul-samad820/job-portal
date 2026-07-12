<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Verify Email</title>
    <meta name="description" content="Verify your email address to activate your Job Hub account.">

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

        /* ── GLASS CARD (centered, single column) ── */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.22);
            box-shadow: 0 20px 60px rgba(0,0,0,0.30);
            padding: 52px 40px;
            text-align: center;
            color: #fff;
        }

        /* ── ICON ── */
        .icon-circle {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            margin: 0 auto 22px;
        }

        .auth-card h3 { font-weight: 700; font-size: 22px; color: #fff; margin-bottom: 8px; }
        .auth-card p  { font-size: 14px; color: rgba(255,255,255,0.72); line-height: 1.75; margin-bottom: 0; }

        /* ── EMAIL BOX ── */
        .email-box {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.20);
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin: 20px 0 28px;
            word-break: break-all;
        }

        /* ── BUTTONS ── */
        .btn-auth {
            display: block;
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
            margin-bottom: 12px;
        }
        .btn-auth:hover {
            background: linear-gradient(135deg, #ff5f00, #ff9500);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255,122,24,0.45);
            color: #fff;
        }

        .btn-logout {
            display: inline-block;
            padding: 8px 22px;
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 8px;
            background: transparent;
            color: rgba(255,255,255,0.80);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-color: rgba(255,255,255,0.6);
        }

        /* ── ALERT ── */
        .alert-success-glass {
            background: rgba(16,185,129,0.20);
            border: 1px solid rgba(16,185,129,0.35);
            border-radius: 10px;
            padding: 10px 16px;
            color: #fff;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .divider-line {
            border-top: 1px solid rgba(255,255,255,0.12);
            margin: 24px 0;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 520px) {
            .auth-card { padding: 36px 22px; border-radius: 16px; }
            .icon-circle { width: 72px; height: 72px; font-size: 28px; }
            .auth-card h3 { font-size: 20px; }
        }
        @media (max-width: 380px) {
            .auth-card { padding: 28px 16px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
</head>
<body>

<div class="auth-card">

    <!-- ICON -->
    <div class="icon-circle">
        <i class="fas fa-envelope-open-text"></i>
    </div>

    <!-- TITLE -->
    <h3>Verify Your Email</h3>
    <p>
        A verification link has been sent to your email.<br>
        Please check your inbox and click the link to activate your account.
    </p>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="alert-success-glass mt-3">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- INFO (e.g. "Account created! Please verify your email to continue.") -->
    @if(session('info'))
        <div class="alert-success-glass mt-3">
            <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
        </div>
    @endif

    <!-- EMAIL DISPLAY -->
    <div class="email-box">
        <i class="fas fa-envelope mr-2 u-op-0-7"></i>
        {{ auth('user')->user()->email }}
    </div>

    <!-- RESEND BUTTON -->
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-auth">
            <i class="fas fa-sync-alt mr-2"></i>Resend Verification Email
        </button>
    </form>

    <div class="divider-line"></div>

    <!-- LOGOUT -->
    <form method="POST" action="{{ route('user.logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>
</html>