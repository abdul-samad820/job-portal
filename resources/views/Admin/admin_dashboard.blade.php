@extends('layouts.Admin_layout')
@section('title', 'Dashboard')

@section('content')

<div class="sa-page-header">
    <div>
        <h1 class="sa-page-title font-weight-bold text-dark mb-0">Welcome back, {{ \Illuminate\Support\Str::limit($adminUser->company_name ?? 'Admin', 30) }}</h1>
        <small class="text-muted">Here's how your hiring is going today.</small>
    </div>
    <a href="{{ route('admin.job_add') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i> Post a Job
    </a>
</div>

<div class="row">
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-pill sa-hoverable">
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <div class="stat-value mb-1">{{ $totalJobs ?? 'No Data Available' }}</div>
                    <div class="stat-label">Total Jobs</div>
                </div>
                <div class="stat-round-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-pill sa-hoverable">
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <div class="stat-value mb-1">{{ $activeJobs }}</div>
                    <div class="stat-label">Active Jobs</div>
                </div>
                <div class="stat-round-icon">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-pill sa-hoverable">
            <div class="d-flex justify-content-between align-items-center p-3">

                <div>
                    <div class="stat-value mb-1">{{ $expiredJobs }}</div>
                    <div class="stat-label">Expired Jobs</div>
                </div>

                <div class="stat-round-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-pill sa-hoverable">
            <div class="d-flex justify-content-between align-items-center p-3">

                <div>
                    <div class="stat-value mb-1">{{ $totalApplications }}</div>
                    <div class="stat-label">Total Applications</div>
                </div>

                <div class="stat-round-icon">
                    <i class="fas fa-file-alt"></i>
                </div>

            </div>
        </div>
    </div>

</div>

@php
$activePercentage = $totalJobs > 0 ? round(($activeJobs / $totalJobs) * 100) : 0;
@endphp

<div class="card shadow-sm border-0 mt-1">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="font-weight-bold text-dark">
                Active Jobs Ratio
            </small>
            <small class="font-weight-bold text-primary">
                <span id="activePercentText">0</span>%
            </small>
        </div>

        <div class="progress premium-progress">
            <div id="activeProgressBar" class="progress-bar premium-bar" role="progressbar" aria-valuemin="0"
                aria-valuemax="100">
            </div>
        </div>

    </div>
</div>

