@extends('layouts.index')
@section('title', 'Dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="stat-value mb-1">{{ $totalJobs }}</div>
                        <div class="stat-label">Total Jobs</div>
                    </div>
                    <div class="stat-round-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="stat-value mb-1">{{ $activeJobs }}</div>
                        <div class="stat-label">Active Jobs</div>
                    </div>
                    <div class="stat-round-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <div class="stat-value mb-1">{{ $expiredJobs }}</div>
                        <div class="stat-label">Expired Jobs</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-eye"></i>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <div class="stat-value mb-1">{{ $totalApplications }}</div>
                        <div class="stat-label">Total Applications</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-pen"></i>
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

    <div class="card shadow-sm border-0 p-3 mb-4">
        <h6 class="font-weight-bold mb-3">Quick Actions</h6>

        <div class="row text-center">

            <div class="col-3">
                <a href="{{ route('admin.job_add') }}" class="btn btn-light w-100">
                    <i class="fa fa-plus text-primary"></i><br>
                    Add Job
                </a>
            </div>

            <div class="col-3">
                <a href="{{ route('job_application') }}" class="btn btn-light w-100">
                    <i class="fa fa-users text-success"></i><br>
                    Applications
                </a>
            </div>

            <div class="col-3">
                <a href="{{ route('admin.job_category') }}" class="btn btn-light w-100">
                    <i class="fa fa-layer-group text-info"></i><br>
                    Categories
                </a>
            </div>

            <div class="col-3">
                <button class="btn btn-light w-100">
                    <i class="fa fa-file-export text-danger"></i><br>
                    Export
                </button>
            </div>

        </div>
    </div>

    <div class="row">

        <!-- ================= LEFT : TOP JOB ================= -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm premium-top-card h-100">

                <div class="card border-0 shadow-sm rounded-4 stat-hover">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <!-- Left Content -->
                            <div>
                                <div class="text-uppercase text-muted fw-semibold small mb-2 letter-spacing">
                                    Top Performing Job
                                </div>

                                <h5 class="fw-bold text-dark mb-2">
                                    {{ $topJob->title ?? 'No Data Available' }}
                                </h5>

                                @if ($topJob)
                                    <div class="text-muted small">
                                        <span class="badge bg-light text-dark border me-2 px-3 py-1 rounded-pill">
                                            {{ ucfirst($topJob->type ?? 'N/A') }}
                                        </span>

                                        @if (!empty($topJob->location))
                                            <span class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $topJob->location }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Right Counter -->
                            <div class="text-end">
                                <div class="display-6 fw-bold text-gradient">
                                    {{ $topJob->applications_count ?? 0 }}
                                </div>
                                <div class="text-muted small text-uppercase fw-semibold">
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
                                    <span class="fw-semibold text-primary">High Demand</span>
                                </div>

                                <div class="premium-progress">
                                    <div class="premium-bar" style="width: 85%;"></div>
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

                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-{{ $status['color'] }}"
                                            style="width: {{ $percentage }}%">
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

                    @foreach ($recentJobs as $job)
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded job-box"
                            style="transition: 0.2s; border-left: 4px solid transparent;">
                          <div class="job-logo mr-3">
    <img src="{{ $job->job_image 
        ? Storage::url($job->job_image) 
        : asset('default/logo.png') }}"
        width="50"
        height="50"
        style="border-radius:8px; object-fit:cover;">
</div>
                            <div class="flex-grow-1">



                                <div class="font-weight-bold text-dark" style="font-size: 14px;">
                                    {{ $job->title }}
                                </div>

                                <div class="text-muted" style="font-size: 12px;">
                                    {{ ucfirst($job->type) }} • {{ $job->location }}
                                </div>
                            </div>

                            <div class="text-muted ml-3">
                                <a href="{{ route('admin.job_edit', $job->id) }} ">
                                    <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    @endforeach

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

        });
    </script>
@endpush
