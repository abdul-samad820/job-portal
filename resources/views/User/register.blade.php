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
            font-family: "Inter", sans-serif;
            font-size: 13px;
        }

        body {
            background: #eef2f7;
            font-family: 'Poppins', sans-serif;
            padding: 40px 15px;
        }

        .container-box {
            max-width: 1100px;
            background: #fff;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            margin: auto;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.12);
        }

        /* LEFT IMAGE SIDE */
        .left-image {
            flex: 1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .left-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 550px;
        }

        /* RIGHT FORM SIDE */
        .right-form {
            flex: 1;
            padding: 50px;
        }

        .form-title {
            color: #3b82f6;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .input-group-text {
            background: #eef3f8;
            border: 1px solid #dde2e7;
            border-radius: 10px 0 0 10px;
            min-width: 20px;

        }

        .form-control {
            height: 48px;
            background: #f8f9fb;
            border: 1px solid #dde2e7;
            border-radius: 0 10px 10px 0;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .btn-register {
            background: #3b82f6;
            border-radius: 10px;
            height: 50px;
            width: 100%;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }

        .btn-register:hover {
            background: #275bb0;
        }

        @media (max-width: 900px) {
            .container-box {
                flex-direction: column;
            }

            .left-image {
                padding: 15px;
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
