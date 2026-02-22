<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Register</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Font: INTER -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: "Inter", sans-serif;
        }

        body {
            background: #f5f7fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .auth-box {
            max-width: 760px;
            /* Smaller box */
            width: 100%;
            background: #fff;
            border-radius: 1.2rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
            display: flex;
            min-height: 420px;
            /* Reduced height */
        }

        /* LEFT PANEL */
        .left-panel {
            background: linear-gradient(135deg, #0b4ccf, #1eb8ff);
            color: #fff;
            padding: 2.2rem 1.8rem;
            /* Reduced padding */
            flex: 0.9;
            /* Slightly smaller left */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-panel img {
            width: 90%;
            max-height: 260px;
            /* Smaller image */
            object-fit: contain;
        }

        /* RIGHT PANEL */
        .right-panel {

            padding: 2rem 1.6rem;
            /* Reduced padding */
            /* Slightly larger right side */
            background: #fff;
            flex: 1.1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel h3 {
            font-size: 24px;
            font-weight: 700;
        }

        .right-panel p {
            font-size: 14px;
        }

        .input-group-text {
            background: #eef2f7;
            border: none;
            color: #334155;
        }

        .input-group {
            background: #eef2f7;
            border-radius: .7rem;
            overflow: hidden;
        }

        .input-group .form-control {
            background: #eef2f7;
            border: none;
            padding: 0.85rem;
            font-size: 11px;
        }

        .btn-primary {
            padding: 0.85rem;
            font-weight: 600;
            border-radius: .7rem;
            background: #0b5ed7;
            border: none;
            font-size: 15px;
        }

        .btn-primary:hover {
            background: #094db3;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Responsive */
        @media(max-width: 992px) {
            .auth-box {
                flex-direction: column;
            }

            .left-panel,
            .right-panel {
                padding: 2rem !important;
                text-align: center;
            }
        }
    </style>

</head>

<body>

    <div class="auth-box">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <img src="{{ asset('admins/dist/img/shineLite_img.png') }}" alt="Illustration">
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            <h3>Admin Registration</h3>
            <p class="text-muted mb-4">Fill in the details to create your admin account</p>

            <form action="{{ route('superadmin.create') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Company Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text" name="company_name" value="{{ old('company_name') }}"
                                class="form-control @error('company_name') is-invalid @enderror"
                                placeholder="Company Name">
                        </div>
                        @error('company_name')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="contact_number" class="form-control"
                                value="{{ old('contact_number') }}"placeholder="Contact Number">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                        <input type="text" name="location" class="form-control"
                            value="{{ old('location') }}"placeholder="Location">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror"
                            placeholder="Short description">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Retype Password">
                        </div>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="agreeTerms">
                    <label for="agreeTerms">I agree to the <a href="#" class="text-primary">terms &
                            conditions</a></label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>



            {{-- <p class="text-center small mt-3">
            Already have an account?
            <a href="{{ route('admin.login.view') }}" class="text-primary fw-semibold">Login Here</a>
        </p> --}}

        </div>

    </div>

</body>

</html>
