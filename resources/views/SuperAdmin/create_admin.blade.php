<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admin Register</title>

<!--  Bootstrap 4 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

* { font-family: "Inter", sans-serif; }

body {
    background: #f5f7fb;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 15px;
}

/* MAIN CARD */
.auth-box {
    max-width: 760px;
    width: 100%;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.07);
    display: flex;
}

/* LEFT */
.left-panel {
    background: linear-gradient(135deg, #0b4ccf, #1eb8ff);
    color: #fff;
    padding: 40px 25px;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.left-panel img {
    width: 100%;
    max-width: 260px;
}

/* RIGHT */
.right-panel {
    flex: 1.2;
    padding: 35px 25px;
}

.right-panel h3 {
    font-weight: 700;
}

.input-group {
    background: #eef2f7;
    border-radius: 8px;
}

.input-group-text {
    background: #eef2f7;
    border: none;
}

.form-control {
    border: none;
    background: #eef2f7;
    font-size: 14px;
}

.form-control:focus {
    box-shadow: none;
    background: #eef2f7;
}

/* BUTTON */
.btn-primary {
    background: #0b5ed7;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    padding: 10px;
}

.btn-primary:hover {
    background: #094db3;
}

/* MOBILE */
@media(max-width: 768px) {
    .auth-box {
        flex-direction: column;
    }

    .left-panel {
        padding: 25px;
    }
}

</style>

</head>

<body>

<div class="auth-box">

    <!-- LEFT -->
    <div class="left-panel">
        <img src="{{ asset('admins/dist/img/shineLite_img.png') }}" alt="Illustration">
    </div>

    <!-- RIGHT -->
    <div class="right-panel">

        <h3>Admin Registration</h3>
        <p class="text-muted mb-4">Fill in the details to create your admin account</p>

        <form method="POST" action="{{ route('superadmin.create') }}">
            @csrf

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label>Company Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <input type="text" name="company_name" class="form-control" placeholder="Company Name">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Contact Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="contact_number" class="form-control" placeholder="Contact Number">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Location</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    </div>
                    <input type="text" name="location" class="form-control" placeholder="Location">
                </div>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    </div>
                    <textarea name="description" rows="2" class="form-control" placeholder="Short description"></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control" placeholder="Email">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Retype Password">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary btn-block">Register</button>

        </form>

    </div>

</div>

</body>
</html>