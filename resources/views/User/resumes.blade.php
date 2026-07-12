@extends('layouts.User_layout')
@section('title', 'My Resumes')

@section('content')

<div class="container py-4">

    @if (session('success'))
    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger shadow-sm border-0">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-file-pdf"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">My Resumes</h1>
                <small class="text-muted">Upload up to 5 resumes and pick which one to send with each application.</small>
            </div>
        </div>
        <span class="badge badge-primary badge-pill px-3 py-2">
            {{ $resumes->count() }} / 5 saved
        </span>
    </div>

    <div class="row">

        <!-- Upload Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow rounded h-100">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold mb-3">
                        <i class="fas fa-upload mr-2 text-primary"></i> Upload New Resume
                    </h5>

                    @if ($resumes->count() >= 5)
                    <p class="text-muted mb-0">
                        You've reached the 5-resume limit. Delete one below to upload another.
                    </p>
                    @else
                    <form action="{{ route('user.resumes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="font-weight-semibold" for="title">Resume Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                placeholder="e.g. Frontend Developer Resume" maxlength="255" required
                                value="{{ old('title') }}">
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-semibold" for="resume">PDF File</label>
                            <input type="file" name="resume" id="resume" class="form-control" accept=".pdf" required>
                            <small class="text-muted">Only PDF allowed | Max size: 2MB</small>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus mr-1"></i> Add Resume
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resume List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow rounded">
                @if ($resumes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="font-weight-bold">Title</th>
                                <th class="font-weight-bold">Size</th>
                                <th class="font-weight-bold">Uploaded</th>
                                <th class="font-weight-bold">Default</th>
                                <th class="font-weight-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resumes as $resume)
                            <tr>
                                <td class="font-weight-bold text-primary">{{ $resume->title }}</td>
                                <td class="text-muted">{{ $resume->file_size_for_humans }}</td>
                                <td class="text-muted">{{ $resume->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($resume->is_default)
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-check mr-1"></i> Default
                                    </span>
                                    @else
                                    <form action="{{ route('user.resumes.default', $resume->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                                            Set Default
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('user.resumes.download', $resume->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        <form action="{{ route('user.resumes.destroy', $resume->id) }}" method="POST"
                                            class="d-inline m-0"
                                            onsubmit="return confirm('Delete this resume? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-file-pdf fa-3x mb-3"></i>
                    <p class="fs-5 mb-0">You haven't uploaded any resumes yet.</p>
                    <p class="text-muted">Upload one to reuse it across job applications instead of re-uploading
                        every time.</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection
