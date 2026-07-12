@extends('layouts.User_layout')
@section('title', 'Add Personal Info')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Header Card --}}
            <div class="sa-page-header">
                <div class="d-flex align-items-center">
                    <div class="sa-page-icon mr-3"><i class="fas fa-key"></i></div>
                    <div>
                        <h1 class="sa-page-title font-weight-bold text-dark mb-0">Add Your Personal Details</h1>
                        <small class="text-muted">Enter your basic information to complete your profile.</small>
                    </div>
                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            {{-- Form Start --}}
                <form action="{{ route('user.account_setting_update') }}" method="POST">
                @csrf

                {{-- Full Name --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Full Name</strong>
                    </div>
                    <div class="card-body">

                        <input type="text" name="name" aria-label="Full Name" value="{{ old('name', $user_data->name) }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Samad Khwaja">

                        @error('name')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Enter your full legal name.
                        </small>
                    </div>
                </div>

                {{-- Email --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Email Address</strong>
                    </div>
                    <div class="card-body">

                        <input type="email" name="email" aria-label="Email" value="{{ old('email', $user_data->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="e.g. samad@example.com">

                        @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Use an email you actively check.
                        </small>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Phone Number</strong>
                    </div>
                    <div class="card-body">

                        <input type="text" name="phone" aria-label="Phone Number" value="{{ old('phone', $user_data->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror" placeholder="e.g. +91 9876543210">

                        @error('phone')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Add WhatsApp or primary contact number.
                        </small>
                    </div>
                </div>

                {{-- Address --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Address</strong>
                    </div>
                    <div class="card-body">

                        <textarea name="address" rows="3" aria-label="Address" class="form-control @error('address') is-invalid @enderror"
                            placeholder="House No, Street, City, State, Country">{{ old('address', $user_data->address) }}</textarea>

                        @error('address')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Provide your complete residential address.
                        </small>
                    </div>
                </div>

                {{-- Change Password --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Change Password</strong>
                    </div>
                    <div class="card-body">

                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Required only if changing your password" autocomplete="current-password">
                        @error('current_password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <label class="form-label mt-3" for="password">New Password</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Leave blank to keep current password" autocomplete="new-password">
                        @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <label class="form-label mt-3" for="password_confirmation">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" autocomplete="new-password">

                        <small class="form-text text-muted mt-2">
                            Leave the password fields blank if you don't want to change it.
                        </small>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="text-right">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mr-2">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Details
                    </button>
                </div>

            </form>

            {{-- Your Data & Privacy --}}
            <div class="sa-page-header mt-4">
                <div class="d-flex align-items-center">
                    <div class="sa-page-icon mr-3"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <h1 class="sa-page-title font-weight-bold text-dark mb-0">Your Data &amp; Privacy</h1>
                        <small class="text-muted">Download a copy of everything we hold about you, or permanently delete your account.</small>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mr-3">
                        <h5 class="mb-1"><i class="fas fa-file-download text-primary mr-2"></i>Export My Data</h5>
                        <p class="text-muted mb-0">
                            Download a JSON file with your profile, applications, resumes, saved jobs, and account activity.
                        </p>
                    </div>
                    <a href="{{ route('user.data_export') }}" class="btn btn-outline-primary">
                        Download My Data
                    </a>
                </div>
            </div>

            <div class="card border-danger mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mr-3">
                        <h5 class="mb-1 text-danger"><i class="fas fa-user-times mr-2"></i>Delete My Account</h5>
                        <p class="text-muted mb-0">
                            Permanently deletes your account, applications, resumes, and saved data. This cannot be undone.
                        </p>
                    </div>
                    <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteAccountModal">
                        Delete Account
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('user.account.delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteAccountModalLabel">Delete your account?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        This will permanently delete your profile, resumes, job applications, saved jobs, and job
                        alerts. This action <strong>cannot be undone</strong>.
                    </p>
                    <div class="form-group">
                        <label for="delete_password">Enter your password to confirm</label>
                        <input type="password" name="delete_password" id="delete_password" class="form-control @error('delete_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('delete_password')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('This is permanent. Are you absolutely sure you want to delete your account?');">
                        Yes, Delete My Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($errors->has('delete_password'))
<script>
    // Re-open the modal if the password confirmation failed, so the
    // validation error above is actually visible to the user.
    document.addEventListener('DOMContentLoaded', function () {
        $('#deleteAccountModal').modal('show');
    });
</script>
@endif

@endsection