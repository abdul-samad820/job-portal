@extends('layouts.superadmin')

@section('title', 'Create Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin-create-admin.css') }}">
@endpush

@section('content')
<div class="container-fluid">
<div class="auth-box">

    {{-- Show Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger m-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

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
                    <label for="company_name">Company Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <input type="text" id="company_name" name="company_name" class="form-control" placeholder="Company Name" value="{{ old('company_name') }}">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="contact_number">Contact Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Contact Number" value="{{ old('contact_number') }}">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="location">Location</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    </div>
                    <input type="text" id="location" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="description">Description</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    </div>
                    <textarea id="description" name="description" rows="2" class="form-control" placeholder="Short description">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Retype Password">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary btn-block">Register</button>

        </form>

    </div>

</div>
</div>
@endsection