<div class="card shadow-sm border-0 mt-2 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="font-weight-bold mb-0">Quick Actions</h5>
        <small class="text-muted">Jump straight to a task</small>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3 col-6 mb-3">
                <a href="{{ route('admin.job_add') }}" class="d-flex align-items-center p-3 rounded-lg hover-bg" style="border: 1px solid #edf2f7; text-decoration: none;">
                    <div class="stat-round-icon mr-3" style="width:38px;height:38px;font-size:14px;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 13.5px;">Add Job</span>
                </a>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <a href="{{ route('job_application') }}" class="d-flex align-items-center p-3 rounded-lg hover-bg" style="border: 1px solid #edf2f7; text-decoration: none;">
                    <div class="stat-round-icon mr-3" style="width:38px;height:38px;font-size:14px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 13.5px;">Applications</span>
                </a>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <a href="{{ route('admin.job_category') }}" class="d-flex align-items-center p-3 rounded-lg hover-bg" style="border: 1px solid #edf2f7; text-decoration: none;">
                    <div class="stat-round-icon mr-3" style="width:38px;height:38px;font-size:14px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 13.5px;">Categories</span>
                </a>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div class="dropdown">
                    <a href="#" id="exportDropdownBtn" role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false"
                       class="d-flex align-items-center p-3 rounded-lg hover-bg" style="border: 1px solid #edf2f7; text-decoration: none;">
                        <div class="stat-round-icon mr-3" style="width:38px;height:38px;font-size:14px;">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <span class="font-weight-bold text-dark" style="font-size: 13.5px;">Export</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="exportDropdownBtn">
                        <a class="dropdown-item" href="{{ route('admin.export.jobs') }}">
                            <i class="fa fa-briefcase text-primary mr-2"></i> Export Jobs
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.export.applications') }}">
                            <i class="fa fa-file-alt text-success mr-2"></i> Export Applications
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.export.candidates') }}">
                            <i class="fa fa-users text-info mr-2"></i> Export Candidates
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.selectedList.export') }}">
                            <i class="fa fa-star text-warning mr-2"></i> Export Shortlisted/Hired
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row">

    <!-- ================= LEFT : TOP JOB ================= -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm premium-top-card h-100">

            <div class="card border-0 shadow-sm rounded stat-hover">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <!-- Left Content -->
                        <div>
                            <div class="text-uppercase text-muted font-weight-bold small mb-2 letter-spacing">
                                Top Performing Job
                            </div>

                            <h5 class="font-weight-bold text-dark mb-2">
                                {{ $topJob->title ?? 'No Data Available' }}
                            </h5>

                            @if ($topJob)
                            <div class="text-muted small">
                                <span class="badge bg-light text-dark border mr-2 px-3 py-1 rounded-pill">
                                    {{ ucfirst($topJob->type ?? 'N/A') }}
                                </span>

                                @if (!empty($topJob->location))
                                <span class="text-muted">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $topJob->location }}
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Right Counter -->
                        <div class="text-end">
                            <div class="display-6 font-weight-bold text-gradient">
                                {{ $topJob->applications_count ?? 0 }}
                            </div>
                            <div class="text-muted small text-uppercase font-weight-bold">
                                Applications
                            </div>
                        </div>

                    </div>

                    <!-- Subtle Divider -->
                    <hr class="my-3">

                    <!-- Progress Bar -->
                    @if ($topJob)
                    <div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Performance</span>
                            <span class="font-weight-bold text-primary">High Demand</span>
                        </div>

                        <div class="premium-progress">
                            <div class="premium-bar u-w-85"></div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- ================= RIGHT : SYSTEM ALERTS ================= -->
    <div class="col-lg-6 mb-4">
        <div class="card premium-card h-100">
            <div class="card-body p-4">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="font-weight-bold mb-0 text-dark text-uppercase small">
                        <i class="fas fa-shield-alt text-primary mr-2"></i>
                        System Alerts
                    </h6>

                    <span class="badge premium-badge-light">
                        {{ ($expiringJobs ?? 0) + ($pendingCount ?? 0) }}
                    </span>
                </div>

                {{-- Expiring Jobs --}}
                @if ($expiringJobs > 0)
                <div class="premium-alert warning mb-3 d-flex align-items-center">

                    <!-- Left Side -->
                    <div class="d-flex align-items-center flex-grow-1">

                        <div class="alert-icon warning-icon mr-3">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div>
                            <div class="font-weight-bold text-dark">
                                {{ $expiringJobs }} Jobs Expiring
                            </div>
                            <small class="text-muted">
                                Deadline within next 3 days
                            </small>
                        </div>

                    </div>

                    <!-- Right Button -->
                    <a href="{{ route('admin.job') }}" class="btn btn-sm premium-btn-warning ml-3">
                        View
                    </a>

                </div>
                @endif


                {{-- Pending Applications --}}
                @if ($pendingCount > 0)
                <div class="premium-alert danger mb-3 d-flex align-items-center">

                    <div class="d-flex align-items-center flex-grow-1">

                        <div class="alert-icon danger-icon mr-3">
                            <i class="fas fa-user-clock"></i>
                        </div>

                        <div>
                            <div class="font-weight-bold text-dark">
                                {{ $pendingCount }} Applications Pending
                            </div>
                            <small class="text-muted">
                                Waiting for review
                            </small>
                        </div>

                    </div>

                    <a href="{{ route('job_application') }}" class="btn btn-sm premium-btn-danger ml-3">
                        Review
                    </a>

                </div>
                @endif


                {{-- All Clear --}}
                @if ($expiringJobs == 0 && $pendingCount == 0)
                <div class="text-center py-5">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="font-weight-bold text-success">
                        All systems running smoothly
                    </div>
                    <small class="text-muted">
                        No urgent alerts at the moment
                    </small>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Graph + Recent column row -->
