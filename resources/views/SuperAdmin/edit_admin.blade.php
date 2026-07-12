@extends('layouts.superadmin')
@section('title', 'Edit Admin')

@section('content')

<div class="container-fluid">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-user-edit',
        'title' => 'Edit Admin',
        'subtitle' => $admin->company_name,
    ])

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
            <form action="{{ route('superadmin.admin.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control"
                               value="{{ old('company_name', $admin->company_name) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control"
                               value="{{ old('contact_number', $admin->contact_number) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $admin->email) }}" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control"
                           value="{{ old('location', $admin->location) }}">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control" maxlength="1080">{{ old('description', $admin->description) }}</textarea>
                </div>

                <hr>

                <p class="text-muted small">Leave password fields blank to keep the current password unchanged.</p>

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

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('superadmin.admins') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>

</div>

@endsection
