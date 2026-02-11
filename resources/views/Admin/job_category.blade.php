@extends('layouts.index')
@section('title', 'Job Categories')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                <!-- Header Section -->
                <div class="p-4 rounded shadow-sm mb-4" style="background:#f3f7ff; border-left:5px solid #007bff;">

                    <div class="d-flex justify-content-between flex-wrap align-items-center">

                        <div>
                            <h4 class="text-dark font-weight-bold mb-1">
                                <i class="fa fa-layer-group text-primary mr-2"></i>
                                Job Categories
                            </h4>
                            <p class="text-muted mb-0" style="font-size:13px;">
                                Manage all categories used for job postings.
                            </p>
                        </div>

                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                            <ol class="breadcrumb bg-white shadow-sm px-3 py-2 rounded">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Categories</li>
                            </ol>
                        </nav>

                    </div>

                    <!-- Search + Add Button -->
                    <div class="d-flex justify-content-between flex-wrap align-items-center mt-3">

                        <form action="{{ route('admin.job_category') }}" method="GET" class="form-inline mb-2"
                            style="max-width:420px;">

                            <input type="search" name="search" placeholder="Search category..."
                                class="form-control mr-2 w-75" value="{{ request('search') }}">

                            <button class="btn btn-primary">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                        <a href="{{ route('admin.job_category_add') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="fa fa-plus mr-2"></i> Add Category
                        </a>
                    </div>

                </div>

                <!-- Table Card -->
                <div class="card shadow-sm border-0 rounded">

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Category Image</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>

                                            <!-- Category Image -->
                                            <td>
                                                <img src="{{ asset('uploads/categories/' . $category->category_image) }}"
                                                    class="img-thumbnail"
                                                    style="width:60px; height:60px; object-fit:cover; border:1px solid #ddd;">
                                            </td>

                                            <!-- Name -->
                                            <td class="font-weight-bold text-dark">
                                                {{ $category->name }}
                                            </td>

                                            <!-- Description -->
                                            <td class="text-muted" style="max-width:350px;">
                                                {{ Str::limit($category->description, 70) }}
                                            </td>

                                            <!-- Actions -->


                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">

                                                    <a href="{{ route('admin.job_category_edit', $category->id) }}"
                                                        class="btn btn-sm btn-outline-primary mr-2">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>

                                                    <form action="{{ route('admin.job_category_delete', $category->id) }}"
                                                        method="POST">
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
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No categories found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>


                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">

                            <span class="small text-muted">
                                Showing {{ $categories->firstItem() ?? 0 }}–
                                {{ $categories->lastItem() ?? 0 }} of
                                {{ $categories->total() }} entries
                            </span>

                            {{ $categories->links('pagination::bootstrap-4') }}

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
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: "Delete Category?",
                    text: "This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "Yes, delete"
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
