@extends('layouts.superadmin')
@section('title', 'Global FAQs')

@section('content')

<div class="container-fluid py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fa fa-question-circle"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Global FAQs</h1>
                <small class="text-muted">These FAQs are shown to everyone on the public homepage. Company-specific FAQs are managed by each Admin separately.</small>
            </div>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-toggle="collapse" data-target="#faqForm">
            <i class="fa fa-plus mr-2"></i> Add FAQ
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="faqForm" class="collapse mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('superadmin.faqs.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">Question</label>
                        <input type="text" name="question" class="form-control" required maxlength="500">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Answer</label>
                        <textarea name="answer" rows="4" class="form-control" required maxlength="5000"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save FAQ</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Question</th>
                        <th style="width:120px">Status</th>
                        <th style="width:110px" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $faq->question }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 100) }}</small>
                        </td>
                        <td>
                            @if($faq->status)
                                <span class="badge badge-success">Visible</span>
                            @else
                                <span class="badge badge-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="sa-row-actions justify-content-end">
                                <button type="button" class="btn btn-primary sa-icon-action" title="Edit"
                                        data-toggle="collapse" data-target="#edit{{ $faq->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('superadmin.faqs.toggle', $faq->id) }}" method="POST" class="d-inline-flex">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-secondary sa-icon-action" title="{{ $faq->status ? 'Hide' : 'Show' }}">
                                        <i class="fa {{ $faq->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('superadmin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Delete this FAQ permanently?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger sa-icon-action" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="collapse" id="edit{{ $faq->id }}">
                        <td colspan="3" class="bg-light">
                            <form action="{{ route('superadmin.faqs.update', $faq->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="form-group">
                                    <label class="font-weight-bold">Question</label>
                                    <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required maxlength="500">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Answer</label>
                                    <textarea name="answer" rows="3" class="form-control" required maxlength="5000">{{ $faq->answer }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-question-circle"></i>
                            <p>No global FAQs yet.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $faqs->links() }}</div>

</div>

@endsection