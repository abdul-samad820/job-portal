@extends('layouts.user_index')
@section('title', 'Add Personal Info')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex gap-3 align-items-center">
                    <div>
                        <h4 class="mb-1">Add Your Personal Details</h4>
                        <p class="mb-0 text-muted small">Enter your basic information to complete your profile.</p>
                    </div>
                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Form Start --}}
           <form action="{{ route('user.account_setting_update', $user_data->id) }}" method="POST">
                @csrf

                {{-- Name --}} 
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Full Name</strong>
                    </div>
                    <div class="card-body">
                        <input type="text" name="name" 
                            value="{{ old('name',$user_data->name)}}"
                             class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Samad Khwaja">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted mt-2">Enter your full legal name.</small>
                    </div>
                </div>

                {{-- Email --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Email Address</strong>
                    </div>
                    <div class="card-body">
                        <input type="email" name="email"
                            value="{{ old('email',$user_data->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="e.g. samad@example.com">

                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted mt-2">Use an email you actively check.</small>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Phone Number</strong>
                    </div>
                    <div class="card-body">
                        <input type="text" name="phone"
                          value="{{ old('phone', $user_data->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="e.g. +91 9876543210">

                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Add WhatsApp or primary contact number.
                        </small>
                    </div>
                </div>

                {{-- Address --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Address</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="address" rows="3"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="House No, Street, City, State, Country">{{ old('address',$user_data->address) }}</textarea>

                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted mt-2">
                            Provide your complete residential address.
                        </small>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="d-flex justify-content-end gap-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Details</button>
                </div>

            </form>
            {{-- Form End --}}

        </div>
    </div>
</div>

@endsection
