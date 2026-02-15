@extends('layouts.index')
@section('title', 'Profile')

@section('content')
    <div class="container">
        <div class="row">

            <!-- LEFT COLUMN -->
            <div class="col-lg-4">

                <!-- Company Profile Card -->
                <div class="card rounded shadow mb-4 p-4 text-center">
                    <div class="card-body">

                        <!-- Profile Image -->
                        <img src="{{ Auth::guard('admin')->user()->profile_image
                            ? asset('storage/admins/' . Auth::guard('admin')->user()->profile_image)
                            : asset('admins/dist/img/default.png') }}"
                            class="img-circle elevation-2" style="width:100px; height:100px; object-fit:cover;"
                            alt="Company Image">
                        <!-- Company Name -->
                        <h4 class="mt-3 font-weight-bold text-dark">{{ $profile->company_name }}</h4>

                        <!-- Email -->
                        <p class="text-muted">{{ $profile->email }}</p>

                        <!-- Stats -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="mx-3">
                                <h5 class="font-weight-bold text-primary mb-0">{{ $totalJobs ?? 0 }}</h5>
                                <small class="text-muted">Jobs Posted</small>
                            </div>
                            <div class="mx-3">
                                <h5 class="font-weight-bold text-primary mb-0">{{ $totalApplications ?? 0 }}</h5>
                                <small class="text-muted">Applicants</small>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Company Details -->
                <div class="card rounded shadow mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="font-weight-bold mb-0">
                            <i class="fa fa-building text-primary mr-2"></i>About Company
                        </h5>
                    </div>

                    <div class="card-body">

                        <strong><i class="fa fa-book mr-2 text-primary"></i>Description</strong>
                        <p class="text-muted">{{ $profile->description }}</p>
                        <hr>

                        <strong><i class="fa fa-map-marker mr-2 text-primary"></i>Location</strong>
                        <p class="text-muted">{{ $profile->location }}</p>
                        <hr>

                        <strong><i class="fa fa-project-diagram mr-2 text-primary"></i>Specialization</strong>

                        @if ($profile->expertise)
                            <ul class="mt-2">
                                @foreach (explode(',', $profile->expertise) as $exp)
                                    <li class="text-muted">{{ trim($exp) }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Not added yet</p>
                        @endif

                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-8">

                <div class="card rounded shadow">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="font-weight-bold mb-0">
                            <i class="fa fa-cog text-primary mr-2"></i>Profile Settings
                        </h5>
                    </div>

                    <div class="card-body p-4">

                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Profile Image -->
                            <div class="form-group text-center mb-4">

                                <div class="mb-3">
                                    <img src="{{ $profile->profile_image
                                        ? asset('storage/admins/' . $profile->profile_image)
                                        : asset('admins/dist/img/default.png') }}"
                                        class="rounded-circle" style="width:120px; height:120px; object-fit:cover;"
                                        alt="Company Image">
                                </div>

                                <label class="font-weight-bold d-block">Company Image</label>
                                <input type="file" name="profile_image" class="form-control-file"
                                    onchange="readURL(this);">
                                <small class="text-muted d-block mt-1">Allowed: JPG, PNG | Max: 2MB</small>

                            </div>

                            <hr>

                            <!-- Company Name -->
                            <div class="form-group">
                                <label class="font-weight-bold">Company Name</label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ $profile->company_name }}">
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $profile->email }}">
                            </div>

                            <!-- Location -->
                            <div class="form-group">
                                <label class="font-weight-bold">Location</label>
                                <input type="text" name="location" class="form-control"
                                    value="{{ $profile->location }}">
                            </div>

                            <!-- Specialization -->
                            <div class="form-group">
                                <label class="font-weight-bold">Specialization</label>
                                <textarea name="expertise" class="form-control" rows="3" placeholder="e.g. IT Consulting, Web Development">{{ $profile->expertise }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2">
                                <i class="fa fa-check-circle mr-2"></i>Update Profile
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

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
