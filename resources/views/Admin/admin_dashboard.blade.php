@extends('layouts.index')
@section('title', 'Dashboard')
@section('content')
    <style>
        .chart-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .total-count {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }

        .status-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafc 100%);
            border: 1px solid #f1f3f5;
            transition: all .25s ease;
        }

        .status-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
        }

        .progress-bar {
            transition: width 0.8s ease;
        }

        .badge-warning {
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
        }

        .stat-hover {
            transition: all .25s ease;
        }

        .stat-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .premium-progress {
            height: 10px;
            border-radius: 50px;
            background: #e9ecef;
            overflow: hidden;
        }

        .premium-bar {
            width: 0%;
            border-radius: 50px;
            background: linear-gradient(90deg, #007bff, #00c6ff);
            position: relative;
            transition: width 1.5s ease-in-out;
        }

        /* Shine Animation */
        .premium-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 50%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            transform: skewX(-25deg);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% {
                left: -50%;
            }

            100% {
                left: 150%;
            }
        }

        .chart-wrapper {
            position: relative;
            width: 260px;
            height: 260px;
            margin: auto;
        }

        .chart-center-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-total {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .chart-label {
            font-size: 13px;
            color: #6c757d;
            letter-spacing: 0.5px;
        }

        /* Smooth animation for number */
        .chart-total {
            transition: all 0.4s ease;
        }
    </style>

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
                        <a href="{{ route('admin.selectedList') }}">
                            <div class="stat-value mb-1">{{ $activeJobs }}</div>
                            <div class="stat-label">Active Jobs</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    </a>
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

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <!-- Left Content -->
                        <div>
                            <div class="small text-uppercase text-muted font-weight-bold mb-1">
                                Top Performing Job
                            </div>

                            <h5 class="font-weight-bold text-dark mb-1">
                                {{ $topJob->title ?? 'No Data Available' }}
                            </h5>

                            @if ($topJob)
                                <small class="text-muted">
                                    {{ ucfirst($topJob->type ?? '') }}
                                    @if (!empty($topJob->location))
                                        • {{ $topJob->location }}
                                    @endif
                                </small>
                            @endif
                        </div>

                        <!-- Right Counter -->
                        <div class="text-right">
                            <div class="top-count text-danger">
                                {{ $topJob->applications_count ?? 0 }}
                            </div>
                            <small class="text-muted text-uppercase">
                                Applications
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT : SYSTEM ALERTS ================= -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm premium-alert-card h-100">

                <div class="card-body p-4">

                    <h6 class="font-weight-bold mb-4">
                        System Alerts
                    </h6>

                    @if ($expiringJobs > 0)
                        <div class="alert-box alert-warning-box mb-3">
                            <div class="d-flex align-items-center">

                                <div class="alert-icon bg-warning">
                                    <i class="fa fa-clock"></i>
                                </div>

                                <div class="ml-3 flex-grow-1">
                                    <div class="font-weight-bold">
                                        {{ $expiringJobs }} Jobs Expiring
                                    </div>
                                    <small class="text-muted">
                                        Expiring in next 3 days
                                    </small>
                                </div>

                                <span class="badge badge-warning">
                                    Action
                                </span>

                            </div>
                        </div>
                    @endif


                    @if ($pendingApplications > 0)
                        <div class="alert-box alert-danger-box mb-3">
                            <div class="d-flex align-items-center">

                                <div class="alert-icon bg-danger">
                                    <i class="fa fa-exclamation-circle"></i>
                                </div>

                                <div class="ml-3 flex-grow-1">
                                    <div class="font-weight-bold">
                                        {{ $pendingApplications }} Applications Pending
                                    </div>
                                    <small class="text-muted">
                                        Waiting for review
                                    </small>
                                </div>

                                <span class="badge badge-danger">
                                    Review
                                </span>

                            </div>
                        </div>
                    @endif


                    @if ($expiringJobs == 0 && $pendingApplications == 0)
                        <div class="text-success small d-flex align-items-center">
                            <i class="fa fa-check-circle mr-2"></i>
                            All systems running smoothly.
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
                                <img src="{{ $job->job_image ? asset('uploads/job/' . $job->job_image) : asset('default/logo.png') }}"
                                    width="50" height="50" style=" border-radius: 8px;">
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
                            backgroundColor: isEmpty ?
                                ['#e9ecef'] :
                                ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b'],
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
