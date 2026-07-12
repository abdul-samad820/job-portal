@extends('layouts.User_layout')
@section('title', 'Bulk Apply')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="sa-page-header">
                <div class="d-flex align-items-center">
                    <div class="sa-page-icon mr-3"><i class="fas fa-paper-plane"></i></div>
                    <div>
                        <h1 class="sa-page-title font-weight-bold text-dark mb-0">Apply to {{ $jobs->count() }} Job(s)</h1>
                    </div>
                </div>
                <a href="{{ route('user.jobs') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>

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

            <!-- Selected Jobs -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-3">Selected Jobs</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach ($jobs as $job)
                        <li class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $job->title }}</span>
                            <span class="text-muted small">{{ $job->admin->company_name ?? 'Company' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Application Form (same cover letter + resume for all jobs) -->
            <div class="card border-0 shadow-lg rounded">
                <div class="card-body p-5">
                    <form action="{{ route('user.bulk_apply.store') }}" method="POST" enctype="multipart/form-data"
                        id="bulkApplicationForm">
                        @csrf

                        @foreach ($jobs as $job)
                        <input type="hidden" name="job_ids[]" value="{{ $job->id }}">
                        @endforeach

                        <!-- Cover Letter -->
                        <div class="mb-4">
                            <label class="font-weight-semibold" for="cover_letter">
                                Cover Letter (used for all selected jobs) <span class="text-danger">*</span>
                            </label>
                            <textarea name="cover_letter" id="cover_letter" class="form-control" rows="5"
                                maxlength="500" placeholder="Write a short professional cover letter..."
                                required></textarea>
                        </div>

                        <!-- Resume Selection -->
                        <div class="mb-4">
                            <label class="font-weight-semibold d-block">
                                Resume <span class="text-danger">*</span>
                            </label>

                            @if ($resumes->count() > 0)
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="src_library" name="resume_source" value="library"
                                    class="custom-control-input" checked>
                                <label class="custom-control-label" for="src_library">Use a saved resume</label>
                            </div>

                            <select name="resume_id" id="resume_id" class="form-control mb-3">
                                @foreach ($resumes as $r)
                                <option value="{{ $r->id }}" {{ $r->is_default ? 'selected' : '' }}>
                                    {{ $r->title }} ({{ $r->file_size_for_humans }})
                                </option>
                                @endforeach
                            </select>

                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="src_upload" name="resume_source" value="upload"
                                    class="custom-control-input">
                                <label class="custom-control-label" for="src_upload">Upload a new resume</label>
                            </div>
                            @else
                            <input type="hidden" name="resume_source" value="upload">
                            <small class="text-muted d-block mb-2">
                                You don't have any saved resumes yet — upload one below.
                            </small>
                            @endif

                            <div id="uploadBlock" class="{{ $resumes->count() > 0 ? 'pl-4' : '' }}">
                                <input type="file" name="resume" id="resume" class="form-control" accept=".pdf">
                                <small class="text-muted">Only PDF allowed | Max size: 2MB</small>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow px-5">
                                <i class="fas fa-paper-plane mr-2"></i> Submit {{ $jobs->count() }} Application(s)
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const srcLibrary = document.getElementById('src_library');
        const srcUpload = document.getElementById('src_upload');
        const uploadBlock = document.getElementById('uploadBlock');
        const resumeInput = document.getElementById('resume');
        const resumeSelect = document.getElementById('resume_id');

        function refresh() {
            const usingUpload = !srcLibrary || srcLibrary.checked === false;
            if (uploadBlock) uploadBlock.style.display = usingUpload ? 'block' : 'none';
            if (resumeInput) resumeInput.required = usingUpload;
            if (resumeSelect) resumeSelect.disabled = usingUpload;
        }

        if (srcLibrary && srcUpload) {
            srcLibrary.addEventListener('change', refresh);
            srcUpload.addEventListener('change', refresh);
        }
        refresh();
    });
</script>
@endpush
