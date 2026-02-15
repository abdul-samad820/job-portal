@extends('layouts.index')
@section('title', 'Job Management')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="p-4 rounded shadow-sm mb-4" style="background: #f7f9ff; border-left: 5px solid #007bff;">

                    <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                        <div>
                            <h1 class="h4 font-weight-bold text-dark mb-1 d-flex align-items-center">
                                <i class="fa fa-briefcase text-primary mr-2"></i>
                                Job Management
                            </h1>
                            <p class="text-muted small mb-0">
                                View, manage and edit all job postings.
                            </p>
                        </div>

                        <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                            <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                                        Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item active font-weight-bold">Jobs</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Search + Add Button -->
                    <div class="d-flex justify-content-between flex-wrap align-items-center mt-3">

                        <form method="GET" action="{{ route('admin.job') }}" class="d-flex w-100"
                            style="max-width:370px;">

                            <input type="search" name="search" class="form-control mr-2" placeholder="Search jobs..."
                                value="{{ request('search') }}">

                            <button class="btn btn-primary px-3" type="submit">
                                <i class="fa fa-search"></i>
                            </button>

                        </form>

                        <a href="{{ route('admin.job_add') }}"
                            class="btn btn-primary rounded-pill d-flex align-items-center mt-3 mt-md-0">
                            <i class="fa fa-plus mr-2"></i> Add New Job
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
                                                <img src="{{ $job->job_image ? asset('storage/jobs/' . $job->job_image) : asset('admins/dist/img/default.png') }}"
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


                                            <td><span class="badge badge-info">{{ $job->type }}</span></td>

                                            <td>
                                                <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                                    {{ $lastDate->format('d M Y') }}
                                                    <small>({{ $isExpired ? 'Expired' : 'Open' }})</small>
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">

                                                    <!-- EDIT BUTTON -->
                                                    <a href="{{ route('admin.job_edit', $job->id) }}"
                                                        class="btn btn-sm btn-outline-primary d-flex align-items-center mr-2 px-3 py-1">
                                                        <i class="fa fa-edit mr-1"></i> Edit
                                                    </a>

                                                    <!-- DELETE BUTTON -->
                                                    <form action="{{ route('admin.job_delete', $job->id) }}" method="POST"
                                                        class="m-0 p-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger d-flex align-items-center px-3 py-1 delete-btn">
                                                            <i class="fa fa-trash mr-1"></i> Delete
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
    </div>

@endsection


{{-- ===================== MODALS ===================== --}}
@foreach ($jobs as $job)
    <div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content shadow">

                <div class="modal-header bg-light">
                    <h5 class="modal-title">{{ $job->title }} — Details</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <h6 class="font-weight-bold mb-2">Overview</h6>
                    <p class="text-muted">{{ $job->overview ?? '—' }}</p>

                    <hr>

                    <h6 class="font-weight-bold">Responsibilities</h6>
                    <p>{{ $job->responsibilities ?? '—' }}</p>
                    <hr>

                    <h6 class="font-weight-bold">Required Skills</h6>
                    <p>{{ $job->required_skills ?? '—' }}</p>
                    <hr>

                    <h6 class="font-weight-bold">Basic Info</h6>
                    <p><strong>Experience:</strong> {{ $job->experience }}</p>
                    <p><strong>Location:</strong> {{ $job->location }}</p>
                    <p><strong>Salary:</strong> {{ $job->salary }} LPA</p>
                    <p><strong>Type:</strong> {{ $job->type }}</p>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endforeach


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
