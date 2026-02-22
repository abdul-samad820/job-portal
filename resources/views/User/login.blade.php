<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Login</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: #eef2f7;
            font-family: "Poppins", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            max-width: 900px;
            width: 100%;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
        }

        .left-side {
            width: 50%;
            padding: 0;
            background: none;
        }

        .left-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .right-side {
            width: 50%;
            padding: 45px 40px;
        }

        .right-side h3 {
            font-weight: 600;
            color: #003566;
            margin-bottom: 20px;
        }

        label {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .input-group .form-control {
            height: 44px;
            border-radius: 10px;
            font-size: 13px;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f1f3f5;
        }

        .btn-custom {
            background: #003566;
            color: #fff;
            border-radius: 10px;
            height: 44px;
            font-weight: 500;
            width: 100%;
        }

        .btn-custom:hover {
            background: #002a4d;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            font-size: 12px;
            color: #777;
        }

        .social-btn {
            height: 42px;
            border-radius: 10px;
            width: 100%;
            font-size: 13px;
        }

        /* RESPONSIVE */
        @media(max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .left-side,
            .right-side {
                width: 100%;
            }

            .left-side {
                height: 260px;
                /* mobile image height */
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <!-- LEFT IMAGE -->
        <div class="left-side">
            <img src="{{ asset('admins/dist/img/ai-generated.jpg') }}" alt="Login Image">
        </div>

        <!-- RIGHT FORM -->
        <div class="right-side">

            <h3>Sign In</h3>

            <form action="{{ route('user.login.submit') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger small">{{ $errors->first() }}</div>
                @endif

                <label>Email Address</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email">
                </div>

                <label>Password</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('email') is-invalid @enderror"
                        placeholder="Enter password">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="checkbox" id="remember">
                        <label for="remember" class="ms-1">Remember me</label>
                    </div>
                    <a href="#" class="small text-decoration-none">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-custom">Sign In</button>
            </form>

            <div class="divider">— OR —</div>

            <button class="btn btn-outline-primary social-btn mb-2">
                <i class="fab fa-facebook me-2"></i> Sign in with Facebook
            </button>

            <button class="btn btn-outline-danger social-btn">
                <i class="fab fa-google me-2"></i> Sign in with Google
            </button>

            <p class="text-center mt-3 small">
                Don't have an account?
                <a href="{{ route('user.register.view') }}">Register Now</a>
            </p>

        </div>

    </div>

</body>

</html>
