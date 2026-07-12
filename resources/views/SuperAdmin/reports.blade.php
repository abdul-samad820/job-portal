@extends('layouts.superadmin')
@section('title', 'Reports & Analytics')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-chart-bar',
        'title' => 'Reports & Analytics',
        'subtitle' => 'Platform-wide trends across the last 30 days.',
    ])

    {{-- Summary cards — same stat-pill treatment as the Dashboard, for a consistent look --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable h-100">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($summary['total_jobs']) }}</div>
                        <div class="stat-label">Total Jobs</div>
                        <small class="sa-report-delta"><i class="fas fa-arrow-up"></i> {{ $summary['jobs_last_30_days'] }} in 30 days</small>
                    </div>
                    <div class="stat-round-icon"><i class="fas fa-briefcase"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable h-100">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($summary['total_users']) }}</div>
                        <div class="stat-label">Total Users</div>
                        <small class="sa-report-delta"><i class="fas fa-arrow-up"></i> {{ $summary['users_last_30_days'] }} in 30 days</small>
                    </div>
                    <div class="stat-round-icon"><i class="fas fa-user"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable h-100">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($summary['total_companies']) }}</div>
                        <div class="stat-label">Total Companies</div>
                    </div>
                    <div class="stat-round-icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable h-100">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($summary['total_applications']) }}</div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-round-icon"><i class="fas fa-file-alt"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trend charts --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100 sa-report-card">
                <div class="card-body">
                    <div class="sa-report-card-header">
                        <span class="sa-report-icon sa-report-icon-blue"><i class="fas fa-chart-line"></i></span>
                        <div>
                            <h6 class="mb-0">Jobs Posted vs Applications</h6>
                            <small class="text-muted">Last 30 days</small>
                        </div>
                    </div>
                    <div class="sa-chart-box" style="height: 320px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            @php
                $sa_statusColors = [
                    'pending'     => '#f6c23e',
                    'shortlisted' => '#2563eb',
                    'rejected'    => '#e74a3b',
                    'hired'       => '#16a34a',
                ];
                $sa_statusTotal = $applicationStatusBreakdown->sum();
            @endphp
            <div class="card border-0 shadow-sm h-100 sa-report-card">
                <div class="card-body d-flex flex-column">
                    <div class="sa-report-card-header">
                        <span class="sa-report-icon sa-report-icon-green"><i class="fas fa-chart-pie"></i></span>
                        <div>
                            <h6 class="mb-0">Application Status</h6>
                            <small class="text-muted">All-time breakdown</small>
                        </div>
                    </div>

                    <div class="sa-chart-box" style="height: 190px;">
                        <canvas id="statusChart"></canvas>
                    </div>

                    <div class="sa-status-list mt-2">
                        @foreach($applicationStatusBreakdown as $sa_label => $sa_count)
                            @php
                                $sa_pct = $sa_statusTotal > 0 ? round($sa_count / $sa_statusTotal * 100) : 0;
                                $sa_color = $sa_statusColors[strtolower($sa_label)] ?? '#94a3b8';
                            @endphp
                            <div class="sa-status-row">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="d-flex align-items-center">
                                        <span class="sa-status-dot mr-2" style="background: {{ $sa_color }};"></span>
                                        <span class="sa-status-label text-capitalize">{{ $sa_label }}</span>
                                    </span>
                                    <span>
                                        <strong>{{ number_format($sa_count) }}</strong>
                                        <small class="text-muted">({{ $sa_pct }}%)</small>
                                    </span>
                                </div>
                                <div class="sa-status-bar">
                                    <div class="sa-status-bar-fill" style="width: {{ $sa_pct }}%; background: {{ $sa_color }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100 sa-report-card">
                <div class="card-body">
                    <div class="sa-report-card-header">
                        <span class="sa-report-icon sa-report-icon-blue"><i class="fas fa-layer-group"></i></span>
                        <div>
                            <h6 class="mb-0">Top Job Categories</h6>
                            <small class="text-muted">By number of jobs posted</small>
                        </div>
                    </div>
                    <div class="sa-chart-box" style="height: 280px;">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100 sa-report-card">
                <div class="card-body">
                    <div class="sa-report-card-header">
                        <span class="sa-report-icon sa-report-icon-green"><i class="fas fa-building"></i></span>
                        <div>
                            <h6 class="mb-0">Top Companies</h6>
                            <small class="text-muted">By jobs posted</small>
                        </div>
                    </div>
                    <div class="sa-chart-box" style="height: 280px;">
                        <canvas id="companiesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* Small delta line under the stat number, e.g. "+12 in 30 days" */
    body.sa-shell .sa-report-delta {
        display: inline-flex; align-items: center; gap: 4px;
        color: var(--success, #16a34a); font-weight: 600; font-size: 11px;
    }

    /* Chart card header — icon chip + title, consistent across all 4 chart cards */
    body.sa-shell .sa-report-card-header {
        display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
    }
    body.sa-shell .sa-report-card-header h6 {
        font-weight: 700; color: var(--text-primary, #1a1f36); font-size: 14.5px;
    }
    body.sa-shell .sa-report-icon {
        width: 38px; height: 38px; border-radius: var(--sa-radius-sm, 8px);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    body.sa-shell .sa-report-icon-blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    body.sa-shell .sa-report-icon-green { background: rgba(28, 200, 138, 0.12); color: #16a34a; }

    /* Slightly taller/breathier cards so charts don't feel cramped */
    body.sa-shell .sa-report-card .card-body { padding: 22px; }

    /* Same subtle lift-on-hover the stat-pill cards already use, for consistency */
    body.sa-shell .sa-report-card {
        transition: box-shadow .15s ease, transform .15s ease;
    }
    body.sa-shell .sa-report-card:hover {
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08) !important;
        transform: translateY(-2px);
    }


    /* Fixed-height wrapper for every chart canvas.
       Chart.js needs a parent with an explicit, non-content-based height
       when maintainAspectRatio:false is used — otherwise the canvas and
       its parent keep growing each other in a resize loop, and the chart
       visibly "grows"/shifts down the page. position:relative is required
       so Chart.js can correctly measure and pin the canvas inside it. */
    body.sa-shell .sa-chart-box {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    body.sa-shell .sa-chart-box canvas {
        position: absolute;
        top: 0; left: 0;
        width: 100% !important;
        height: 100% !important;
    }

    /* Application Status breakdown list — color dot + label + count/% + mini bar */
    body.sa-shell .sa-status-row + .sa-status-row { margin-top: 10px; }
    body.sa-shell .sa-status-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
    body.sa-shell .sa-status-label {
        font-size: 13px; font-weight: 600; color: var(--text-primary, #1a1f36);
    }
    body.sa-shell .sa-status-row strong { font-size: 13px; color: var(--text-primary, #1a1f36); }
    body.sa-shell .sa-status-row small { font-size: 11px; }
    body.sa-shell .sa-status-bar {
        height: 5px; border-radius: 3px; background: #f1f5f9; overflow: hidden;
    }
    body.sa-shell .sa-status-bar-fill {
        height: 100%; border-radius: 3px; transition: width .3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Shared look & feel, set once so every chart matches the dashboard ----
    Chart.defaults.global.defaultFontFamily = "'Source Sans Pro', -apple-system, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.global.defaultFontColor = '#6b7280';
    Chart.defaults.global.defaultFontSize = 12;
    Chart.defaults.global.tooltips.backgroundColor = '#1a1f36';
    Chart.defaults.global.tooltips.titleFontSize = 12;
    Chart.defaults.global.tooltips.titleFontStyle = 'bold';
    Chart.defaults.global.tooltips.bodyFontSize = 12;
    Chart.defaults.global.tooltips.bodySpacing = 4;
    Chart.defaults.global.tooltips.xPadding = 10;
    Chart.defaults.global.tooltips.yPadding = 8;
    Chart.defaults.global.tooltips.cornerRadius = 6;
    Chart.defaults.global.tooltips.displayColors = true;
    Chart.defaults.global.tooltips.caretSize = 6;

    // Helper: soft top-to-bottom gradient fill for line/area charts
    function fade(ctx, hex, alphaTop) {
        const g = ctx.createLinearGradient(0, 0, 0, 280);
        g.addColorStop(0, hex + alphaTop);
        g.addColorStop(1, hex + '00');
        return g;
    }

    const labels = @json($jobsTrend->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
    const trendCtx = document.getElementById('trendChart').getContext('2d');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Jobs Posted',
                    data: @json($jobsTrend->values()),
                    borderColor: '#2563eb',
                    backgroundColor: fade(trendCtx, '#2563eb', '26'),
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBorderWidth: 2,
                    pointBackgroundColor: '#2563eb',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb',
                    tension: 0.4,
                },
                {
                    label: 'Applications',
                    data: @json($applicationsTrend->values()),
                    borderColor: '#16a34a',
                    backgroundColor: fade(trendCtx, '#16a34a', '26'),
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBorderWidth: 2,
                    pointBackgroundColor: '#16a34a',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#16a34a',
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 16 } },
            tooltips: { mode: 'index', intersect: false },
            hover: { mode: 'index', intersect: false },
            scales: {
                yAxes: [{ ticks: { beginAtZero: true, precision: 0, padding: 8 }, gridLines: { color: '#f1f5f9', zeroLineColor: '#f1f5f9' } }],
                xAxes: [{ gridLines: { display: false }, ticks: { autoSkip: true, maxTicksLimit: 10, maxRotation: 0 } }]
            }
        }
    });

    const statusData = @json($applicationStatusBreakdown->values());
    const statusLabels = @json($applicationStatusBreakdown->keys());
    const statusColorMap = { pending: '#f6c23e', shortlisted: '#2563eb', rejected: '#e74a3b', hired: '#16a34a' };
    const statusColors = statusLabels.map(l => statusColorMap[l.toLowerCase()] || '#94a3b8');
    const statusTotal = statusData.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors,
                hoverBackgroundColor: statusColors,
                hoverBorderColor: '#fff',
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 72,
            legend: { display: false },
            tooltips: { callbacks: {
                label: function (tt, data) {
                    const label = data.labels[tt.index];
                    const val = data.datasets[0].data[tt.index];
                    const pct = statusTotal > 0 ? Math.round(val / statusTotal * 100) : 0;
                    return label + ': ' + val + ' (' + pct + '%)';
                }
            } }
        },
        plugins: [{
            afterDraw: function (chart) {
                const w = chart.chartArea.right - chart.chartArea.left;
                const h = chart.chartArea.bottom - chart.chartArea.top;
                const cx = chart.chartArea.left + w / 2;
                const cy = chart.chartArea.top + h / 2;
                const c = chart.chart.ctx;
                c.save();
                c.textAlign = 'center';
                c.textBaseline = 'middle';
                c.fillStyle = '#1a1f36';
                c.font = "bold 22px 'Source Sans Pro', sans-serif";
                c.fillText(statusTotal, cx, cy - 8);
                c.fillStyle = '#9ca3af';
                c.font = "11px 'Source Sans Pro', sans-serif";
                c.fillText('Applications', cx, cy + 14);
                c.restore();
            }
        }]
    });

    const catCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: @json($topCategories->pluck('name')),
            datasets: [{
                label: 'Jobs',
                data: @json($topCategories->pluck('jobs_count')),
                backgroundColor: fade(catCtx, '#2563eb', 'ff'),
                hoverBackgroundColor: '#1d4ed8',
                borderRadius: 6,
                maxBarThickness: 38,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: { mode: 'index', intersect: false },
            scales: {
                yAxes: [{ ticks: { beginAtZero: true, precision: 0, padding: 8 }, gridLines: { color: '#f1f5f9', zeroLineColor: '#f1f5f9' } }],
                xAxes: [{ gridLines: { display: false }, ticks: {
                    callback: function (label) { return label.length > 12 ? label.substring(0, 11) + '…' : label; }
                } }]
            }
        }
    });

    const compCtx = document.getElementById('companiesChart').getContext('2d');
    new Chart(compCtx, {
        type: 'bar',
        data: {
            labels: @json($topCompanies->pluck('company_name')),
            datasets: [{
                label: 'Jobs',
                data: @json($topCompanies->pluck('jobs_count')),
                backgroundColor: fade(compCtx, '#16a34a', 'ff'),
                hoverBackgroundColor: '#15803d',
                borderRadius: 6,
                maxBarThickness: 38,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: { mode: 'index', intersect: false },
            scales: {
                yAxes: [{ ticks: { beginAtZero: true, precision: 0, padding: 8 }, gridLines: { color: '#f1f5f9', zeroLineColor: '#f1f5f9' } }],
                xAxes: [{ gridLines: { display: false }, ticks: {
                    callback: function (label) { return label.length > 12 ? label.substring(0, 11) + '…' : label; }
                } }]
            }
        }
    });
});
</script>
@endpush