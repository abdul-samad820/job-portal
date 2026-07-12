@extends('layouts.Admin_layout')
@section('title', 'FAQs')
@section('content')

<div class="container-fluid py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fa fa-question-circle"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">FAQs</h1>
                <small class="text-muted">Manage frequently asked questions.</small>
            </div>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-toggle="collapse" data-target="#faqForm">
            <i class="fa fa-plus mr-2"></i> Add FAQ
        </button>
    </div>

    {{-- FORM SECTION --}}
    <div id="faqForm" class="collapse mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form action="{{ route('faqs_create') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold" for="question">Question</label>
                        <input id="question" type="text" name="question" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold" for="answer">Answer</label>
                        <textarea id="answer" name="answer" rows="4" class="form-control" required></textarea>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-success">
                            <i class="fa fa-check mr-1"></i> Save FAQ
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card shadow-sm border-0 rounded">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Active</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($faqs as $faq)
                        <tr>
                            <td>{{ $faq->id }}</td>

                            <td class="font-weight-bold">
                                {{ $faq->question }}
                            </td>

                            <td class="text-muted">
                                {{ Str::limit($faq->answer, 80) }}
                            </td>
                            <td>
                                @if ($faq->status == 1)
                                <span class="status-badge status-active">
                                    <i class="fa fa-check-circle mr-1"></i> Active
                                </span>
                                @else
                                <span class="status-badge status-inactive">
                                    <i class="fa fa-times-circle mr-1"></i> Inactive
                                </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center">

                                    <a href="{{ route('faqs_edit', $faq->id) }}"
                                        class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('faqs_delete', $faq->id) }}" method="POST" class="d-inline">
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
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-circle-question fa-3x text-muted mb-3"></i>
                                <h5 class="font-weight-bold text-muted">No FAQs yet</h5>
                                <p class="text-muted mb-3">Add common questions to help candidates before they apply.</p>
                                <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#faqForm">
                                    <i class="fas fa-plus mr-1"></i> Add FAQ
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
                    Showing {{ $faqs->firstItem() ?? 0 }}
                    –
                    {{ $faqs->lastItem() ?? 0 }}
                    of {{ $faqs->total() }} entries
                </small>
            </div>

            <div class="col-md-6 text-center text-md-right">
                {{ $faqs->links('pagination::bootstrap-4') }}
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
                title: "Delete FAQs ?"
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