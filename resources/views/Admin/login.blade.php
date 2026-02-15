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
            font-family: "Inter", sans-serif;
            font-size: 13px;
        }

        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-container {
            display: flex;
            height: 100%;
            min-height: 500px;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .left-panel {
            padding: 3rem;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* vertically center form */
        }
        /* Right Image */
        .auth-image {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            width: 100%;
        }

        .form-control {
            padding: 0.8rem;
            border-radius: .6rem;
        }

        .btn-primary {
            padding: 0.8rem;
            font-weight: 600;
            border-radius: .6rem;
        }

        /* Responsive fix */
        @media (max-width: 991px) {
            .auth-container {
                flex-direction: column;
                min-height: auto;
            }

            .auth-image {
                height: 260px;
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
                                    <span class="input-group-text bg-white"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter email"
                                        required>
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-lock"></i></span>
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
                        <div class="auth-image"
                            style="background-image: url('https://images.unsplash.com/photo-1521791136064-7986c2920216');">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
