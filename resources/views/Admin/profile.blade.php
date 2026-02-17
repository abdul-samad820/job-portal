@extends('layouts.index')
@section('title', 'Profile')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-md-4">

            <!-- PROFILE CARD -->
            <div class="card shadow-sm mb-4 text-center">
                <div class="card-body">

                    <img id="previewImage"
                        src="{{ $profile->profile_image 
                                ? asset('storage/admins/'.$profile->profile_image) 
                                : asset('admins/dist/img/default.png') }}"
                        class="rounded-circle mb-3"
                        style="width:120px;height:120px;object-fit:cover;">

                    <h5 class="font-weight-bold">{{ $profile->company_name }}</h5>
                    <p class="text-muted mb-2">{{ $profile->email }}</p>

                    <div class="row mt-3">
                        <div class="col-6 border-right">
                            <h6 class="text-primary font-weight-bold mb-0">
                                {{ $totalJobs ?? 0 }}
                            </h6>
                            <small class="text-muted">Jobs Posted</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-primary font-weight-bold mb-0">
                                {{ $totalApplications ?? 0 }}
                            </h6>
                            <small class="text-muted">Applicants</small>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ABOUT COMPANY -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong>About Company</strong>
                </div>
                <div class="card-body">

                    <p><strong>Description:</strong><br>
                        <span class="text-muted">{{ $profile->description ?? 'Not added' }}</span>
                    </p>

                    <p><strong>Location:</strong><br>
                        <span class="text-muted">{{ $profile->location ?? 'Not added' }}</span>
                    </p>

                    <p><strong>Specialization:</strong></p>

                    @if($profile->expertise)
                        <ul class="pl-3">
                            @foreach(explode(',', $profile->expertise) as $exp)
                                <li class="text-muted">{{ trim($exp) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Not added</p>
                    @endif

                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>Profile Settings</strong>
                </div>

                <div class="card-body">

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Image Upload -->
                        <div class="form-group text-center">
                            <label class="font-weight-bold d-block">Company Image</label>
                            <input type="file" name="profile_image" class="form-control-file"
                                   onchange="readURL(this);">
                            <small class="text-muted">Allowed: JPG, PNG | Max: 2MB</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control"
                                   value="{{ $profile->company_name }}">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $profile->email }}">
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control"
                                   value="{{ $profile->location }}">
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <textarea name="expertise" class="form-control" rows="3">
{{ $profile->expertise }}
                            </textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>
</div>
@endsection
@push('scripts')
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('previewImage').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush