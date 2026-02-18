@extends('layouts.index')
@section('title', 'Job Management')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
@endpush
@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary"
                    style="border-width:4px !important;">

                    {{-- ROW 1 : Title + Breadcrumb --}}
                    <div class="d-md-flex justify-content-between align-items-center">

                        <div class="mb-3 mb-md-0">
                            <h4 class="font-weight-bold text-dark mb-1">
                                <i class="fa fa-briefcase text-primary mr-2"></i>
                                Job Management
                            </h4>
                            <small class="text-muted">
                                View, manage and edit all job postings.
                            </small>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active font-weight-bold">
                                    Jobs
                                </li>
                            </ol>
                        </nav>

                    </div>

                    {{-- ROW 2 : Search + Add --}}
                    <div class="mt-3">

                        {{-- Desktop Layout --}}
                        <div class="d-none d-md-flex justify-content-between align-items-center">

                            <form method="GET" action="{{ route('admin.job') }}" class="form-inline">

                                <input type="search" name="search" class="form-control mr-2" placeholder="Search jobs..."
                                    value="{{ request('search') }}" style="max-width:280px;">

                                <button class="btn btn-outline-primary">
                                    <i class="fa fa-search"></i>
                                </button>

                            </form>

                            <a href="{{ route('admin.job_add') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fa fa-plus mr-2"></i>
                                Add Job
                            </a>

                        </div>

                        {{-- Mobile Layout --}}
                        <div class="d-block d-md-none">

                            <form method="GET" action="{{ route('admin.job') }}">

                                <div class="input-group mb-3">
                                    <input type="search" name="search" class="form-control" placeholder="Search jobs..."
                                        value="{{ request('search') }}">

                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                            </form>

                            <a href="{{ route('admin.job_add') }}" class="btn btn-primary btn-block rounded-pill">
                                <i class="fa fa-plus mr-2"></i>
                                Add Job
                            </a>

                        </div>

                    </div>

                </div>

                <!-- Jobs Table -->
                <div class="card shadow-sm border-0 rounded">
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
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($jobs as $job)
                                        @php
                                            $lastDate = \Carbon\Carbon::parse($job->last_date);
                                            $isExpired = $lastDate->isPast();
                                        @endphp

                                        <tr @if ($isExpired) style="opacity:0.6" @endif>

                                            <td>{{ $job->id }}</td>

                                            <!-- job Image -->
                                            <td>
                                                <img src="{{ Storage::url($job->job_image) }}"
                                                    style="width:70px;height:70px;object-fit:cover;border-radius:6px;">
                                            </td>


                                            <td class="font-weight-bold text-dark">
                                                {{ $job->title }} <br>
                                                <button class="btn btn-link p-0 mt-1 text-primary small" data-toggle="modal"
                                                    data-target="#jobModal{{ $job->id }}">
                                                    View Details
                                                </button>
                                            </td>

                                            <td>{{ $job->location }}</td>

                                            <td class="font-weight-bold text-success">
                                                {{ str_replace('LPA', '', $job->salary) }} LPA
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
                                                <span
                                                    class="badge {{ $typeClasses[$job->type] ?? 'badge-secondary' }} px-3 py-2 badge-pill">
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

                                            <td class="text-center align-middle">

                                                <div class="d-flex justify-content-center align-items-center">

                                                    <a href="{{ route('admin.job_edit', $job->id) }}"
                                                        class="btn btn-sm btn-outline-primary mr-2">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('admin.job_delete', $job->id) }}" method="POST"
                                                        class="mb-0">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger delete-btn">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>

                                                </div>

                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No jobs found.</td>
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

            </div>
        </div>
        @foreach ($jobs as $job)
            <div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content border-0 shadow">

                        <!-- ================= HEADER ================= -->
                        <div class="modal-header text-white" style="background: linear-gradient(135deg,#007bff,#0056b3);">

                            <div class="d-flex align-items-center">

                                <img src="{{ Storage::url($job->job_image) }}" class="mr-3 shadow"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:12px;">

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
                                            {{ $job->salary ?? 'N/A' }} LPA
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
                    title: "Are you sure?",
                    text: "This job will be permanently deleted.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        });
    </script>
@endpush