<div class="row align-items-stretch">

    <!-- GRAPH CARD -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-4">

                <div class="mb-4">
                    <h3 class="font-weight-bold mb-1">Application Status Overview</h3>
                    <small class="text-muted">Real-time breakdown of applications</small>
                </div>

                <div class="row align-items-center">

                    <!-- CHART -->
                    <div class="col-md-6 d-flex justify-content-center align-items-center">

                        <div class="chart-wrapper">

                            <canvas id="applicationStatusChart"></canvas>

                            <!-- Center Content -->
                            <div class="chart-center-content">
                                <div class="chart-total" id="totalCounter">
                                    {{ $totalApplications }}
                                </div>
                                <div class="chart-label">
                                    Total Applications
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- STATUS CARDS -->
                    <div class="col-md-6">

                        @php
                        $total = max($totalApplications, 1);
                        $statuses = [
                        [
                        'label' => 'Pending',
                        'count' => $pendingCount,
                        'color' => 'warning',
                        'icon' => 'hourglass-half',
                        ],
                        [
                        'label' => 'Shortlisted',
                        'count' => $shortlistedCount,
                        'color' => 'info',
                        'icon' => 'user-check',
                        ],
                        [
                        'label' => 'Hired',
                        'count' => $hiredCount,
                        'color' => 'success',
                        'icon' => 'check-circle',
                        ],
                        [
                        'label' => 'Rejected',
                        'count' => $rejectedCount,
                        'color' => 'danger',
                        'icon' => 'times-circle',
                        ],
                        ];
                        @endphp

                        @foreach ($statuses as $status)
                        @php
                        $percentage = round(($status['count'] / $total) * 100);
                        @endphp

                        <div class="status-card mb-3 p-3 rounded">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-{{ $status['color'] }}">
                                        <i class="fa fa-{{ $status['icon'] }}"></i>
                                    </div>
                                    <div class="ml-2 font-weight-bold">
                                        {{ $status['label'] }}
                                    </div>
                                </div>

                                <div>
                                    <span class="badge badge-{{ $status['color'] }}">
                                        {{ $percentage }}%
                                    </span>
                                </div>

                            </div>

                            <div class="small text-muted mb-1">
                                {{ $status['count'] }} Applications
                            </div>

                            <div class="progress u-h-6px">
                                <div class="progress-bar bg-{{ $status['color'] }}" style="width: {{ $percentage }}%">
                                </div>
                            </div>

                        </div>
                        @endforeach

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- RECENT JOBS CARD -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 rounded-lg">

            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-dark">Recent Posted Jobs</h6>
            </div>

            <div class="card-body pt-0">

                @forelse ($recentJobs as $job)
                <div class="d-flex align-items-center p-3 mb-2 bg-light rounded job-box u-trans-0-2s-bl-4px-solid-tran">
                    <div class="mr-3" style="flex-shrink: 0;">
                        <img src="{{ $job->job_image ? Storage::url($job->job_image) : asset('default/logo.png') }}"
                            width="50" height="50" class="d-block"
                            style="border-radius:8px; object-fit:cover; width:50px; height:50px;" alt="{{ $job->title ?? 'Job' }}">
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="font-weight-bold text-dark u-fs-0-933rem text-truncate">
                            {{ $job->title }}
                        </div>

                        <div class="text-muted u-fs-0-8rem text-truncate">
                            {{ ucfirst($job->type) }} • {{ $job->location }}
                        </div>
                    </div>

                    <div class="text-muted ml-3" style="flex-shrink: 0;">
                        <a href="{{ route('admin.job_edit', $job->id) }} ">
                            <i class="fas fa-chevron-right"></i></a>
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

