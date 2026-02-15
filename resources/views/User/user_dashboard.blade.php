@extends('layouts.user_index')
@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style-user-file.css') }}">
@endpush
@section('content')


    @if (session('login_success'))
        <div class="position-fixed toast-wrapper">

            <div id="welcomeToast" class="custom-toast shadow-lg">

                <!-- Header -->
                <div class="toast-header-custom d-flex align-items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong class="mr-auto">Login Successful</strong>
                    <button type="button" class="close text-white" onclick="closeToast()">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="toast-body-custom">
                    {{ session('login_success') }}
                </div>

                <!-- Animated Line -->
                <div class="toast-progress"></div>
            </div>
        </div>
    @endif

    <div class="row">
        {{-- New Jobs --}}
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm"
                style="border-radius:20px;">

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
                    <i class="fas fa-user-check mr-2" style="color: {{ $color }};"></i>

                    <span class="font-weight-semibold mr-2">
                        Profile Strength:
                    </span>

                    <span id="counter" class="font-weight-bold mr-2" style="color: {{ $color }};">
                        0%
                    </span>

                    <span style="color: {{ $color }};">
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

                <!-- Arrow Button -->
                <a href="{{ route('user.add_profile') }}"
                    class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width:35px; height:35px;">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>
        </div>
    </div>

    {{-- ================= QUICK ACTIONS ================= --}}
    <div class="row mt-4">

        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0">
                            <i class="fas fa-bolt text-warning mr-2"></i>
                            Quick Actions
                        </h6>
                    </div>

                    <div class="row text-center">

                        <div class="col-md-4 mb-3">
                            <a href="{{ route('user.jobs') }}" class="action-box d-block p-3 rounded">
                                <div class="icon-circle bg-primary-light mb-2">
                                    <i class="fas fa-search text-primary"></i>
                                </div>
                                <h6 class="font-weight-bold mb-1">Browse Jobs</h6>
                                <small class="text-muted">Find new opportunities</small>
                            </a>
                        </div>

                        <div class="col-md-4 mb-3">
                            <a href="{{ route('user.profile') }}" class="action-box d-block p-3 rounded">
                                <div class="icon-circle bg-secondary-light mb-2">
                                    <i class="fas fa-user-edit text-secondary"></i>
                                </div>
                                <h6 class="font-weight-bold mb-1">Update Profile</h6>
                                <small class="text-muted">Improve job visibility</small>
                            </a>
                        </div>

                        <div class="col-md-4 mb-3">
                            <a href="{{ route('user.saved.jobs') }}" class="action-box d-block p-3 rounded">
                                <div class="icon-circle bg-success-light mb-2">
                                    <i class="fas fa-bookmark text-success"></i>
                                </div>
                                <h6 class="font-weight-bold mb-1">Saved Jobs</h6>
                                <small class="text-muted">View your saved jobs</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="card shadow-lg border-0 rounded-lg mt-4">
        <div class="card-body p-4">

            <!-- Header -->
            <div class="mb-4">
                <h5 class="font-weight-bold mb-1">
                    <i class="fas fa-chart-pie text-primary mr-2"></i>
                    Application Analytics
                </h5>
                <small class="text-muted">
                    Track your application performance
                </small>
            </div>

            <div class="row">

                <!-- LEFT SIDE -->
                <div class="col-md-7">

                    <div class="row">

                        @php
                            $pending = (int) $pendingCount;
                            $shortlisted = (int) $shortlistedCount;
                            $hired = (int) $hiredCount;
                            $rejected = (int) $rejectedCount;

                            $total = $pending + $shortlisted + $hired + $rejected;
                            $successRate = $total > 0 ? round(($hired / $total) * 100) : 0;
                        @endphp

                        <!-- Pending -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-box stat-warning">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-hourglass-half text-white"></i>
                                </div>
                                <div>
                                    <h4 class="counter mb-0" data-count="{{ $pending }}">0</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>

                        <!-- Shortlisted -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-box stat-primary">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                                <div>
                                    <h4 class="counter mb-0" data-count="{{ $shortlisted }}">0</h4>
                                    <small class="text-muted">Shortlisted</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hired -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-box stat-success">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="counter mb-0" data-count="{{ $hired }}">0</h4>
                                    <small class="text-muted">Hired</small>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-box stat-danger">
                                <div class="stat-icon bg-danger">
                                    <i class="fas fa-times-circle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="counter mb-0" data-count="{{ $rejected }}">0</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Rate -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="font-weight-bold">Hiring Success Rate</small>
                            <small class="font-weight-bold text-success">
                                {{ $successRate }}%
                            </small>
                        </div>

                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $successRate }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE CHART -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">

                            <h6 class="font-weight-bold mb-0">
                                Application Status
                            </h6>
                            <small class="text-muted">
                                Distribution Overview
                            </small>

                            <div class="position-relative mt-3" style="height:230px;">

                                <canvas id="statusChart"></canvas>

                                <!-- Center Text -->
                                <div id="chartCenterText"
                                    style="position:absolute;
                            top:50%;
                            left:50%;
                            transform:translate(-50%,-50%);
                            text-align:center;">
                                    <h4 class="font-weight-bold mb-0 text-primary" id="successText">
                                        0%
                                    </h4>
                                    <small class="text-muted">Success</small>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SAVED + RECENT JOBS SECTION --}}
    <div class="row mt-4">
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
                                <img src="{{ $job->admin && $job->admin->profile_image
                                    ? asset('storage/admins/' . $job->admin->profile_image)
                                    : asset('default/company.png') }}"
                                    width="50" height="50" style="border-radius:8px; object-fit:cover;"
                                    alt="Company Logo">
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
                            <div class="text-muted ml-3">
                                <a href="{{ route('user.job_applied') }}"><i class="fas fa-chevron-right"></i></a>
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

            /* ================= PROFILE STRENGTH ================= */

            const percentage = {{ $profileCompletion ?? 0 }};
            const progressBar = document.getElementById("progressBar");
            const profileCounter = document.getElementById("counter");

            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = percentage + "%";
                }, 200);
            }

            if (profileCounter) {
                let count = 0;
                const interval = setInterval(() => {
                    count++;
                    profileCounter.innerText = count + "%";
                    if (count >= percentage) clearInterval(interval);
                }, 15);
            }


            /* ================= COUNTERS ================= */

            document.querySelectorAll('.counter').forEach(counter => {

                const target = parseInt(counter.dataset.count) || 0;
                let count = 0;
                const increment = target > 50 ? 5 : 1;

                const update = setInterval(() => {
                    count += increment;
                    if (count >= target) {
                        counter.innerText = target;
                        clearInterval(update);
                    } else {
                        counter.innerText = count;
                    }
                }, 20);
            });


            /* ================= DOUGHNUT CHART ================= */

            const pending = {{ $pending ?? 0 }};
            const shortlisted = {{ $shortlisted ?? 0 }};
            const hired = {{ $hired ?? 0 }};
            const rejected = {{ $rejected ?? 0 }};

            const total = pending + shortlisted + hired + rejected;
            const successRate = total > 0 ? Math.round((hired / total) * 100) : 0;

            const ctx = document.getElementById('statusChart');

            if (ctx && typeof Chart !== "undefined") {

                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Shortlisted', 'Hired', 'Rejected'],
                        datasets: [{
                            data: [pending, shortlisted, hired, rejected],
                            backgroundColor: [
                                '#ffc107',
                                '#007bff',
                                '#28a745',
                                '#dc3545'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 70,
                        legend: {
                            display: false
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1500
                        }
                    }
                });

                // Center % animation
                const successText = document.getElementById("successText");
                if (successText) {
                    let count = 0;
                    const interval = setInterval(function() {
                        count++;
                        successText.innerText = count + "%";
                        if (count >= successRate) clearInterval(interval);
                    }, 20);
                }
            }

        });


        /* ================= TOAST ================= */

        function closeToast() {
            const toast = document.getElementById('welcomeToast');
            if (toast) {
                toast.style.opacity = "0";
                toast.style.transform = "translateX(120%)";
                setTimeout(() => {
                    toast.parentElement.style.display = "none";
                }, 400);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(closeToast, 5000);
        });
    </script>
@endpush
