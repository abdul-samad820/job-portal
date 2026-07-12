@extends('layouts.User_layout')
@section('title', 'Write a Review')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-star"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Share Your Experience</h1>
                <small class="text-muted">You were hired for <strong>{{ $application->job->title ?? 'this role' }}</strong>
            @if (optional($application->job->admin)->company_name)
                at <strong>{{ $application->job->admin->company_name }}</strong>
            @endif.
            Your review will be shown on the homepage once an admin approves it.</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow rounded">
        <div class="card-body p-4">

            <form action="{{ route('user.review.store', $application->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="font-weight-bold" for="designation">Your Role / Title (optional)</label>
                    <input id="designation" type="text" name="designation" class="form-control"
                        value="{{ old('designation') }}" placeholder="e.g. Backend Developer">
                    @error('designation')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" for="rating">Rating</label>
                    <select id="rating" name="rating" class="custom-select" required>
                        <option value="">Select a rating</option>
                        @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i == 1 ? 'Star' : 'Stars' }}
                        </option>
                        @endfor
                    </select>
                    @error('rating')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" for="review">Your Review</label>
                    <textarea id="review" name="review" rows="4" class="form-control" maxlength="1000"
                        placeholder="Tell other job seekers about your experience..."
                        required>{{ old('review') }}</textarea>
                    @error('review')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" for="image">Photo (optional)</label>
                    <input id="image" type="file" name="image" class="form-control-file" accept="image/png, image/jpeg">
                    <small class="text-muted d-block mt-1">JPG or PNG, up to 2MB.</small>
                    @error('image')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-right">
                    <a href="{{ route('user.job_applied') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Review
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
