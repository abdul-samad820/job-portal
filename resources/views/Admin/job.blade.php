@extends('layouts.Admin_layout')
@section('title', 'Job Management')

@push('styles')
<style>
    /* ---- Active job row: crisp, clearly "live" ---- */
    .sa-job-row-active td:first-child {
        border-left: 3px solid #28a745;
    }
    .sa-job-row-active:hover {
        background-color: rgba(40, 167, 69, 0.04);
    }

    /* ---- Expired job row: frosted / whited-out, clearly de-emphasized ---- */
    .sa-job-row-expired td:first-child {
        border-left: 3px solid #dc3545;
    }
    .sa-job-row-expired {
        background: rgba(248, 249, 250, 0.85);
        position: relative;
    }
    .sa-job-row-expired > td {
        color: #9aa1ac !important;
        filter: grayscale(0.6) saturate(0.7);
        opacity: 0.62;
    }
    .sa-job-row-expired img {
        filter: grayscale(1) blur(0.4px);
        opacity: 0.7;
    }
    .sa-job-row-expired .badge {
        opacity: 0.65;
    }
    /* Keep action buttons fully usable even when the rest of the row is muted */
    .sa-job-row-expired > td.sa-job-actions {
        opacity: 1;
        filter: none;
        color: inherit !important;
    }

    .sa-expired-chip {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3px;
        color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.25);
        padding: 1px 8px;
        border-radius: 20px;
        margin-left: 6px;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* ---- Performance column: views / applications / conversion, each on its own separated line ---- */
    .sa-perf-cell {
        min-width: 150px;
    }
    .sa-perf-row {
        padding: 4px 0;
        border-bottom: 1px dashed #e9ecef;
    }
    .sa-perf-row:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-briefcase',
        'title' => 'Job Management',
        'subtitle' => 'View, manage and edit all job postings.',
    ])

    <!-- Jobs Table -->
    <div class="card shadow-sm border-0 rounded">
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <form method="GET" action="{{ route('admin.job') }}" class="form-inline">
                    <input type="search" name="search" class="form-control form-control-sm mr-2" aria-label="Search jobs" placeholder="Search jobs..." value="{{ request('search') }}">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-search"></i>
                    </button>
                </form>

                <a href="{{ route('admin.job_add') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus mr-1"></i> Add Job
                </a>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Job Image</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Salary</th>
                                    <th>Type</th>
                                    <th>Last Date</th>
                                    <th>Performance</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($jobs as $job)
                                @php
                                $lastDate = \Carbon\Carbon::parse($job->last_date);
                                $isExpired = $lastDate->isPast();
                                @endphp

                                <tr class="{{ $isExpired ? 'sa-job-row-expired' : 'sa-job-row-active' }}">

                                    <td>{{ $job->id }}</td>

                                    <!-- job Image -->
                                    <td>
                                        <img src="{{ Storage::url($job->job_image) }}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;" alt="Company logo">
                                    </td>


                                    <td class="font-weight-bold text-dark">
                                        {{ $job->title }}
                                        @if ($isExpired)
                                            <span class="sa-expired-chip">EXPIRED</span>
                                        @endif
                                        <br>
                                        <button class="btn btn-link p-0 mt-1 text-primary small" data-toggle="modal" data-target="#jobModal{{ $job->id }}">
                                            View Details
                                        </button>
                                    </td>

                                    <td>{{ $job->location }}</td>

                                    <td class="font-weight-bold text-success">
                                       {{ number_format($job->min_salary / 100000, 1) }}L - 
                                      {{ number_format($job->max_salary / 100000, 1) }}L
                                    </td>


                                    @php
                                    $typeClasses = [
                                    'Full-time' => 'badge-success',
                                    'Part-time' => 'badge-primary',
                                    'Internship' => 'badge-warning',
                                    'Contract' => 'badge-dark',
                                    ];

                                    $typeIcons = [
                                    'Full-time' => 'fa-briefcase',
                                    'Part-time' => 'fa-clock',
                                    'Internship' => 'fa-user-graduate',
                                    'Contract' => 'fa-file-signature',
                                    ];
                                    @endphp

                                    <td>
                                        <span class="badge {{ $typeClasses[$job->type] ?? 'badge-secondary' }} px-3 py-2 badge-pill">
                                            <i class="fa {{ $typeIcons[$job->type] ?? 'fa-briefcase' }} mr-1"></i>
                                            {{ $job->type }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                            {{ $lastDate->format('d M Y') }}
                                            <small>({{ $isExpired ? 'Expired' : 'Open' }})</small>
                                        </span>
                                    </td>

                                    <td class="small sa-perf-cell">
                                        <div class="sa-perf-row">
                                            <i class="fa fa-eye text-muted mr-1"></i>{{ number_format($job->views_count) }} views
                                        </div>
                                        <div class="sa-perf-row">
                                            <i class="fa fa-file-alt text-muted mr-1"></i>{{ $job->applications_count }} applications
                                        </div>
                                        @if ($job->views_count > 0)
                                        <div class="sa-perf-row text-muted">
                                            {{ round(($job->applications_count / $job->views_count) * 100, 1) }}% conversion
                                        </div>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle sa-job-actions">

                                        <div class="sa-row-actions justify-content-center">

                                            <a href="{{ route('admin.job_edit', $job->id) }}" class="btn btn-primary sa-icon-action" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.job_delete', $job->id) }}" method="POST" class="d-inline-flex">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger sa-icon-action delete-btn" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>

                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                        <h5 class="font-weight-bold text-muted">No jobs yet</h5>
                                        <p class="text-muted mb-3">Post your first job to start receiving applications.</p>
                                        <a href="{{ route('admin.job_add') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Post a Job
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($jobs instanceof \Illuminate\Pagination\AbstractPaginator)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted small">
                            Showing {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of {{ $jobs->total() }}
                        </span>
                        {{ $jobs->links('pagination::bootstrap-4') }}
                    </div>
                    @endif

                </div>
            </div>

    @foreach ($jobs as $job)
    <div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <!-- ================= HEADER ================= -->
                <div class="modal-header text-white u-bg-linear-gradien-2">

                    <div class="d-flex align-items-center">

                        <img src="{{ Storage::url($job->job_image) }}" class="mr-3 shadow" style="width:60px;height:60px;object-fit:cover;border-radius:12px;" alt="Company logo">

                        <div>
                            <h5 class="mb-1 font-weight-bold">
                                {{ $job->title }}
                            </h5>
                            <small>
                                <i class="fa fa-map-marker-alt mr-1"></i>
                                {{ $job->location }}
                            </small>
                        </div>

                    </div>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <!-- ================= BODY ================= -->
                <div class="modal-body px-4 py-4">

                    <!-- INFO CARDS -->
                    <div class="row text-center mb-4">

                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fa fa-briefcase text-primary mb-2"></i>
                                <small class="text-muted d-block">Experience</small>
                                <strong>{{ $job->experience ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fa fa-money-bill-wave text-success mb-2"></i>
                                <small class="text-muted d-block">Salary</small>
                                <strong class="text-success">
                                    @if($job->min_salary && $job->max_salary)
                                    {{ number_format($job->min_salary / 100000, 1) }}L -
                                    {{ number_format($job->max_salary / 100000, 1) }}L
                                    @else
                                    Not Disclosed
                                    @endif
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fa fa-clock text-info mb-2"></i>
                                <small class="text-muted d-block">Job Type</small>
                                <span class="badge badge-info px-3 py-1">
                                    {{ $job->type }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light h-100">
                                <i class="fa fa-calendar-alt text-danger mb-2"></i>
                                <small class="text-muted d-block">Last Date</small>
                                <strong>
                                    {{ \Carbon\Carbon::parse($job->last_date)->format('d M Y') }}
                                </strong>
                            </div>
                        </div>

                    </div>

                    <!-- OVERVIEW -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fa fa-info-circle text-primary mr-1"></i>
                            Job Overview
                        </h6>
                        <p class="text-muted mb-0">
                            {{ $job->overview ?? 'No overview provided.' }}
                        </p>
                    </div>

                    <hr>

                    <!-- RESPONSIBILITIES -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fa fa-tasks text-primary mr-1"></i>
                            Responsibilities
                        </h6>
                        <p class="mb-0">
                            {{ $job->responsibilities ?? 'Not specified.' }}
                        </p>
                    </div>

                    <hr>

                    <!-- SKILLS -->
                    <div>
                        <h6 class="font-weight-bold mb-2">
                            <i class="fa fa-tools text-primary mr-1"></i>
                            Required Skills
                        </h6>
                        <p class="mb-0">
                            {{ $job->required_skills ?? 'Not specified.' }}
                        </p>
                    </div>

                </div>

                <!-- ================= FOOTER ================= -->
                <div class="modal-footer bg-light">

                    <button class="btn btn-outline-secondary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>
    @endforeach

</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function() {

        $('.delete-btn').on('click', function(e) {
            e.preventDefault();

            let form = $(this).closest('form');

            Swal.fire({
                title: "Are you sure?"
                , text: "This job will be permanently deleted."
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: "#dc3545"
                , cancelButtonColor: "#6c757d"
                , confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });

    });

</script>
@endpush