{{-- ================= GROWTH SNAPSHOT ================= --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="sa-growth-card sa-growth-card--applications">
            <div class="sa-growth-top">
                <div class="sa-growth-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="sa-growth-badge {{ $applicationsGrowth >= 0 ? 'sa-growth-badge--up' : 'sa-growth-badge--down' }}">
                    <i class="fas {{ $applicationsGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ abs($applicationsGrowth) }}%
                </span>
            </div>
            <div class="sa-growth-value">{{ $applicationsThisMonth }}</div>
            <div class="sa-growth-label">Applications This Month</div>
            <div class="sa-growth-compare">vs <strong>{{ $applicationsLastMonth }}</strong> last month</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="sa-growth-card sa-growth-card--jobs">
            <div class="sa-growth-top">
                <div class="sa-growth-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="sa-growth-badge {{ $jobsGrowth >= 0 ? 'sa-growth-badge--up' : 'sa-growth-badge--down' }}">
                    <i class="fas {{ $jobsGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ abs($jobsGrowth) }}%
                </span>
            </div>
            <div class="sa-growth-value">{{ $jobsThisMonth }}</div>
            <div class="sa-growth-label">Jobs Posted This Month</div>
            <div class="sa-growth-compare">vs <strong>{{ $jobsLastMonth }}</strong> last month</div>
        </div>
    </div>
</div>

{{-- ================= APPLICATIONS TREND (30 days) ================= --}}
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-chart-line text-primary mr-2"></i> Applications Trend (Last 30 Days)
                </h6>
                <div style="height: 240px;">
                    <canvas id="applicationsTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-building text-primary mr-2"></i> Company Profile
                </h6>

                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Profile Completeness</small>
                    <small class="font-weight-bold">{{ $companyProfileCompletion }}%</small>
                </div>
                <div class="progress u-h-8px mb-3">
                    <div class="progress-bar bg-primary" style="width: {{ $companyProfileCompletion }}%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Verification</small>
                    @if ($admin->is_verified ?? false)
                    <span class="badge badge-success px-3 py-1 rounded-pill">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>
                    @else
                    <span class="badge badge-warning px-3 py-1 rounded-pill">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                    @endif
                </div>

                @if ($companyProfileCompletion < 100)
                <a href="{{ route('admin.profile') }}" class="btn btn-sm btn-outline-primary btn-block mt-3 rounded-pill">
                    Complete Your Profile
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= HIRING FUNNEL + JOB PERFORMANCE ================= --}}
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-filter text-primary mr-2"></i> Hiring Funnel
                </h6>

                @php
                $funnelSteps = [
                    ['label' => 'Applied', 'count' => $funnel['applied'], 'color' => 'secondary'],
                    ['label' => 'Shortlisted', 'count' => $funnel['shortlisted'], 'color' => 'info'],
                    ['label' => 'Interviewed', 'count' => $funnel['interviewed'], 'color' => 'warning'],
                    ['label' => 'Hired', 'count' => $funnel['hired'], 'color' => 'success'],
                ];
                $funnelBase = $funnel['applied'] > 0 ? $funnel['applied'] : 1;
                @endphp

                @foreach ($funnelSteps as $step)
                @php $pct = round(($step['count'] / $funnelBase) * 100); @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="font-weight-semibold">{{ $step['label'] }}</small>
                        <small class="text-muted">{{ $step['count'] }} ({{ $pct }}%)</small>
                    </div>
                    <div class="progress u-h-8px">
                        <div class="progress-bar bg-{{ $step['color'] }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-chart-bar text-primary mr-2"></i> Job-wise Applications
                </h6>
                @if ($jobPerformance->count() > 0)
                <div style="height: 220px;">
                    <canvas id="jobPerformanceChart"></canvas>
                </div>
                @else
                <p class="text-muted mb-0">No jobs posted yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= CANDIDATE PIPELINE ================= --}}
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-users text-primary mr-2"></i> Open to Work
                </h6>
                <h3 class="font-weight-bold mb-1">{{ $openToWorkCount }}</h3>
                <small class="text-muted d-block mb-3">Candidates actively looking</small>
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-sm btn-primary rounded-pill">
                    Browse Candidates
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-paper-plane text-primary mr-2"></i> Job Invites Sent
                </h6>
                <h3 class="font-weight-bold mb-1">{{ $invitesSent }}</h3>
                <small class="text-muted d-block">
                    {{ $invitesResponded }} responded
                    ({{ $invitesSent > 0 ? round(($invitesResponded / $invitesSent) * 100) : 0 }}% response rate)
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-hourglass-half text-primary mr-2"></i> Avg. Time to Hire
                </h6>
                <h3 class="font-weight-bold mb-1">
                    {{ $avgTimeToHireDays !== null ? $avgTimeToHireDays.' days' : '—' }}
                </h3>
                <small class="text-muted d-block">From application to hired</small>
            </div>
        </div>
    </div>
