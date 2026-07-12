@extends('layouts.User_layout')
@section('title', 'Dashboard')
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

<div class="sa-page-header">
    <div>
        <h1 class="sa-page-title font-weight-bold text-dark mb-0">Welcome back, {{ \Illuminate\Support\Str::limit(explode(' ', $authUser->name ?? 'Candidate')[0], 20) }}</h1>
        <small class="text-muted">Here's what's happening with your job search today.</small>
    </div>
    <a href="{{ route('user.jobs') }}" class="btn btn-primary">
        <i class="fas fa-search mr-1"></i> Find Jobs
    </a>
</div>

<div class="row">
    {{-- New Jobs --}}
    <div class="col-6 col-md-3 mb-3">
        <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm stat-card sa-hoverable">

            <div>
                <h4 class="mb-1 stat-card-value">{{ $newJobsCount }}</h4>
                <small class="stat-card-label">New Jobs</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon-circle">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>

    {{-- Applied Jobs --}}
    <div class="col-6 col-md-3 mb-3">
        <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm stat-card sa-hoverable">

            <div>
                <h4 class="mb-1 stat-card-value">{{ $appliedJobsCount }}</h4>
                <small class="stat-card-label">Applied Jobs</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon-circle">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    {{-- saved jobs --}}
    <div class="col-6 col-md-3 mb-3">
        <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm stat-card sa-hoverable">

            <div>
                <h4 class="mb-1 stat-card-value">{{ $savedJobsCount }}</h4>
                <small class="stat-card-label">Jobs Saved</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon-circle">
                <i class="fas fa-bookmark"></i>
            </div>
        </div>
    </div>

    {{-- recommended jobs --}}
    <div class="col-6 col-md-3 mb-3">
        <a href="{{ route('user.jobs', ['recommended' => 1]) }}" class="text-decoration-none">
        <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm stat-card sa-hoverable">

            <div>
                <h4 class="mb-1 stat-card-value">{{ $recommendedJobsCount }}</h4>
                <small class="stat-card-label">Recommended Jobs</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center stat-icon-circle">
                <i class="fas fa-lightbulb"></i>

            </div>
        </div>
        </a>
    </div>
</div>

{{-- Profile Strength Single Line --}}
<div class="row mt-4">

    <div class="col-12">
        <div class="bg-white p-3 shadow-sm u-radius-15px profile-strength-card">

            @php
            if ($profileCompletion < 40) { $strength='Weak' ; $color='#ef4444' ; } elseif ($profileCompletion < 75) {
                $strength='Good' ; $color='#f59e0b' ; } else { $strength='Strong' ; $color='#22c55e' ; } @endphp

            <div class="d-flex align-items-center justify-content-between flex-wrap profile-strength-row">

                {{-- Left Side --}}
                <div class="d-flex align-items-center flex-wrap profile-strength-info">
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

                {{-- Right Side: Progress Bar + Arrow --}}
                <div class="d-flex align-items-center profile-strength-bar-wrap">
                    <div class="u-w-40 profile-strength-progress">
                        <div class="u-h-6px-bg-e5e7eb-radius-10px">
                            <div id="progressBar" style="height:6px;
                                           width:0%;
                                           background: {{ $color }};
                                           border-radius:10px;
                                           transition: width 1.5s ease;">
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('user.add_profile') }}" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center u-w-35px-h-35px ml-3 flex-shrink-0">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

            </div>
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

                    <div class="col-6 col-md-4 mb-3">
                        <a href="{{ route('user.jobs') }}" class="action-box d-block p-3 rounded">
                            <div class="icon-circle bg-primary-light mb-2">
                                <i class="fas fa-search text-primary"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1">Browse Jobs</h6>
                            <small class="text-muted">Find new opportunities</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 mb-3">
                        <a href="{{ route('user.profile') }}" class="action-box d-block p-3 rounded">
                            <div class="icon-circle bg-secondary-light mb-2">
                                <i class="fas fa-user-edit text-secondary"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1">Update Profile</h6>
                            <small class="text-muted">Improve job visibility</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 mb-3">
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
            <div class="col-md-7 order-2 order-md-1 mt-4 mt-md-0">

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

                    <div class="progress u-h-8px">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $successRate }}%">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE CHART -->
            <div class="col-md-5 order-1 order-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">

                        <h6 class="font-weight-bold mb-0">
                            Application Status
                        </h6>
                        <small class="text-muted">
                            Distribution Overview
                        </small>

                        <div class="position-relative mt-3 u-h-230px">

                            <canvas id="statusChart"></canvas>

                            <!-- Center Text -->
                            <div class="u-pos-absolute-top-50-left-50-tf-translate-50-5-ta-center" id="chartCenterText">
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
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-lg">

            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    Recent Applied Jobs
                </h6>
            </div>

            <div class="card-body">
                <div class="row">

                    @forelse ($recentAppliedJobs as $recentjob)
                    @php $job = $recentjob->job; @endphp

                    <div class="col-md-3 mb-4">
                        <div class="card job-card-pro border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">

                                {{-- Top Section --}}
                                <div class="d-flex align-items-center mb-3">

                                    <div class="mr-3">
                                        <img src="{{ $job->admin && $job->admin->profile_image
                                                    ? asset('storage/admins/' . $job->admin->profile_image)
                                                    : asset('default/company.png') }}" width="50" height="50"
                                            class="u-radius-12px-fit-cover" alt="{{ $job->title ?? 'Job' }}">
                                    </div>

                                    <div>
                                        <div class="font-weight-bold u-fs-0-933rem">
                                            {{ $job->title }}
                                        </div>

                                        <div class="text-muted u-fs-0-8rem">
                                            {{ ucfirst($job->type) }} • {{ $job->location }}
                                        </div>
                                    </div>

                                </div>

                                {{-- Salary --}}
                                <div class="text-success font-weight-bold mb-2 u-fs-var-fs-sm">
                                    @if($job->min_salary && $job->max_salary)
                                    ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{
                                    number_format($job->max_salary / 100000, 1) }}L
                                    @elseif($job->min_salary)
                                    ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                                    @else
                                    Salary not disclosed
                                    @endif
                                </div>

                                {{-- Status --}}
                                @php
                                $statusClass =
                                [
                                'pending' => 'badge-warning',
                                'shortlisted' => 'badge-info',
                                'hired' => 'badge-success',
                                'rejected' => 'badge-danger',
                                ][$recentjob->status] ?? 'badge-secondary';
                                @endphp

                                <div class="mb-3">
                                    <span class="badge {{ $statusClass }} status-badge">
                                        {{ ucfirst($recentjob->status) }}
                                    </span>
                                </div>

                                {{-- Push footer to bottom --}}
                                <div class="mt-auto text-right">
                                    <a href="{{ route('user.job_applied') }}" class="text-primary job-footer-link">
                                        View Details
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="col-12 text-center text-muted">
                        No recent job applications found.
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>

