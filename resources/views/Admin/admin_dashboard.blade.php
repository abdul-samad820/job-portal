@extends('layouts.index')
@section('title', 'Dashboard')
@section('content')


    <div class="row">
        <!-- Total Visitors -->
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <div class="stat-value mb-1"></div>
                        <div class="stat-label">samad</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-user"></i>
                    </div>

                </div>
            </div>
        </div>

        <!-- Shortlisted -->
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <a href="{{route('admin.selectedList')}}">
                        <div class="stat-value mb-1">{{ $totalshortlisted }}</div>
                        <div class="stat-label">Shortlisted</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
</a>
                </div>
            </div>
        </div>

        <!-- Views -->
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <div class="stat-value mb-1">{{ $totalSelected }}</div>
                        <div class="stat-label">Total Selected</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-eye"></i>
                    </div>

                </div>
            </div>
        </div>

        <!-- Applied Job -->
        <div class="col-md-3 mb-3">
            <div class="card stat-pill">
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>
                        <div class="stat-value mb-1">{{ $totalApplications }}</div>
                        <div class="stat-label">Applied Job</div>
                    </div>

                    <div class="stat-round-icon">
                        <i class="fas fa-pen"></i>
                    </div>

                </div>
            </div>
        </div>

    </div>



    <!-- Graph + Recent column row -->
    <div class="row align-items-stretch">

        <!-- GRAPH CARD -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-dark font-weight-bold">Profile Views</h5>

                        <!-- Pills -->
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light border active" data-range="Day">
                                <input type="radio" checked> Day
                            </label>

                            <label class="btn btn-light border" data-range="Week">
                                <input type="radio"> Week
                            </label>

                            <label class="btn btn-light border" data-range="Month">
                                <input type="radio"> Month
                            </label>
                        </div>

                    </div>

                    <hr class="my-2" style="border-color:#f1f3f5;">

                    <div style="position: relative; height: 280px;">
                        <canvas id="jobStatusChart"></canvas>
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



    <!-- Additional content area (cards grid) -->
    {{-- <div class="row align-items-stretch">

        <!-- About Company -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body">

                    <h6 class="card-title mb-2">About Company</h6>

                    <p class="muted mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        This company belongs to freshers. Manage company details from profile.
                    </p>

                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-body">

                    <h6 class="card-title mb-2">Quick Actions</h6>

                    <div class="d-flex flex-wrap">
                        <button class="btn btn-primary btn-sm mr-2 mb-2">
                            <i class="fas fa-briefcase mr-1"></i> Post Job
                        </button>

                        <button class="btn btn-outline-primary btn-sm mr-2 mb-2">
                            <i class="fas fa-users mr-1"></i> View Candidates
                        </button>

                        <button class="btn btn-outline-primary btn-sm mr-2 mb-2">
                            <i class="fas fa-file-alt mr-1"></i> Reports
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div> --}}

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script>
        $(function() {

            var canvas = document.getElementById('jobStatusChart');
            if (!canvas) return;

            canvas.style.display = 'block';
            canvas.parentNode.style.minHeight = '260px';

            var ctx = canvas.getContext('2d');

            // Gradient fill
            var gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, "rgba(47,128,237,0.25)");
            gradient.addColorStop(1, "rgba(47,128,237,0.03)");

            // Default = DAY
            var labels = @json($dayLabels);
            var values = @json($dayValues);

            var chartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "",
                        data: values,
                        backgroundColor: gradient,
                        borderColor: "#2f80ed",
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: "#2f80ed",
                        fill: true,
                        lineTension: 0.45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },

                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontColor: "#7d8b97"
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontColor: "#7d8b97"
                            },
                            gridLines: {
                                color: "rgba(0,0,0,0.07)",
                                borderDash: [4, 4]
                            }
                        }]
                    }
                }
            });

            // -----------------------------
            //   TOGGLE BUTTON CLICK
            // -----------------------------
            $(".btn-group-toggle label").click(function() {

                $(".btn-group-toggle label").removeClass("active btn-primary")
                $(this).addClass("active btn-primary")

                var range = $(this).data("range");

                if (range === "Day") {
                    chartInstance.data.labels = @json($dayLabels);
                    chartInstance.data.datasets[0].data = @json($dayValues);
                }

                if (range === "Week") {
                    chartInstance.data.labels = @json($weekLabels);
                    chartInstance.data.datasets[0].data = @json($weekValues);
                }

                if (range === "Month") {
                    chartInstance.data.labels = @json($monthLabels);
                    chartInstance.data.datasets[0].data = @json($monthValues);
                }

                chartInstance.update();
            });

        });
    </script>
@endpush