</div>

{{-- ================= UPCOMING INTERVIEWS ================= --}}
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fas fa-calendar-check text-primary mr-2"></i> Upcoming Interviews
                </h6>
            </div>
            <div class="card-body pt-0">
                @forelse ($upcomingInterviews as $interview)
                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                    <div style="min-width: 0;">
                        <div class="font-weight-bold text-dark u-fs-0-933rem text-truncate">
                            {{ $interview->application->user->name ?? 'Candidate' }}
                        </div>
                        <small class="text-muted text-truncate d-block">
                            {{ $interview->application->job->title ?? 'Job' }}
                        </small>
                    </div>
                    <span class="badge badge-light border text-nowrap ml-2">
                        {{ $interview->formatted_date_time }}
                    </span>
                </div>
                @empty
                <p class="text-muted mb-0">No interviews scheduled.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i> Jobs Needing Attention
                </h6>
            </div>
            <div class="card-body pt-0">
                @forelse ($expiringJobsList as $job)
                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                    <span class="text-truncate">{{ $job->title }}</span>
                    <span class="badge badge-warning text-nowrap ml-2">
                        Expires {{ $job->last_date->diffForHumans() }}
                    </span>
                </div>
                @empty
                @endforelse

                @forelse ($zeroApplicationJobs as $job)
                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                    <span class="text-truncate">{{ $job->title }}</span>
                    <span class="badge badge-secondary text-nowrap ml-2">0 applications</span>
                </div>
                @empty
                @if ($expiringJobsList->count() === 0)
                <p class="text-muted mb-0">All jobs are healthy — nothing needs attention right now.</p>
                @endif
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ================= SALARY INSIGHT + ACTIVITY/SECURITY ================= --}}
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-4">
                    <i class="fas fa-rupee-sign text-primary mr-2"></i> Your Salary vs Market Average
                </h6>

                @forelse ($salaryComparison as $row)
                @php
                    $maxVal = max($row['your_avg'], $row['market_avg'] ?? 0, 1);
                    $youPct = round(($row['your_avg'] / $maxVal) * 100);
                    $marketPct = $row['market_avg'] ? round(($row['market_avg'] / $maxVal) * 100) : 0;

                    $diffPct = null;
                    if ($row['market_avg']) {
                        $diffPct = $row['market_avg'] > 0
                            ? round((($row['your_avg'] - $row['market_avg']) / $row['market_avg']) * 100)
                            : null;
                    }

                    // Raw values are stored in rupees — convert to LPA (Lakhs Per Annum)
                    // for display, dropping the decimal when it's a whole number.
                    $yourAvgLpa = $row['your_avg'] / 100000;
                    $yourAvgLabel = number_format($yourAvgLpa, ($yourAvgLpa == floor($yourAvgLpa)) ? 0 : 1);

                    if ($row['market_avg']) {
                        $marketAvgLpa = $row['market_avg'] / 100000;
                        $marketAvgLabel = number_format($marketAvgLpa, ($marketAvgLpa == floor($marketAvgLpa)) ? 0 : 1);
                    }
                @endphp
                <div class="sa-salary-row">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="sa-salary-role">{{ $row['role'] }}</span>
                        @if (! is_null($diffPct))
                        <span class="sa-salary-diff-badge {{ $diffPct >= 0 ? 'sa-salary-diff-badge--up' : 'sa-salary-diff-badge--down' }}">
                            <i class="fas {{ $diffPct >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($diffPct) }}% {{ $diffPct >= 0 ? 'above' : 'below' }} market
                        </span>
                        @endif
                    </div>

                    <div class="sa-salary-bar-line">
                        <span class="sa-salary-bar-tag">You</span>
                        <div class="sa-salary-bar-track">
                            <div class="sa-salary-bar-fill sa-salary-bar-fill--you" style="width: {{ $youPct }}%;"></div>
                        </div>
                        <span class="sa-salary-bar-value">₹{{ $yourAvgLabel }} LPA</span>
                    </div>

                    @if ($row['market_avg'])
                    <div class="sa-salary-bar-line">
                        <span class="sa-salary-bar-tag">Market</span>
                        <div class="sa-salary-bar-track">
                            <div class="sa-salary-bar-fill sa-salary-bar-fill--market" style="width: {{ $marketPct }}%;"></div>
                        </div>
                        <span class="sa-salary-bar-value">₹{{ $marketAvgLabel }} LPA</span>
                    </div>
                    @else
                    <div class="sa-salary-no-market">No market data available for this role yet.</div>
                    @endif
                </div>
                @empty
                <p class="text-muted mb-0">Add salary ranges to your job posts to see this comparison.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fas fa-history text-primary mr-2"></i> Recent Activity
                </h6>
                <small class="text-muted">{{ $activeSessionsCount }} active session(s)</small>
            </div>
            <div class="card-body pt-0">
                @forelse ($recentActivity as $log)
                <div class="d-flex align-items-start p-2 mb-2 bg-light rounded">
                    <i class="fas fa-circle text-primary mt-2 mr-2" style="font-size: 0.5rem;"></i>
                    <div>
                        <small class="d-block">{{ $log->description }}</small>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

            var ctx = document.getElementById('applicationStatusChart');

            if (ctx) {

                var dataValues = [
                    {{ $pendingCount ?? 0 }},
                    {{ $shortlistedCount ?? 0 }},
                    {{ $hiredCount ?? 0 }},
                    {{ $rejectedCount ?? 0 }}
                ];

                var totalApplications = dataValues.reduce((a, b) => a + b, 0);
                var isEmpty = totalApplications === 0;

                if (isEmpty) {
                    dataValues = [1];
                }

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: dataValues,
                            backgroundColor: isEmpty ? ['#e9ecef'] : ['#f6c23e', '#36b9cc',
                                '#1cc88a', '#e74a3b'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 78,
                        legend: {
                            display: false
                        },
                        tooltips: {
                            enabled: false
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1200
                        }
                    }
                });

                /* ===== Center Counter Animation ===== */

                var counter = document.getElementById("totalCounter");

                if (counter) {
                    var target = totalApplications;
                    var count = 0;
                    var step = Math.ceil(target / 30);

                    var update = setInterval(function() {
                        count += step;

                        if (count >= target) {
                            counter.innerText = target;
                            clearInterval(update);
                        } else {
                            counter.innerText = count;
                        }

                    }, 20);
                }
            }


            var activePercent = {{ $activePercentage ?? 0 }};
            var bar = document.getElementById("activeProgressBar");
            var text = document.getElementById("activePercentText");

            if (bar && text) {

                setTimeout(function() {
                    bar.style.width = activePercent + "%";
                }, 300);

                var count = 0;

                var interval = setInterval(function() {
                    count++;

                    if (count >= activePercent) {
                        text.innerText = activePercent;
                        clearInterval(interval);
                    } else {
                        text.innerText = count;
                    }

                }, 20);
            }

            /* ===== Applications Trend (30-day line chart) ===== */
            var trendCtx = document.getElementById('applicationsTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendLabels ?? []) !!},
                        datasets: [{
                            label: 'Applications',
                            data: {!! json_encode($trendData ?? []) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            xAxes: [{ ticks: { maxTicksLimit: 8 }, gridLines: { display: false } }],
                            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                        }
                    }
                });
            }

            /* ===== Job-wise Applications (bar chart) ===== */
            var jobPerfCtx = document.getElementById('jobPerformanceChart');
            if (jobPerfCtx) {
                new Chart(jobPerfCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($jobPerformance->pluck('title')->map(fn($t) => \Illuminate\Support\Str::limit($t, 18))) !!},
                        datasets: [{
                            label: 'Applications',
                            data: {!! json_encode($jobPerformance->pluck('applications_count')) !!},
                            backgroundColor: '#2563eb',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            xAxes: [{ gridLines: { display: false } }],
                            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                        }
                    }
                });
            }

        });
</script>
@endpush