<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Login</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

   <style>

* {
    font-family: "Poppins", sans-serif;
}

/* 🔥 PREMIUM DARK CORPORATE BACKGROUND */
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

/* 🔥 SOFT GLOW (subtle - admin ke liye zyada flashy nahi) */
body::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    background: rgba(79, 172, 254, 0.25);
    filter: blur(120px);
    top: 10%;
    left: 10%;
    z-index: -1;
}

/* 💎 GLASS CONTAINER */
.auth-container {
    display: flex;
    min-height: 500px;
    border-radius: 16px;
    overflow: hidden;

    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    border: 1px solid rgba(255,255,255,0.15);

    box-shadow: 
        0 10px 40px rgba(0,0,0,0.5),
        0 0 30px rgba(79,172,254,0.15);
}

/* LEFT PANEL */
.left-panel {
    padding: 3rem;
    color: #fff;
}

/* TITLE */
.left-panel h2 {
    color: #4facfe;
}

/* INPUT GROUP */
.input-group-text {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
}

/* INPUT */
.form-control {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.6);
}

.form-control:focus {
    background: rgba(255,255,255,0.12);
    border-color: #4facfe;
    box-shadow: 0 0 10px rgba(79,172,254,0.4);
}

/* 🔥 PREMIUM BUTTON */
.btn-primary {
    background: linear-gradient(135deg, #4facfe, #00c6ff);
    border: none;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(79,172,254,0.5);
}

/* TEXT */
label, a {
    color: rgba(255,255,255,0.85);
}

.text-muted {
    color: rgba(255,255,255,0.6) !important;
}

/* RIGHT IMAGE */
.auth-image {
    background-size: cover;
    background-position: center;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .auth-container {
        flex-direction: column;
    }

    .auth-image {
        height: 250px;
    }
}

</style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- MAIN CARD (Flex + Equal Height) -->
                <div class="auth-container">

                    <!-- LEFT: LOGIN FORM -->
                    <div class="col-lg-6 left-panel">

                        <h2 class="fw-bold text-primary mb-2">
                            <i class="fas fa-user-shield fa-2x me-2"></i> Admin Login
                        </h2>
                        <p class="text-muted mb-4">Welcome back! Please sign in to continue.</p>

                        <form action="{{ route('admin.login') }}" method="POST">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger py-2">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <!-- EMAIL -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter email"
                                        required>
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text "><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Enter password" required>
                                </div>
                            </div>

                            <!-- REMEMBER -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                    <label for="remember" class="form-check-label">Remember Me</label>
                                </div>
                                <a href="#" class="text-primary small text-decoration-none">Forgot Password?</a>
                            </div>

                            <!-- LOGIN BUTTON -->
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>

                        </form>

                        <!-- REGISTER -->
                        {{-- <p class="text-center small mt-3">
                        Don't have an account?
                        <a href="{{ route('admin.register.view') }}" class="text-primary fw-semibold">
                            Register now
                        </a>
                    </p> --}}
                    </div>

                    <!-- RIGHT: IMAGE (Equal Height) -->
                    <div class="col-lg-6 p-0">
                        <img src="{{ asset('admins/dist/img/login_image.jpg') }}" class="position-absolute w-50 h-100">
                    </div>


                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
