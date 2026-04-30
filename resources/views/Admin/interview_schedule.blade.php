@extends('layouts.Admin_layout')
@section('title', 'Interview Schedule')

@section('content')

<style>
    .p-4.shadow-sm:hover {
        transform: translateY(-2px);
        transition: 0.2s;
    }
    .container-fluid {
        max-width: 1400px;
        margin: auto;
    }

</style>

<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12">

            {{-- HEADER (Selected Candidates Style) --}}
            <div class="p-4 rounded shadow-sm mb-4" style="background:#f8faff; border-left:5px solid #007bff;">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    {{-- LEFT --}}
                    <div>
                        <h4 class="font-weight-bold mb-1 d-flex align-items-center">
                            <i class="fas fa-calendar-check text-primary mr-2"></i>
                            {{ $application->interview ? 'Reschedule Interview' : 'Schedule Interview' }}
                        </h4>

                        <small class="text-muted">
                            Candidate will be notified once interview is scheduled.
                        </small>
                    </div>

                    {{-- RIGHT --}}
                    <div class="d-flex align-items-center bg-white px-3 py-2 rounded shadow-sm">

                        <div class="bg-primary text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px; font-weight:600;">
                            {{ strtoupper(substr($application->user->name,0,1)) }}
                        </div>

                        <div>
                            <div class="font-weight-bold small">
                                {{ $application->user->name }}
                            </div>
                            <small class="text-muted">
                                {{ $application->job->title }}
                            </small>
                        </div>

                    </div>

                </div>
            </div>

            {{--  WARNING --}}
            @if($application->interview)
            <div class="alert alert-warning">
                Already scheduled:
                <strong>{{ $application->interview->formatted_date_time }}</strong>
            </div>
            @endif

            {{--  MAIN FORM --}}
            <div class="card shadow-sm border-0" style="border-radius:14px;">
                <div class="card-body p-0">

                    <form method="POST" action="{{ route('admin.interview.store', $application->id) }}">
                        @csrf

                        {{-- DATE & TIME --}}
                        <div class="p-4 border-bottom">
                            <h6 class="text-uppercase text-muted small mb-3">
                                <i class="fas fa-clock text-primary mr-2"></i>
                                Date & Time
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold small">Date</label>
                                    <input type="date" name="interview_date" class="form-control" value="{{ old('interview_date') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="font-weight-bold small">Time</label>
                                    <input type="time" name="interview_time" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- MODE --}}
                        <div class="p-4 border-bottom">
                            <h6 class="text-uppercase text-muted small mb-3">
                                <i class="fas fa-video text-primary mr-2"></i>
                                Interview Mode
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="d-block border rounded p-3 text-center mode-box">
                                        <input type="radio" name="mode" value="online" class="mr-2">
                                        <i class="fas fa-video text-primary"></i>
                                        <div class="mt-2 font-weight-bold">Online</div>
                                        <small class="text-muted">Video Call</small>
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="d-block border rounded p-3 text-center mode-box">
                                        <input type="radio" name="mode" value="offline" class="mr-2">
                                        <i class="fas fa-building text-primary"></i>
                                        <div class="mt-2 font-weight-bold">In-Person</div>
                                        <small class="text-muted">Office Visit</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- LOCATION --}}
                        <div class="p-4 border-bottom">
                            <h6 class="text-uppercase text-muted small mb-3">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                Location / Link
                            </h6>

                            <input type="text" name="location" class="form-control" placeholder="Enter meeting link or address">
                        </div>

                        {{-- NOTES --}}
                        <div class="p-4">
                            <h6 class="text-uppercase text-muted small mb-3">
                                <i class="fas fa-sticky-note text-primary mr-2"></i>
                                Notes
                            </h6>

                            <textarea name="notes" class="form-control" rows="3" placeholder="Instructions for candidate"></textarea>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="d-flex justify-content-end align-items-center p-4 border-top bg-light">

                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary px-4">
                                    {{ $application->interview ? 'Reschedule' : 'Schedule' }}
                                </button>

                                <a href="{{ route('job_application') }}" class="btn btn-outline-secondary ml-2">
                                    Cancel
                                </a>
                            </div>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
