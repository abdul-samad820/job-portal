<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Login</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 4 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; font-family: "Poppins", sans-serif; }

        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Soft glow */
        body::before {
            content: "";
            position: fixed;
            width: 350px; height: 350px;
            background: rgba(79, 172, 254, 0.25);
            filter: blur(120px);
            top: 10%; left: 10%;
            z-index: 0;
            pointer-events: none;
        }

        /* Glass container — flex row by default */
        .auth-container {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 500px;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.5),
                0 0 30px rgba(79, 172, 254, 0.15);
        }

        /* Left form panel */
        .left-panel {
            width: 50%;
            flex-shrink: 0;
            padding: 3rem;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-panel h2 {
            color: #4facfe;
            font-weight: 700;
            margin-bottom: 6px;
        }

        /* Right image panel */
        .right-panel {
            width: 50%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .right-panel img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Input group */
        .input-group-text {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* Bootstrap 4 input-group border radius fix */
        .input-group > .input-group-prepend > .input-group-text {
            border-radius: 10px 0 0 10px;
        }
        .input-group > .form-control:last-child {
            border-radius: 0 10px 10px 0;
        }
        .input-group > .input-group-append > .input-group-text {
            border-radius: 0 10px 10px 0;
            cursor: pointer;
        }

        /* Input */
        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .form-control::placeholder { color: rgba(255, 255, 255, 0.6); }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #4facfe;
            box-shadow: 0 0 10px rgba(79, 172, 254, 0.4);
            color: #fff;
        }

        /* Password toggle */
        .input-group-append .input-group-text:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: #fff;
            width: 100%;
            height: 44px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(79, 172, 254, 0.5);
            color: #fff;
        }

        /* Text & links */
        label { color: rgba(255, 255, 255, 0.85); }
        a { color: #4facfe; }
        a:hover { color: #00c6ff; text-decoration: none; }

        .text-muted { color: rgba(255, 255, 255, 0.6) !important; }

        /* Remember + forgot row */
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        
        .form-check {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-left: 0;
}

.form-check-input {
    width: 16px;
    height: 16px;
    margin-top: 0;        
    cursor: pointer; 
    position: relative;
    top: 1px;            
}

.form-check-label {
    margin: 0;
    font-size: 14px;
    line-height: 1;       
    cursor: pointer;
}

        /* ── RESPONSIVE ─────────────────────────────────── */

        /* Tablet 768–900px: shrink padding */
        @media (max-width: 900px) {
            .left-panel { padding: 2.2rem 2rem; }
        }

        /* Mobile < 768px — stack vertically */
        @media (max-width: 767px) {

            body {
                align-items: flex-start;
                padding: 12px;
            }

            .auth-container {
                flex-direction: column;
                min-height: unset;
                border-radius: 14px;
            }

            /* Image becomes top banner */
            .right-panel {
                width: 100%;
                height: 200px;
                order: -1; /* image upar */
                position: relative;
            }

            .right-panel img {
                position: absolute;
            }

            /* Form full width */
            .left-panel {
                width: 100%;
                padding: 28px 22px 32px;
            }

            .left-panel h2 { font-size: 22px; }

            .meta-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Small phones < 420px */
        @media (max-width: 420px) {
            .right-panel { height: 170px; }
            .left-panel  { padding: 22px 16px 26px; }
            .left-panel h2 { font-size: 20px; }
        }
    </style>
</head>

<body>

    <div class="auth-container">

        <!-- LEFT: LOGIN FORM -->
        <div class="left-panel">

            <h2>
                <i class="fas fa-user-shield mr-2"></i>Admin Login
            </h2>
            <p class="text-muted mb-4">Welcome back! Please sign in to continue.</p>

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:13px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Email -->
                <div class="mb-3">
                    <label class="mb-1 font-weight-600" style="font-size:14px;">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter email"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="mb-1 font-weight-600" style="font-size:14px;">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="adminPw"
                            class="form-control"
                            placeholder="Enter password"
                            required
                            autocomplete="current-password"
                        >
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePw()" title="Show/hide password">
                                <i class="fas fa-eye" id="adminPwIcon"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Remember + Forgot -->
                <div class="meta-row">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label for="remember" class="form-check-label"> Remember Me</label>
                    </div>
                    <a href="{{ route('admin.password.request') }}" class="small text-decoration-none">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-login">Sign In</button>

            </form>
        </div>

        <!-- RIGHT: IMAGE -->
        <div class="right-panel">
            <img src="{{ asset('admins/dist/img/login_image.jpg') }}" alt="Admin Login">
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePw() {
            var input = document.getElementById('adminPw');
            var icon  = document.getElementById('adminPwIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>