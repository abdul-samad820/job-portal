@extends('layouts.Admin_layout')
@section('title', 'Company Profile')

@section('content')
<div class="container mt-4">

    <!-- ================= PROFILE SECTION ================= -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center">

            <div class="position-relative d-inline-block">
                <img id="previewImage" src="{{ $profile->profile_image
                            ? asset('storage/admins/' . $profile->profile_image)
                            : asset('admins/dist/img/default.png') }}" class="rounded-circle shadow u-w-130px-h-130px-fit-cover-border-4px-solid-white"
                    alt="Profile preview">
            </div>

            <h4 class="mt-3 font-weight-bold">
                {{ $profile->company_name }}
            </h4>

            <p class="text-muted mb-1">
                {{ $profile->email }}
            </p>

            <p class="text-muted">
                {{ $profile->location ?? 'Location not added' }}
            </p>

            @if ($profile->slug)
            <a href="{{ route('company.show', $profile->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                <i class="fas fa-external-link-alt mr-1"></i> View Public Profile
            </a>
            @endif

            <hr>

            <div class="row text-center">
                <div class="col-md-6 border-right">
                    <h5 class="text-primary font-weight-bold">
                        {{ $totalJobs ?? 0 }}
                    </h5>
                    <small class="text-muted">Jobs Posted</small>
                </div>

                <div class="col-md-6">
                    <h5 class="text-primary font-weight-bold">
                        {{ $totalApplications ?? 0 }}
                    </h5>
                    <small class="text-muted">Total Applicants</small>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= ABOUT COMPANY ================= -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white font-weight-bold">
            About Company
        </div>
        <div class="card-body">

            <p>
                <strong>Description:</strong><br>
                <span class="text-muted">
                    {{ $profile->description ?? 'Not added yet.' }}
                </span>
            </p>

            <hr>

            <p>
                <strong>Specialization:</strong>
            </p>

            @if ($profile->expertise)
            @foreach (explode('.', $profile->expertise) as $exp)
            @if (trim($exp))
            <span class="badge badge-primary mr-2 mb-2 p-2">
                {{ trim($exp) }}
            </span>
            @endif
            @endforeach
            @else
            <p class="text-muted">Not added</p>
            @endif

        </div>
    </div>

    <!-- ================= PROFILE SETTINGS ================= -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white font-weight-bold">
            Profile Settings
        </div>

        <div class="card-body">

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- IMAGE UPLOAD -->
                <div class="form-group text-center">
                    <label class="font-weight-bold d-block" for="profile_image">Update Company Image</label>
                    <input id="profile_image" type="file" name="profile_image" class="form-control-file" onchange="readURL(this);">
                    <small class="text-muted">Allowed: JPG, PNG | Max 2MB</small>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input id="company_name" type="text" name="company_name" class="form-control"
                                value="{{ $profile->company_name }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ $profile->email }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input id="location" type="text" name="location" class="form-control" value="{{ $profile->location }}">
                </div>

                <div class="form-group">
                    <label for="expertise">Specialization</label>
                    <textarea id="expertise" name="expertise" class="form-control" rows="3">{{ $profile->expertise }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    Update Profile
                </button>

            </form>

        </div>
    </div>

    {{-- Your Data & Privacy --}}
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="font-weight-bold text-dark mb-3"><i class="fas fa-shield-alt mr-2"></i>Your Data &amp; Privacy</h4>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-1"><i class="fas fa-file-download text-primary mr-2"></i>Export Company Data</h5>
                    <p class="text-muted flex-grow-1">
                        Download a JSON file with your company profile, job postings, categories, and applications received.
                    </p>
                    <a href="{{ route('admin.data_export') }}" class="btn btn-outline-primary align-self-start">
                        Download My Data
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-danger h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-1 text-danger"><i class="fas fa-user-times mr-2"></i>Delete Company Account</h5>
                    <p class="text-muted flex-grow-1">
                        Permanently deletes your company account, all job postings, and related applications. This cannot be undone.
                    </p>
                    <button type="button" class="btn btn-outline-danger align-self-start" data-toggle="modal" data-target="#deleteAdminAccountModal">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAdminAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdminAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.account.delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteAdminAccountModalLabel">Delete your company account?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        This will permanently delete your company profile, every job you've posted, and all
                        applications, invites, and follows tied to them. This action <strong>cannot be undone</strong>.
                    </p>
                    <div class="form-group">
                        <label for="admin_delete_password">Enter your password to confirm</label>
                        <input type="password" name="delete_password" id="admin_delete_password" class="form-control @error('delete_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('delete_password')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('This is permanent. Are you absolutely sure you want to delete your company account?');">
                        Yes, Delete My Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($errors->has('delete_password'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#deleteAdminAccountModal').modal('show');
    });
</script>
@endif
@endsection


@push('scripts')
<script>
    function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
@endpush