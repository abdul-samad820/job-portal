@extends('layouts.index')
@section('title', 'Job Categories')

@section('content')

    <div class="container-fluid py-4">

        {{-- HEADER SECTION --}}
        <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary" style="border-width:4px !important;">

            {{-- ROW 1 : Title + Breadcrumb --}}
            <div class="d-md-flex justify-content-between align-items-center">

                <div class="mb-3 mb-md-0">
                    <h4 class="font-weight-bold text-dark mb-1">
                        <i class="fa fa-layer-group text-primary mr-2"></i>
                        Job Categories
                    </h4>
                    <small class="text-muted">
                        Manage all categories used for job postings.
                    </small>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active font-weight-bold">
                            Categories
                        </li>
                    </ol>
                </nav>

            </div>

            {{-- ROW 2 : Search + Add --}}
            <div class="mt-3">

                {{-- Desktop Layout --}}
                <div class="d-none d-md-flex justify-content-between align-items-center">

                    <form method="GET" action="{{ route('admin.job_category') }}" class="form-inline">

                        <input type="search" name="search" class="form-control mr-2" placeholder="Search category..."
                            value="{{ request('search') }}" style="max-width:280px;">

                        <button class="btn btn-outline-primary">
                            <i class="fa fa-search"></i>
                        </button>

                    </form>

                    <a href="{{ route('admin.job_category_add') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fa fa-plus mr-2"></i>
                        Add Category
                    </a>

                </div>

                {{-- Mobile Layout --}}
                <div class="d-block d-md-none">

                    <form method="GET" action="{{ route('admin.job_category') }}">

                        <div class="input-group mb-3">
                            <input type="search" name="search" class="form-control" placeholder="Search category..."
                                value="{{ request('search') }}">

                            <div class="input-group-append">
                                <button class="btn btn-outline-primary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>

                    </form>

                    <a href="{{ route('admin.job_category_add') }}" class="btn btn-primary rounded-pill btn-block">
                        <i class="fa fa-plus mr-2"></i>
                        Add Category
                    </a>

                </div>

            </div>

        </div>
        {{-- TABLE SECTION --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">

                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>

                                    <td>
                                        <img src="{{ $category->category_image ? Storage::url($category->category_image) : asset('admins/dist/img/default.png') }}"
                                            class="img-thumbnail" width="60" height="60" style="object-fit:cover;">
                                    </td>

                                    <td class="font-weight-bold">
                                        {{ $category->name }}
                                    </td>

                                    <td class="text-muted">
                                        {{ Str::limit($category->description, 70) }}
                                    </td>

                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center">

                                            <a href="{{ route('admin.job_category_edit', $category->id) }}"
                                                class="btn btn-sm btn-outline-primary mr-1">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.job_category_delete', $category->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">
                                                    <i class="fa fa-trash"></i>
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

                {{-- Pagination --}}
                <div class="row align-items-center p-3">

                    <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
                        <small class="text-muted">
                            Showing {{ $categories->firstItem() ?? 0 }}
                            –
                            {{ $categories->lastItem() ?? 0 }}
                            of {{ $categories->total() }} entries
                        </small>
                    </div>

                    <div class="col-md-6 text-center text-md-right">
                        {{ $categories->links('pagination::bootstrap-4') }}
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
