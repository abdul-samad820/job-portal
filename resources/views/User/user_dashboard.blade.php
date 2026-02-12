@extends('layouts.user_index')
@section('title', 'Dashboard')
@section('content')

    <style>
        .white-box {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
        }

        /* Hover effect for applied job */

        .job-box:hover {
            background: #eef5ff;
            border-left-color: #3b82f6 !important;
            transform: translateX(3px);
        }
    </style>


    <div class="row">

        {{-- New Jobs --}}
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm" style="border-radius:20px;">

                <div>
                    <h4 class="mb-1" style="font-weight:600; font-size:24px;">{{ $newJobsCount }}</h4>
                    <small style="color:#73808D;">New Jobs</small>
                </div>

                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:50px; height:50px; background:#D8E8FF;">
                    <i class="fas fa-user" style="color:#10392E; font-size:18px;"></i>
                </div>
            </div>
        </div>

        {{-- Applied Jobs --}}
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm"
                style="border-radius:20px;">

                <div>
                    <h4 class="mb-1" style="font-weight:600; font-size:24px;">{{ $appliedJobsCount }}</h4>
                    <small style="color:#73808D;">Applied Jobs</small>
                </div>

                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:50px; height:50px; background:#D8E8FF;">
                    <i class="fas fa-bookmark" style="color:#10392E; font-size:18px;"></i>
                </div>
            </div>
        </div>

        {{-- Placeholder --}}
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm"
                style="border-radius:20px;">

                <div>
                    <h4 class="mb-1" style="font-weight:600; font-size:24px;">{{ $savedJobsCount }}</h4>
                    <small style="color:#73808D;">Jobs Saved</small>
                </div>

                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:50px; height:50px; background:#D8E8FF;">
                    <i class="fas fa-eye" style="color:#10392E; font-size:18px;"></i>
                </div>
            </div>
        </div>

        {{-- Placeholder 2 --}}
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm"
                style="border-radius:20px;">

                <div>
                    <h4 class="mb-1" style="font-weight:600; font-size:24px;">{{ $recommendedJobsCount }}</h4>
                    <small style="color:#73808D;">Recommended Jobs</small>
                </div>

                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:50px; height:50px; background:#D8E8FF;">
                    <i class="fas fa-magic" style="color:#10392E; font-size:18px;"></i>

                </div>
            </div>
        </div>

    </div>


    {{-- Profile Strength Single Line --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="bg-white p-3 shadow-sm d-flex align-items-center justify-content-between"
                style="border-radius:15px;">

                @php
                    if ($profileCompletion < 40) {
                        $strength = 'Weak';
                        $color = '#ef4444';
                    } elseif ($profileCompletion < 75) {
                        $strength = 'Good';
                        $color = '#f59e0b';
                    } else {
                        $strength = 'Strong';
                        $color = '#22c55e';
                    }
                @endphp

                {{-- Left Side --}}
                <div class="d-flex align-items-center">

                    <i class="fas fa-user-check me-2" style="color: {{ $color }};"></i>

                    <span class="fw-semibold me-2">
                        Profile Strength:
                    </span>

                    <span id="counter" class="fw-bold me-2" style="color: {{ $color }};">
                        0%
                    </span>

                    <span class="fw-medium" style="color: {{ $color }};">
                        ({{ $strength }})
                    </span>

                </div>

                {{-- Right Progress Bar --}}
                <div style="width:40%;">
                    <div style="height:6px; background:#e5e7eb; border-radius:10px;">
                        <div id="progressBar"
                            style="height:6px;
                                width:0%;
                                background: {{ $color }};
                                border-radius:10px;
                                transition: width 1.5s ease;">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SAVED + RECENT JOBS SECTION --}}
    <div class="row mt-4">

        {{-- Saved Jobs --}}
        <div class="col-md-8">
            <div class="white-box p-4">
                <h5 class="mb-3">Saved Jobs</h5>
                <hr>

                @forelse($savedJobs as $saved)
                    <div class="card mb-3 p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h6 class="mb-1">{{ $saved->job->title }}</h6>
                                <small class="text-muted">
                                    {{ $saved->job->location }}
                                </small>
                            </div>

                            <a href="{{ route('user.unsave.job', $saved->job->id) }}" class="text-danger">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                        </div>
                    </div>

                @empty

                    <div class="text-center py-5">
                        <i class="far fa-bookmark" style="font-size:40px; color:#718096;"></i>

                        <h5 class="mt-3">No Saved Jobs</h5>

                        <a href="{{ route('user.jobs') }}" class="btn btn-success btn-sm mt-2">
                            Browse Jobs
                        </a>
                    </div>
                @endforelse

            </div>
        </div>


        {{-- Recent Applied Jobs --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">

                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">Recent Applied Jobs</h6>
                </div>

                <div class="card-body pt-0">

                    @forelse ($recentAppliedJobs as $recentjob)
                        @php $job = $recentjob->job; @endphp

                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded job-box"
                            style="transition: .2s; border-left: 4px solid transparent;">

                            {{-- Job Image --}}
                            <div class="job-logo mr-3">
                                <img src="{{ $job->admin->profile_image ? asset('uploads/admins/' . $job->admin->profile_image) : asset('default/company.png') }}"
                                    width="50" height="50" style="border-radius: 8px; object-fit: cover;">
                            </div>

                            {{-- Job Info --}}
                            <div class="flex-grow-1">

                                <div class="font-weight-bold text-dark" style="font-size: 14px;">
                                    {{ $job->title }}
                                </div>

                                <div class="text-muted" style="font-size: 12px;">
                                    {{ ucfirst($job->type) }} • {{ $job->location }}
                                </div>

                                <div class="text-success" style="font-size: 12px;">
                                    <i class="fas fa-rupee-sign"></i> {{ $job->salary ?? 'Not Disclosed' }} LPA
                                </div>

                                <div class="text-primary" style="font-size: 12px;">
                                    Status: {{ ucfirst($recentjob->status) }}
                                </div>

                            </div>

                            {{-- Arrow --}}
                            <div class="text-muted ml-3">
                                <i class="fas fa-chevron-right"></i>
                            </div>

                        </div>

                    @empty
                        <p class="text-muted">No recent job applications found.</p>
                    @endforelse

                </div>

            </div>
        </div>


    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let percentage = {{ $profileCompletion }};
            let counter = document.getElementById("counter");
            let progressBar = document.getElementById("progressBar");

            // Animate Progress Bar
            setTimeout(() => {
                progressBar.style.width = percentage + "%";
            }, 200);

            // Animate Counter
            let count = 0;
            let interval = setInterval(() => {
                if (count >= percentage) {
                    clearInterval(interval);
                } else {
                    count++;
                    counter.innerText = count + "%";
                }
            }, 15);

        });
    </script>
@endpush
