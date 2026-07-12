@extends('layouts.superadmin')
@section('title', 'My Profile')

@section('content')

<div class="container-fluid mt-3">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-user-circle',
        'title' => 'My Profile',
        'subtitle' => 'Manage your SuperAdmin email and password.',
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('superadmin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $superadmin->email) }}" required>
                </div>

                <hr>

                <p class="text-muted small">Leave the new password fields blank if you only want to change your email.</p>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="font-weight-bold">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" class="form-control" required>
                    <small class="text-muted">Required to confirm any change on this page.</small>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

</div>

@endsection
