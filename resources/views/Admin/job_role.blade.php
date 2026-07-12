@extends('layouts.Admin_layout')
@section('title', 'Job Roles')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            @include('partials.superadmin-page-header', [
                'icon' => 'fa-briefcase',
                'title' => 'Job Roles',
                'subtitle' => 'Manage all job roles used across the job postings.',
            ])

            <!-- Main Table Card -->
            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-white">
                    <div class="sa-toolbar mb-0">
                        <form method="GET" action="{{ route('admin.job_role') }}" class="form-inline">
                            <input type="search" name="search" class="form-control form-control-sm mr-2" aria-label="Search role" placeholder="Search role..." value="{{ request('search') }}">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                        <a href="{{ route('admin.job_role_add') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus mr-1"></i> Add Role
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">

                            <thead class="thead-light">
                                <tr>
                                    <th class="u-w-70px">#</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th class="text-center u-w-180px">Actions</th>
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
                                        <div class="sa-row-actions justify-content-center">

                                            <a href="{{ route('admin.job_role_edit', $role->id) }}"
                                                class="btn btn-primary sa-icon-action" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.job_role_delete', $role->id) }}" method="POST" class="d-inline-flex">
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
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                        <h5 class="font-weight-bold text-muted">No job roles yet</h5>
                                        <p class="text-muted mb-3">Add a role so you can attach it to job postings.</p>
                                        <a href="{{ route('admin.job_role_add') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Add Role
                                        </a>
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