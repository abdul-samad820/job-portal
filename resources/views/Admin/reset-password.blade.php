<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: "Poppins", sans-serif; }
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        body::before {
            content: "";
            position: absolute;
            width: 350px; height: 350px;
            background: rgba(79,172,254,0.2);
            filter: blur(120px);
            top: 10%; left: 10%; z-index: -1;
        }
        .auth-container {
            display: flex;
            width: 100%; max-width: 900px; min-height: 520px;
            border-radius: 16px; overflow: hidden;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .left-panel {
            background: linear-gradient(135deg, #1e293b, #0f2027);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 3rem; color: #fff;
        }
        .step {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .step:last-child { border-bottom: none; }
        .step-num {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(79,172,254,0.2);
            border: 1px solid rgba(79,172,254,0.4);
            color: #4facfe;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; flex-shrink: 0;
        }
        .step-text { font-size: 13px; color: rgba(255,255,255,0.7); }
        .right-panel { padding: 3rem; color: #fff; }
        .form-label { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 14px; }
        .input-group-text {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7); cursor: pointer;
        }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.4); }
        .form-control:focus {
            background: rgba(255,255,255,0.12);
            border-color: #4facfe; color: #fff;
            box-shadow: 0 0 0 3px rgba(79,172,254,0.2);
        }
        .strength-bar {
            height: 4px; border-radius: 4px;
            background: rgba(255,255,255,0.1);
            margin-top: 8px; overflow: hidden;
        }
        .strength-fill {
            height: 100%; border-radius: 4px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }
        .strength-label { font-size: 11px; margin-top: 4px; }
        .req { font-size: 12px; color: rgba(255,255,255,0.4); display: flex; align-items: center; gap: 6px; }
        .req.met { color: #86efac; }
        .req i { font-size: 10px; }
        .btn-submit {
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            border: none; border-radius: 10px;
            font-weight: 600; padding: 12px; color: #fff; transition: 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,172,254,0.4); color: #fff;
        }
        .back-link { color: rgba(255,255,255,0.6); font-size: 14px; text-decoration: none; }
        .back-link:hover { color: #4facfe; }
        @media(max-width:768px) {
            .auth-container { flex-direction: column; margin: 1rem; }
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
                    <div class="w-100">
                        <div class="text-center mb-4">
                            <i class="fas fa-key fa-2x" style="color:#4facfe"></i>
                            <h6 class="fw-bold mt-3 mb-1">Create New Password</h6>
                            <p class="small" style="opacity:0.5">Choose a strong password to protect your account</p>
                        </div>
                        <div class="mt-3">
                            <div class="step">
                                <div class="step-num"><i class="fas fa-check fa-xs"></i></div>
                                <div class="step-text">Email verified</div>
                            </div>
                            <div class="step">
                                <div class="step-num"><i class="fas fa-check fa-xs"></i></div>
                                <div class="step-text">Reset link received</div>
                            </div>
                            <div class="step">
                                <div class="step-num" style="background:rgba(79,172,254,0.3);border-color:#4facfe;color:#4facfe">3</div>
                                <div class="step-text" style="color:#fff;font-weight:500">Set new password</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT PANEL --}}
                <div class="col-lg-7 right-panel">

                    <a href="{{ route('admin.login') }}" class="back-link d-inline-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Login
                    </a>

                    <h4 class="fw-bold mb-1">Set new password</h4>
                    <p class="mb-4" style="color:rgba(255,255,255,0.5);font-size:14px">
                        Must be at least 8 characters with 1 uppercase letter and 1 number.
                    </p>

                    @if($errors->any())
                        <div class="alert mb-4" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock fa-sm"></i></span>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter new password"
                                    oninput="checkStrength(this.value)"
                                    required>
                                <span class="input-group-text" onclick="togglePass('password', this)">
                                    <i class="fas fa-eye fa-sm"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Strength bar --}}
                            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                            <div class="strength-label" id="strengthLabel" style="color:rgba(255,255,255,0.4)"></div>
                            {{-- Requirements --}}
                            <div class="mt-2 d-flex flex-wrap gap-3">
                                <div class="req" id="req-len"><i class="fas fa-circle-xmark"></i> 8+ characters</div>
                                <div class="req" id="req-upper"><i class="fas fa-circle-xmark"></i> Uppercase</div>
                                <div class="req" id="req-num"><i class="fas fa-circle-xmark"></i> Number</div>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock fa-sm"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control"
                                    placeholder="Confirm new password"
                                    oninput="checkMatch()"
                                    required>
                                <span class="input-group-text" onclick="togglePass('password_confirmation', this)">
                                    <i class="fas fa-eye fa-sm"></i>
                                </span>
                            </div>
                            <div class="req mt-2" id="req-match"><i class="fas fa-circle-xmark"></i> Passwords match</div>
                        </div>

                        <button type="submit" class="btn btn-submit w-100">
                            <i class="fas fa-shield-halved me-2"></i>
                            Reset Password
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash fa-sm';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye fa-sm';
    }
}

function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    const len   = document.getElementById('req-len');
    const upper = document.getElementById('req-upper');
    const num   = document.getElementById('req-num');

    const hasLen   = val.length >= 8;
    const hasUpper = /[A-Z]/.test(val);
    const hasNum   = /[0-9]/.test(val);
    const hasSpec  = /[^A-Za-z0-9]/.test(val);

    toggle(len,   hasLen);
    toggle(upper, hasUpper);
    toggle(num,   hasNum);

    const score = [hasLen, hasUpper, hasNum, hasSpec].filter(Boolean).length;
    const map = {
        0: [0,   'transparent',              ''],
        1: [25,  '#ef4444',                  'Weak'],
        2: [50,  '#f97316',                  'Fair'],
        3: [75,  '#eab308',                  'Good'],
        4: [100, '#22c55e',                  'Strong'],
    };
    const [w, c, t] = map[score];
    fill.style.width      = w + '%';
    fill.style.background = c;
    label.textContent     = t;
    label.style.color     = c;
}

function toggle(el, met) {
    el.classList.toggle('met', met);
    el.querySelector('i').className = met
        ? 'fas fa-circle-check'
        : 'fas fa-circle-xmark';
}

function checkMatch() {
    const p1  = document.getElementById('password').value;
    const p2  = document.getElementById('password_confirmation').value;
    const req = document.getElementById('req-match');
    toggle(req, p1 === p2 && p2.length > 0);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>