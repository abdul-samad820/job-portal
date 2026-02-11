@extends('layouts.index')
@section('title', 'Job Roles')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <!-- Soft Header Section -->
            <div class="p-4 rounded shadow-sm mb-4" style="background: #f7f9ff; border-left: 5px solid #007bff;">

                <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                    <div>
                        <h1 class="h4 font-weight-bold text-dark mb-1 d-flex align-items-center">
                            <i class="fa fa-briefcase text-primary mr-2"></i>
                            Job Roles
                        </h1>
                        <p class="text-muted small mb-0">
                            Manage all job roles used across the job postings.
                        </p>
                    </div>

                    <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                        <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active font-weight-bold">Roles</li>
                        </ol>
                    </nav>
                </div>

                <!-- Search + Add Button -->
                <div class="d-flex justify-content-between flex-wrap align-items-center mt-3">

                    <form method="GET" action="job_role" class="form-inline w-100" style="max-width: 400px;">
                        <input 
                            class="form-control mr-2 w-75" 
                            type="search" 
                            placeholder="Search role..." 
                            name="search" 
                            value="{{ request('search') }}"
                        >
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>

                    <a href="{{ route('admin.job_role_add') }}" class="btn btn-primary rounded-pill d-flex align-items-center mt-3 mt-md-0">
                        <i class="fa fa-plus mr-2"></i> Add New
                    </a>

                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body">

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>

                                    <td class="font-weight-bold text-dark">{{ $role->name }}</td>

                                    <td class="text-secondary">{{ $role->description }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">

                                            <a href="{{ route('admin.job_role_edit', $role->id) }}"
                                               class="btn btn-sm btn-outline-primary mr-2">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.job_role_delete', $role->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger delete-btn">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
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
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                        <div class="text-muted small">
                            Showing {{ $roles->firstItem() ?? 0 }} to
                            {{ $roles->lastItem() ?? 0 }} of
                            {{ $roles->total() }} entries
                        </div>

                        <div>
                            {{ $roles->links('pagination::bootstrap-4') }}
                        </div>
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
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
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
