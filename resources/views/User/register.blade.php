<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal – Register</title>
    <meta name="description" content="Create a free Job Hub account to search jobs, apply online, and track your applications.">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
</head>
<body>

<div class="auth-card">

    <!-- LEFT PANEL -->
    <div class="auth-left">
        <div class="brand-icon"><i class="fas fa-briefcase"></i></div>
        <h4>Join JobHub</h4>
        <p>Connect with top companies and find your dream job in minutes.</p>
        <ul class="feature-list">
            <li><i class="fas fa-check-circle"></i> {{ number_format($liveJobsCount) }}+ live job listings</li>
            <li><i class="fas fa-check-circle"></i> {{ number_format($verifiedCompaniesCount) }}+ verified companies</li>
            <li><i class="fas fa-check-circle"></i> Free for job seekers</li>
            <li><i class="fas fa-check-circle"></i> Quick & easy apply</li>
        </ul>
    </div>

    <!-- RIGHT FORM -->
    <div class="auth-right">

        <h3><i class="fas fa-user-plus mr-2 u-fs-1-333rem"></i>Create Account</h3>
        <p class="subtitle">Fill in your details to get started</p>

        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3 u-radius-10px">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.register') }}" method="POST">
            <input type="hidden" name="ref" id="referral_ref_field" value="{{ request('ref') }}">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="name">Full Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Your full name">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="email">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="address">Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="form-control" placeholder="Your city / address">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="phone">Phone Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="+91 XXXXX XXXXX">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="pw1">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" id="pw1"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min 8 characters">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePw('pw1','i1')">
                                <i class="fas fa-eye" id="i1"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="field-label" for="pw2">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password_confirmation" id="pw2"
                            class="form-control" placeholder="Re-enter password">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePw('pw2','i2')">
                                <i class="fas fa-eye" id="i2"></i>
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="check-group mb-4">
                <input type="checkbox" id="agreeTerms" name="agree_terms" value="1"
                    class="@error('agree_terms') is-invalid @enderror" required
                    {{ old('agree_terms') ? 'checked' : '' }}>
                <label for="agreeTerms">
                    I agree to the <a href="{{ route('terms.conditions') }}" target="_blank" class="link-accent">Terms & Conditions</a>
                </label>
                @error('agree_terms')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-auth">Create Account</button>

        </form>

        <p class="text-center mt-3 mb-0 small">
            Already have an account?
            <a href="{{ route('user.login') }}" class="link-accent">Sign In</a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePw(id, iconId) {
        var input = document.getElementById(id);
        var icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye','fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash','fa-eye');
        }
    }
</script>
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>
</html>