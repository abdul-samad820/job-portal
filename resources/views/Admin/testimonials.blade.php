@extends('layouts.Admin_layout')
@section('title', 'Testimonials')
@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-quote-left',
        'title' => 'Testimonials',
        'subtitle' => 'Manage the success stories shown on the homepage. User-submitted reviews need your approval before they go live.',
        'badge' => $pendingCount > 0 ? ['text' => $pendingCount.' pending', 'class' => 'badge-warning'] : null,
    ])

    {{-- FORM SECTION --}}
    <div id="testimonialForm" class="collapse mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form action="{{ route('testimonials_create') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold" for="name">Name</label>
                            <input id="name" type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold" for="designation">Designation</label>
                            <input id="designation" type="text" name="designation" class="form-control"
                                placeholder="e.g. UI/UX Designer">
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold" for="company">Company / City</label>
                            <input id="company" type="text" name="company" class="form-control"
                                placeholder="e.g. TechCorp India">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold" for="review">Review</label>
                        <textarea id="review" name="review" rows="3" class="form-control" maxlength="1000" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold" for="rating">Rating</label>
                            <select id="rating" name="rating" class="form-control" required>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>

                        <div class="col-md-9 form-group">
                            <label class="font-weight-bold" for="image">Photo</label>
                            <input id="image" type="file" name="image" class="form-control-file" accept="image/png, image/jpeg">
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-success">
                            <i class="fa fa-check mr-1"></i> Save Testimonial
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card shadow-sm border-0 rounded">
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <div class="sa-filter-pills">
                    <a href="{{ route('admin.testimonials') }}" class="btn btn-sm {{ ! $filter ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                    <a href="{{ route('admin.testimonials', ['filter' => 'pending']) }}" class="btn btn-sm {{ $filter === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Pending @if ($pendingCount > 0)<span class="badge badge-light ml-1">{{ $pendingCount }}</span>@endif
                    </a>
                    <a href="{{ route('admin.testimonials', ['filter' => 'approved']) }}" class="btn btn-sm {{ $filter === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">Approved</a>
                    <a href="{{ route('admin.testimonials', ['filter' => 'rejected']) }}" class="btn btn-sm {{ $filter === 'rejected' ? 'btn-primary' : 'btn-outline-secondary' }}">Rejected</a>
                </div>

                <button class="btn btn-primary btn-sm rounded-pill px-4" data-toggle="collapse" data-target="#testimonialForm">
                    <i class="fa fa-plus mr-2"></i> Add Testimonial
                </button>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Source</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($testimonials as $testimonial)
                        <tr>
                            <td>{{ $testimonial->id }}</td>

                            <td>
                                <img src="{{ $testimonial->image ? Storage::url($testimonial->image) : asset('admins/dist/img/user2-160x160.jpg') }}"
                                    width="42" height="42" class="rounded-circle" style="object-fit:cover;"
                                    alt="{{ $testimonial->name }}">
                            </td>

                            <td class="font-weight-bold">
                                {{ $testimonial->name }}
                                <div class="text-muted font-weight-normal small">
                                    {{ $testimonial->designation }}
                                    @if ($testimonial->designation && $testimonial->company) · @endif
                                    {{ $testimonial->company }}
                                </div>
                            </td>

                            <td>
                                @if ($testimonial->isUserSubmitted())
                                <span class="badge badge-info">
                                    <i class="fa fa-user mr-1"></i> Job Seeker
                                </span>
                                @else
                                <span class="badge badge-secondary">Admin</span>
                                @endif
                            </td>

                            <td class="text-muted">
                                {{ Str::limit($testimonial->review, 60) }}
                            </td>

                            <td class="text-warning">
                                {{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}
                            </td>

                            <td>
                                @if ($testimonial->status === 'approved')
                                <span class="status-badge status-active">
                                    <i class="fa fa-check-circle mr-1"></i> Approved
                                </span>
                                @elseif ($testimonial->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="fa fa-clock mr-1"></i> Pending
                                </span>
                                @else
                                <span class="status-badge status-inactive">
                                    <i class="fa fa-times-circle mr-1"></i> Rejected
                                </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center">

                                    @if ($testimonial->status === 'pending')
                                    <form action="{{ route('testimonials_approve', $testimonial->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success mr-1" title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('testimonials_reject', $testimonial->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger mr-1" title="Reject">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <a href="{{ route('testimonials_edit', $testimonial->id) }}"
                                        class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('testimonials_delete', $testimonial->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-quote-left fa-3x text-muted mb-3"></i>
                                <h5 class="font-weight-bold text-muted">No testimonials yet</h5>
                                <p class="text-muted mb-3">Add success stories to build trust on the homepage.</p>
                                <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#testimonialForm">
                                    <i class="fas fa-plus mr-1"></i> Add Testimonial
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
        <div class="row align-items-center p-3">
            <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
                <small class="text-muted">
                    Showing {{ $testimonials->firstItem() ?? 0 }}
                    &ndash;
                    {{ $testimonials->lastItem() ?? 0 }}
                    of {{ $testimonials->total() }} entries
                </small>
            </div>

            <div class="col-md-6 text-center text-md-right">
                {{ $testimonials->links('pagination::bootstrap-4') }}
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
                title: "Delete Testimonial ?"
                , text: "This action cannot be undone."
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: '#d33'
                , cancelButtonColor: '#6c757d'
                , confirmButtonText: "Yes, delete"
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
@endpush