<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User | Registration</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

   <style>
    * {
    font-family: "Poppins", sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* MAIN GLASS CONTAINER */
.container-box {
    max-width: 1000px;
    width: 100%;
    display: flex;
    border-radius: 20px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
}

/* LEFT SIDE */
.left-image {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 25px;
}

.left-image img {
    width: 100%;
    max-height: 500px;
    object-fit: contain;
}

/* RIGHT SIDE */
.right-form {
    flex: 1;
    padding: 50px;
    color: #fff;
}

/* TITLE */
.form-title {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 25px;
}

/* INPUT GROUP */
.input-group-text {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
}

/* INPUT */
.form-control {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.7);
}

.form-control:focus {
    border-color: #fff;
    box-shadow: none;
    background: rgba(255,255,255,0.15);
}

/* BUTTON */
.btn-register {
    background: rgba(255,255,255,0.2);
    border: none;
    backdrop-filter: blur(10px);
    color: #fff;
    font-weight: 600;
    height: 50px;
    border-radius: 10px;
    transition: 0.3s;
}

.btn-register:hover {
    background: rgba(255,255,255,0.35);
}

/* CHECKBOX */
.form-check-label {
    color: rgba(255,255,255,0.8);
}

/* LINKS */
a {
    color: #fff;
}

/* MOBILE */
@media (max-width: 900px) {
    .container-box {
        flex-direction: column;
    }
}
   </style>
</head>

<body>

    <div class="container-box">

        <!-- LEFT IMAGE PANEL -->
        <div class="left-image">
            <img src="{{ asset('admins/dist/img/landing_hero.png') }}" alt="Illustration">
        </div>

        <!-- RIGHT FORM PANEL -->
        <div class="right-form">

            <h2 class="form-title">
                <i class="fas fa-user-plus me-2 fs-1"></i>
                User Registration
            </h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.register') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- Column 1 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control  @error('name') is-invalid @enderror" placeholder="Enter your name">
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email"
                                value="{{ old('email') }}"class="form-control @error('email') is-invalid @enderror"
                                placeholder="Enter email">
                        </div>
                    </div>

                    <!-- Column 1 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="address" value="{{ old('address') }}" class="form-control"
                                placeholder="Enter address">
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Enter phone number">
                        </div>
                    </div>

                    <!-- Column 1 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Enter password">
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Re-enter password">
                        </div>
                    </div>

                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms">
                    <label class="form-check-label" for="agreeTerms">
                        I agree to the <a href="#" class="text-primary fw-semibold">terms & conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn-register">Register Account</button>

            </form>

            <p class="mt-4">
                Already have an account?
                <a href="{{ route('user.login') }}" class="fw-bold text-primary">Login here</a>
            </p>

        </div>

    </div>

</body>

</html>
