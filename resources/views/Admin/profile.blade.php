@extends('layouts.index')
@section('title', 'Company Profile')

@section('content')
<div class="container mt-4">

    <!-- ================= PROFILE SECTION ================= -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center">

            <div class="position-relative d-inline-block">
                <img id="previewImage"
                    src="{{ $profile->profile_image 
                            ? asset('storage/admins/'.$profile->profile_image) 
                            : asset('admins/dist/img/default.png') }}"
                    class="rounded-circle shadow"
                    style="width:130px;height:130px;object-fit:cover;border:4px solid #fff;">
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

            @if($profile->expertise)
                @foreach(explode('.', $profile->expertise) as $exp)
                    @if(trim($exp))
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
                    <label class="font-weight-bold d-block">Update Company Image</label>
                    <input type="file" name="profile_image" class="form-control-file"
                        onchange="readURL(this);">
                    <small class="text-muted">Allowed: JPG, PNG | Max 2MB</small>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name"
                                class="form-control"
                                value="{{ $profile->company_name }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"
                                class="form-control"
                                value="{{ $profile->email }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location"
                        class="form-control"
                        value="{{ $profile->location }}">
                </div>

                <div class="form-group">
                    <label>Specialization</label>
                    <textarea name="expertise"
                        class="form-control"
                        rows="3">{{ $profile->expertise }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    Update Profile
                </button>

            </form>

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
