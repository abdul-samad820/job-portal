@extends('layouts.index')
@section('title', 'Job Roles')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                <!-- Header Section -->
                <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary"
                    style="border-width:4px !important;">

                    <!-- Row 1 : Title + Breadcrumb -->
                    <div class="d-flex justify-content-between flex-wrap align-items-center">

                        <div>
                            <h4 class="font-weight-bold text-dark mb-1">
                                <i class="fa fa-briefcase text-primary mr-2"></i>
                                Job Roles
                            </h4>
                            <small class="text-muted">
                                Manage all job roles used across the job postings.
                            </small>
                        </div>

                        <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                            <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active font-weight-bold">
                                    Roles
                                </li>
                            </ol>
                        </nav>

                    </div>

                    <!-- Row 2 : Search + Add Button -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">

                        <!-- Search -->
                        <form method="GET" action="{{ route('admin.job_role') }}" class="form-inline">

                            <input class="form-control mr-2" type="search" placeholder="Search role..." name="search"
                                value="{{ request('search') }}" style="max-width:280px;">

                            <button class="btn btn-outline-primary">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                        <!-- Add Button -->
                        <a href="{{ route('admin.job_role_add') }}" class="btn btn-primary rounded-pill px-4 mt-3 mt-md-0">
                            <i class="fa fa-plus mr-2"></i>
                            Add Role
                        </a>

                    </div>

                </div>

                <!-- Main Table Card -->
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">

                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:70px;">#</th>
                                        <th>Role Name</th>
                                        <th>Description</th>
                                        <th class="text-center" style="width:180px;">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($roles as $index => $role)
                                        <tr>

                                            <!-- Pagination Friendly Index -->
                                            <td>
                                                {{ $roles->firstItem() + $index }}
                                            </td>

                                            <!-- Role Name -->
                                            <td class="font-weight-bold text-dark">
                                                {{ $role->name }}
                                            </td>

                                            <!-- Limited Description -->
                                            <td class="text-muted">
                                                {{ \Illuminate\Support\Str::limit($role->description, 60) }}
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <a href="{{ route('admin.job_role_edit', $role->id) }}"
                                                    class="btn btn-sm btn-outline-primary mr-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.job_role_delete', $role->id) }}"
                                                    method="POST" class="d-inline">
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
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No job roles found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">

                            <span class="text-muted small">
                                Showing {{ $roles->firstItem() ?? 0 }} –
                                {{ $roles->lastItem() ?? 0 }}
                                of {{ $roles->total() }} entries
                            </span>

                            {{ $roles->links('pagination::bootstrap-4') }}

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "Are You Sure?",
                        text: "You want to delete this role?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete!'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endpush