{{-- UPCOMING INTERVIEWS + SAVED JOBS --}}
<div class="row mt-4">

    {{-- Upcoming Interviews --}}
    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
        <div class="card shadow-sm border-0 rounded-lg h-100">

            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="far fa-calendar-alt text-primary mr-1"></i>
                    Upcoming Interviews
                </h6>
                <a href="{{ route('user.interviews') }}" class="text-primary small font-weight-bold text-decoration-none">
                    View all
                </a>
            </div>

            <div class="card-body">
                @forelse ($upcomingInterviews as $interview)
                <div class="d-flex align-items-start justify-content-between {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                    <div class="d-flex align-items-start">
                        <div class="u-w-42px-h-42px rounded-circle bg-light d-flex align-items-center justify-content-center mr-3 flex-shrink-0">
                            <i class="far fa-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <p class="mb-1 font-weight-bold text-dark">
                                {{ $interview->application->job->title ?? 'Job' }}
                            </p>
                            <small class="text-muted d-block">
                                <i class="fas fa-building mr-1"></i>
                                {{ $interview->application->job->admin->company_name ?? 'N/A' }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="far fa-clock mr-1"></i>
                                {{ $interview->formatted_date_time }}
                                &middot; {{ ucfirst($interview->mode) }}
                            </small>
                        </div>
                    </div>
                    <span class="badge badge-{{ $interview->status_color }} align-self-center">
                        {{ ucfirst($interview->status) }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    No upcoming interviews scheduled.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Saved Jobs Preview --}}
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 rounded-lg h-100">

            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fas fa-bookmark text-primary mr-1"></i>
                    Saved Jobs
                </h6>
                <a href="{{ route('user.saved.jobs') }}" class="text-primary small font-weight-bold text-decoration-none">
                    View all
                </a>
            </div>

            <div class="card-body">
                @forelse ($savedJobs as $savedJob)
                @php $job = $savedJob->job; @endphp
                @if ($job)
                <a href="{{ route('user.job_single', $job->id) }}"
                   class="d-flex align-items-center justify-content-between text-decoration-none {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="u-w-42px-h-42px rounded-circle bg-light d-flex align-items-center justify-content-center mr-3 flex-shrink-0">
                            <i class="fas fa-briefcase text-primary"></i>
                        </div>
                        <div>
                            <p class="mb-1 font-weight-bold text-dark">
                                {{ $job->title }}
                            </p>
                            <small class="text-muted d-block">
                                <i class="fas fa-building mr-1"></i>
                                {{ $job->admin->company_name ?? 'N/A' }}
                                @if ($job->location)
                                &middot; <i class="fas fa-map-marker-alt ml-1 mr-1"></i>{{ $job->location }}
                                @endif
                            </small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
                @endif
                @empty
                <div class="text-center text-muted py-3">
                    You haven't saved any jobs yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

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

            if (count >= percentage) {
                clearInterval(interval);
            }
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
                    backgroundColor: ['#ffc107', '#007bff', '#28a745', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 70,
                legend: { display: false },
                animation: {
                    animateRotate: true,
                    duration: 1500
                }
            }
        });

        const successText = document.getElementById("successText");

        if (successText) {
            let count = 0;

            const interval = setInterval(() => {
                count++;
                successText.innerText = count + "%";

                if (count >= successRate) {
                    clearInterval(interval);
                }
            }, 20);
        }
    }


    /* ================= TOAST SYSTEM ================= */

    const toast = document.getElementById('welcomeToast');
    let toastTimer = null;

    function closeToast() {
        if (!toast) return;

        toast.style.opacity = "0";
        toast.style.transform = "translateX(120%)";

        setTimeout(() => {
            toast.remove();
        }, 400);
    }

    function startToastTimer() {
        toastTimer = setTimeout(closeToast, 5000);
    }

    if (toast) {

        // Start auto close
        startToastTimer();

        // Pause on hover
        toast.addEventListener("mouseenter", () => {
            clearTimeout(toastTimer);
        });

        // Resume on leave
        toast.addEventListener("mouseleave", () => {
            startToastTimer();
        });

        // Scroll pe close
        window.addEventListener("scroll", closeToast);

        // Outside click pe close
        document.addEventListener("click", function (e) {
            if (!e.target.closest('#welcomeToast')) {
                closeToast();
            }
        });
    }

});
</script>
@endpush