@extends('layouts.Admin_layout')
@section('title', 'Job Categories')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-layer-group',
        'title' => 'Job Categories',
        'subtitle' => 'Manage all categories used for job postings.',
    ])

    {{-- TABLE SECTION --}}
    <div class="card shadow-sm border-0 rounded">
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <form method="GET" action="{{ route('admin.job_category') }}" class="form-inline">
                    <input type="search" name="search" class="form-control form-control-sm mr-2" aria-label="Search category" placeholder="Search category..." value="{{ request('search') }}">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-search"></i>
                    </button>
                </form>

                <a href="{{ route('admin.job_category_add') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus mr-1"></i> Add Category
                </a>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

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
                                    class="img-thumbnail" width="60" height="60" style="object-fit:cover;" alt="{{ $category->name ?? 'Category' }}">
                            </td>

                            <td class="font-weight-bold">
                                {{ $category->name }}
                            </td>

                            <td class="text-muted">
                                {{ Str::limit($category->description, 70) }}
                            </td>

                            <td class="text-center align-middle">
                                <div class="sa-row-actions justify-content-center">

                                    <a href="{{ route('admin.job_category_edit', $category->id) }}"
                                        class="btn btn-primary sa-icon-action" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.job_category_delete', $category->id) }}" method="POST"
                                        class="d-inline-flex">
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
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="font-weight-bold text-muted">No categories yet</h5>
                                <p class="text-muted mb-3">Create a category to start organizing your job postings.</p>
                                <a href="{{ route('admin.job_category_add') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Add Category
                                </a>
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