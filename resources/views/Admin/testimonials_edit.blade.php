@extends('layouts.Admin_layout')
@section('title', 'Edit Testimonial')
@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary u-bw-4px">

        <div class="d-md-flex justify-content-between align-items-center">

            <div>
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fa fa-edit text-primary mr-2"></i>
                    Edit Testimonial
                </h4>
                <small class="text-muted">
                    Update the selected testimonial details.
                </small>
            </div>

            <nav>
                <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active font-weight-bold">
                        Edit
                    </li>
                </ol>
            </nav>

        </div>

    </div>

    {{-- FORM --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            @if ($testimonial->isUserSubmitted())
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fa fa-user mr-2"></i>
                Submitted by a job seeker
                @if ($testimonial->jobApplication && $testimonial->jobApplication->job)
                    for <strong>{{ $testimonial->jobApplication->job->title }}</strong>
                @endif
                — review carefully before approving.
            </div>
            @endif

            <form action="{{ route('testimonials_update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="name">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input id="name" type="text" name="name" class="form-control"
                            value="{{ old('name', $testimonial->name) }}" required>
                        @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold" for="designation">Designation</label>
                        <input id="designation" type="text" name="designation" class="form-control"
                            value="{{ old('designation', $testimonial->designation) }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold" for="company">Company / City</label>
                        <input id="company" type="text" name="company" class="form-control"
                            value="{{ old('company', $testimonial->company) }}">
                    </div>
                </div>

                {{-- Review --}}
                <div class="form-group">
                    <label class="font-weight-bold" for="review">
                        Review <span class="text-danger">*</span>
                    </label>
                    <textarea id="review" name="review" rows="3" class="form-control" maxlength="1000"
                        required>{{ old('review', $testimonial->review) }}</textarea>
                    @error('review')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold" for="rating">Rating</label>
                        <select id="rating" name="rating" class="form-control" required>
                            @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'Star' : 'Stars' }}
                            </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="image">Photo</label>
                        <input id="image" type="file" name="image" class="form-control-file" accept="image/png, image/jpeg">
                        @error('image')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold" for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="pending" {{ $testimonial->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $testimonial->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $testimonial->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>

                @if ($testimonial->image)
                <div class="form-group">
                    <label class="font-weight-bold d-block">Current Photo</label>
                    <img src="{{ Storage::url($testimonial->image) }}" class="img-thumbnail"
                        style="max-width:120px;" alt="{{ $testimonial->name }}">
                </div>
                @endif

                {{-- Submit --}}
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-save mr-1"></i> Update Testimonial
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection