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
    font-family: "Poppins", sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    /* 🔥 IMAGE MATCHED GRADIENT */
    background: linear-gradient(135deg, 
        #1e3c72 0%, 
        #2a5298 25%, 
        #6a11cb 50%, 
        #ff6a00 75%, 
        #ee0979 100%
    );

    position: relative;
    overflow: hidden;
}

/* 🔥 GLOW EFFECT (ULTRA PREMIUM) */
body::before {
    content: "";
    position: absolute;
    width: 400px;
    height: 400px;
    background: rgba(255, 106, 0, 0.3);
    filter: blur(150px);
    top: 10%;
    left: 10%;
    z-index: -1;
}

body::after {
    content: "";
    position: absolute;
    width: 400px;
    height: 400px;
    background: rgba(106, 17, 203, 0.3);
    filter: blur(150px);
    bottom: 10%;
    right: 10%;
    z-index: -1;
}

/* 💎 GLASS CONTAINER */
.login-container {
    max-width: 900px;
    width: 100%;
    display: flex;
    border-radius: 20px;
    overflow: hidden;

    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);

    border: 1px solid rgba(255,255,255,0.2);

    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.4),
        0 0 60px rgba(255,106,0,0.2); /* 🔥 glow */
}

/* LEFT IMAGE */
.left-side {
    width: 50%;
}

.left-side img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* RIGHT FORM */
.right-side {
    width: 50%;
    padding: 45px;
    color: #fff;
}

/* TITLE */
.right-side h3 {
    font-weight: 600;
    margin-bottom: 20px;
}

/* INPUT ICON */
.input-group-text {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
}

/* INPUT */
.form-control {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    border-radius: 0 10px 10px 0;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.7);
}

.form-control:focus {
    background: rgba(255,255,255,0.18);
    border-color: #fff;
    box-shadow: 0 0 10px rgba(255,255,255,0.2);
}

/* 🔥 PREMIUM BUTTON */
.btn-custom {
    background: linear-gradient(135deg, #ff6a00, #ffb347);
    border: none;
    color: #fff;
    border-radius: 10px;
    height: 44px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-custom:hover {
    background: linear-gradient(135deg, #ff512f, #dd2476);
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(255,106,0,0.6);
}

/* SOCIAL BUTTONS */
.social-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    transition: 0.3s;
}

.social-btn:hover {
    background: rgba(255,255,255,0.2);
}

/* TEXT */
a, label {
    color: rgba(255,255,255,0.9);
}

/* DIVIDER */
.divider {
    text-align: center;
    margin: 20px 0;
    color: rgba(255,255,255,0.6);
}

/* MOBILE */
@media(max-width: 768px) {
    .login-container {
        flex-direction: column;
    }

    .left-side,
    .right-side {
        width: 100%;
    }

    .left-side {
        height: 250px;
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
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email">
                </div>

                <label>Password</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('email') is-invalid @enderror" placeholder="Enter password">
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
