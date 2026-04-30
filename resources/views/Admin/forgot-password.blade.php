<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: "Poppins", sans-serif; }
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: "";
            position: absolute;
            width: 350px; height: 350px;
            background: rgba(79, 172, 254, 0.2);
            filter: blur(120px);
            top: 10%; left: 10%;
            z-index: -1;
        }
        .auth-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 480px;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 30px rgba(79,172,254,0.1);
        }
        .left-panel {
            background: linear-gradient(135deg, #1e293b, #0f2027);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: #fff;
        }
        .icon-circle {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(79,172,254,0.15);
            border: 1px solid rgba(79,172,254,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            color: #4facfe;
            margin-bottom: 1.5rem;
        }
        .right-panel { padding: 3rem; color: #fff; }
        .form-label { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 14px; }
        .input-group-text {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
        }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.4); }
        .form-control:focus {
            background: rgba(255,255,255,0.12);
            border-color: #4facfe;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(79,172,254,0.2);
        }
        .btn-submit {
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 12px;
            color: #fff;
            transition: 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,172,254,0.4);
            color: #fff;
        }
        .back-link { color: rgba(255,255,255,0.6); font-size: 14px; text-decoration: none; }
        .back-link:hover { color: #4facfe; }
        .divider { border-color: rgba(255,255,255,0.1); }
        @media(max-width:768px) {
            .auth-container { flex-direction: column; margin: 1rem; }
            .left-panel { padding: 2rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="auth-container">

                {{-- LEFT PANEL --}}
                <div class="col-lg-5 left-panel d-none d-lg-flex">
                    <div class="text-center">
                        <div class="icon-circle mx-auto">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Account Recovery</h5>
                        <p class="small mb-0" style="opacity:0.6; line-height:1.7">
                            Enter your registered email address and we'll send you a secure reset link. Link expires in 15 minutes.
                        </p>
                        <hr class="divider mt-4 pt-2">
                        <span class="small" style="opacity:0.4; letter-spacing:1px">
                            ADMIN PORTAL
                        </span>
                    </div>
                </div>

                {{-- RIGHT PANEL --}}
                <div class="col-lg-7 right-panel">

                    {{-- Back to login --}}
                    <a href="{{ route('admin.login') }}" class="back-link d-inline-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Login
                    </a>

                    <h4 class="fw-bold mb-1">Forgot your password?</h4>
                    <p class="mb-4" style="color:rgba(255,255,255,0.5); font-size:14px">
                        No worries — we'll send reset instructions to your email.
                    </p>

                    {{-- Success alert --}}
                    @if(session('success'))
                        <div class="alert d-flex align-items-center gap-2 mb-4"
                             style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#86efac; border-radius:10px;">
                            <i class="fas fa-circle-check"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope fa-sm"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="admin@company.com"
                                    value="{{ old('email') }}"
                                    autofocus
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit w-100">
                            <i class="fas fa-paper-plane me-2"></i>
                            Send Reset Instructions
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>