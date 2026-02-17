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

                    <!-- Row 1 : Title + Breadcrumb -->
                    <div class="d-flex justify-content-between flex-wrap align-items-center">

                        <div>
                            <h4 class="font-weight-bold text-dark mb-1">
                                <i class="fa fa-briefcase text-primary mr-2"></i>
                                Job Management
                            </h4>
                            <small class="text-muted">
                                View, manage and edit all job postings.
                            </small>
                        </div>

                        <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
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

                    <!-- Row 2 : Search + Add Button -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">

                        <!-- Search -->
                        <form method="GET" action="{{ route('admin.job') }}" class="form-inline">

                            <input type="search" name="search" class="form-control mr-2" placeholder="Search jobs..."
                                value="{{ request('search') }}" style="max-width:280px;">

                            <button class="btn btn-outline-primary">
                                <i class="fa fa-search"></i>
                            </button>

                        </form>

                        <!-- Add Button -->
                        <a href="{{ route('admin.job_add') }}" class="btn btn-primary rounded-pill px-4 mt-3 mt-md-0">
                            <i class="fa fa-plus mr-2"></i>
                            Add Job
                        </a>

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

                                            <td class="text-center">
                                                <a href="{{ route('admin.job_edit', $job->id) }}"
                                                    class="btn btn-sm btn-outline-primary mr-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.job_delete', $job->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">

                    <div class="d-flex align-items-center">

                        <img src="{{ $job->job_image ? Storage::url('jobs/' . $job->job_image) : asset('admins/dist/img/default.png') }}"
                            style="width:55px;height:55px;object-fit:cover;border-radius:10px;" class="mr-3">

                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $job->title }}
                            </h5>
                            <small class="opacity-75">
                                {{ $job->location }}
                            </small>
                        </div>

                    </div>

                    <button class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <!-- Body -->
                <div class="modal-body px-4 py-3">

                    <!-- Basic Info Cards -->
                    <div class="row text-center mb-4">

                        <div class="col-md-3 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Experience</small>
                                <strong>{{ $job->experience ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Salary</small>
                                <strong class="text-success">
                                    {{ $job->salary ?? 'N/A' }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Type</small>
                                <span class="badge badge-info px-3 py-1">
                                    {{ $job->type }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Last Date</small>
                                <strong>
                                    {{ \Carbon\Carbon::parse($job->last_date)->format('d M Y') }}
                                </strong>
                            </div>
                        </div>

                    </div>

                    <!-- Overview -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold border-bottom pb-2">
                            Overview
                        </h6>
                        <p class="text-muted mt-2 mb-0">
                            {{ $job->overview ?? 'No overview provided.' }}
                        </p>
                    </div>

                    <!-- Responsibilities -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold border-bottom pb-2">
                            Responsibilities
                        </h6>
                        <p class="mt-2 mb-0">
                            {{ $job->responsibilities ?? 'Not specified.' }}
                        </p>
                    </div>

                    <!-- Required Skills -->
                    <div>
                        <h6 class="font-weight-bold border-bottom pb-2">
                            Required Skills
                        </h6>
                        <p class="mt-2 mb-0">
                            {{ $job->required_skills ?? 'Not specified.' }}
                        </p>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>
@endforeach
    </div>

@endsection


{{-- ===================== MODALS ===================== --}}


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
