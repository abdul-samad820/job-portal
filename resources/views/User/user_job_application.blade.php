@extends('layouts.landing_page')
@section('title', 'Application Form ')
@section('content')

<div class="container py-5 u-mt-90px">

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <!-- Header -->
            <div class="apply-header">
                <div class="d-flex align-items-center">
                    <div class="apply-header-icon mr-3"><i class="fas fa-file-signature"></i></div>
                    <div>
                        <h1 class="apply-header-title mb-0">Apply for {{ $job->title ?? 'Job' }}</h1>
                        <small class="text-muted">at {{ $job->admin->company_name ?? 'this company' }}</small>
                    </div>
                </div>
                <a href="{{ route('user.jobs') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Jobs
                </a>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
            <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
            @endif

            <!-- Application Card -->
            <div class="card border-0 shadow-lg rounded apply-card">
                <div class="card-body p-5">

                            @if ($alreadyApplied)
                            <div class="alert alert-info shadow-sm d-flex align-items-center">
                                <i class="fas fa-check-circle text-info mr-2"></i>
                                <strong>You have already applied for this job.</strong>
                            </div>
                            @endif

                            <h4 class="font-weight-bold text-dark mb-4 border-bottom pb-2">
                                <i class="fas fa-user-edit mr-2 text-primary"></i> Application Form
                            </h4>

                            <!-- Job Summary Card -->
                            <div class="apply-job-summary mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    @if($job->admin->profile_image ?? false)
                                    <img src="{{ asset('storage/admins/'.$job->admin->profile_image) }}"
                                         class="apply-company-logo mr-3" alt="{{ $job->admin->company_name }}">
                                    @else
                                    <div class="apply-company-logo apply-company-logo-fallback mr-3">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <h5 class="font-weight-bold mb-0 text-dark">{{ $job->admin->company_name ?? 'Not specified' }}</h5>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $job->location ?? 'Remote / Flexible' }}</small>
                                    </div>

                                    @php
                                        $daysLeft = \Carbon\Carbon::parse($job->last_date)->diffInDays(now(), false) * -1;
                                    @endphp
                                    @if($daysLeft >= 0 && $daysLeft <= 5)
                                    <span class="badge apply-badge-urgent ml-auto">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $daysLeft == 0 ? 'Closes today' : ($daysLeft == 1 ? '1 day left' : "{$daysLeft} days left") }}
                                    </span>
                                    @endif
                                </div>

                                <div class="apply-job-meta">
                                    <div class="apply-job-meta-item">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>
                                            @if($job->min_salary && $job->max_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{ number_format($job->max_salary / 100000, 1) }}L
                                            @elseif($job->min_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                                            @else
                                            Salary not disclosed
                                            @endif
                                        </span>
                                    </div>
                                    <div class="apply-job-meta-item">
                                        <i class="fas fa-briefcase"></i>
                                        <span>{{ ucfirst($job->type ?? 'Full-time') }}</span>
                                    </div>
                                    <div class="apply-job-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Apply by {{ \Carbon\Carbon::parse($job->last_date)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <form action="{{ route('apply_job_application', $job->id) }}" id="applicationForm"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Position -->
                                <div class="mb-4 mt-2">
                                    <label class="font-weight-semibold" for="position_display">Position</label>
                                    <input type="text" id="position_display" class="form-control bg-light" value="{{ $job->title }}" readonly>
                                </div>

                                <!-- Cover Letter -->
                                <div class="mb-4">
                                    <label class="font-weight-semibold" for="cover_letter">
                                        Cover Letter <span class="text-danger">*</span>
                                    </label>

                                    <textarea name="cover_letter" id="cover_letter" class="form-control" rows="5"
                                        maxlength="500" placeholder="Write a short professional cover letter..."
                                        required></textarea>

                                    <small class="text-muted">
                                        <span id="letterCount">0</span>/500 characters
                                    </small>
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
                                        <label class="custom-control-label" for="src_library">
                                            Use a saved resume
                                        </label>
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
                                        <label class="custom-control-label" for="src_upload">
                                            Upload a new resume
                                        </label>
                                    </div>
                                    @else
                                    <input type="hidden" name="resume_source" value="upload">
                                    <small class="text-muted d-block mb-2">
                                        You don't have any saved resumes yet — upload one below.
                                        <a href="{{ route('user.resumes.index') }}" target="_blank">Manage resumes</a>
                                    </small>
                                    @endif

                                    <div id="uploadBlock" class="{{ $resumes->count() > 0 ? 'pl-4' : '' }}">
                                        <label for="resume" class="apply-dropzone" id="dropzone">
                                            <i class="fas fa-cloud-upload-alt apply-dropzone-icon"></i>
                                            <span class="apply-dropzone-text" id="dropzoneText">Click to choose a PDF, or drag it here</span>
                                            <small class="text-muted">Only PDF allowed | Max size: 2MB</small>
                                        </label>
                                        <input type="file" name="resume" id="resume" class="d-none" accept=".pdf">

                                        <div class="custom-control custom-checkbox mt-3">
                                            <input type="checkbox" class="custom-control-input" id="save_to_library"
                                                name="save_to_library" value="1">
                                            <label class="custom-control-label" for="save_to_library">
                                                Save this resume to my library for future applications
                                            </label>
                                        </div>
                                        <input type="text" name="resume_title" class="form-control mt-2"
                                            placeholder="Resume title (e.g. Backend Developer Resume)" maxlength="255">
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="text-center mt-4">
                                    <button type="submit" id="submitBtn"
                                        class="btn btn-primary btn-lg rounded-pill shadow px-5">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Submit Application
                                    </button>
                                </div>
                            </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* This page extends layouts.landing_page, which doesn't load
       user-pro.css (that's only pulled in by the internal dashboard
       shell, layouts.User_layout). The old .sa-page-header/.sa-page-icon
       classes here had no matching CSS at all, so the header rendered
       completely flat. These are page-scoped replacements that reuse
       the same --accent tokens already defined in landing_page.css. */

    .apply-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 16px;
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, var(--accent-soft) 0%, #ffffff 60%);
        padding: 24px 28px; margin-top: 8px; margin-bottom: 24px;
        border: 1px solid var(--border); border-radius: 14px;
        box-shadow: var(--shadow-xs);
    }
    .apply-header::before {
        content: ""; position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
        background: linear-gradient(180deg, var(--accent), var(--accent-dark));
    }
    .apply-header-icon {
        width: 50px; height: 50px; border-radius: 12px;
        background: rgba(var(--accent-rgb), 0.1); color: var(--accent);
        display: inline-flex; align-items: center; justify-content: center; font-size: 20px;
        flex-shrink: 0;
    }
    .apply-header-title {
        font-size: 1.4rem; font-weight: 700; letter-spacing: -0.01em;
        color: var(--text-primary);
    }

    .apply-card { border-radius: 16px; }

    .apply-job-summary {
        background: var(--bg-page); border: 1px solid var(--border);
        border-radius: 12px; padding: 20px 22px;
    }
    .apply-company-logo {
        width: 44px; height: 44px; border-radius: 10px; object-fit: cover;
        flex-shrink: 0;
    }
    .apply-company-logo-fallback {
        background: rgba(var(--accent-rgb), 0.1); color: var(--accent);
        display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .apply-badge-urgent {
        background: #fef3c7; color: #92400e; font-weight: 600;
        padding: 6px 12px; border-radius: 20px; font-size: 0.78rem;
    }
    .apply-job-meta {
        display: flex; flex-wrap: wrap; gap: 14px 24px;
        padding-top: 12px; border-top: 1px solid var(--border);
    }
    .apply-job-meta-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.88rem; color: var(--text-secondary);
    }
    .apply-job-meta-item i { color: var(--accent); width: 16px; text-align: center; }

    .apply-dropzone {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px; text-align: center; cursor: pointer;
        border: 2px dashed var(--border); border-radius: 12px;
        padding: 28px 20px; background: var(--bg-page);
        transition: border-color .15s ease, background .15s ease;
        margin: 0;
    }
    .apply-dropzone:hover, .apply-dropzone.is-dragover {
        border-color: var(--accent); background: var(--accent-soft);
    }
    .apply-dropzone-icon { font-size: 26px; color: var(--accent); }
    .apply-dropzone-text { font-weight: 500; color: var(--text-secondary); }
</style>
@endpush

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /* Cover Letter Counter */
                const textarea = document.getElementById('cover_letter');
                const counter = document.getElementById('letterCount');

                textarea.addEventListener('input', function() {
                    counter.innerText = this.value.length;
                });

                /* Draft autosave (Phase9 UX-04) — cover letter only; file
                   inputs can't be restored from localStorage for security
                   reasons, so a lost resume selection is unavoidable, but
                   losing typed text is the more painful part to lose. */
                const draftKey = 'job_application_draft_{{ $job->id }}';

                const savedDraft = localStorage.getItem(draftKey);
                if (savedDraft && !textarea.value) {
                    textarea.value = savedDraft;
                    counter.innerText = savedDraft.length;
                }

                let saveTimeout;
                textarea.addEventListener('input', function() {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(function() {
                        localStorage.setItem(draftKey, textarea.value);
                    }, 500);
                });

                document.getElementById('applicationForm').addEventListener('submit', function() {
                    localStorage.removeItem(draftKey);
                });

                /* Resume File Name Preview + Dropzone */
                const resumeInput = document.getElementById('resume');
                const dropzone = document.getElementById('dropzone');
                const dropzoneText = document.getElementById('dropzoneText');

                function showFileName(file) {
                    if (file) {
                        dropzoneText.innerText = file.name;
                    }
                }

                resumeInput.addEventListener('change', function() {
                    if (this.files.length > 0) showFileName(this.files[0]);
                });

                if (dropzone) {
                    ['dragover', 'dragenter'].forEach(evt => {
                        dropzone.addEventListener(evt, function(e) {
                            e.preventDefault();
                            dropzone.classList.add('is-dragover');
                        });
                    });
                    ['dragleave', 'drop'].forEach(evt => {
                        dropzone.addEventListener(evt, function(e) {
                            e.preventDefault();
                            dropzone.classList.remove('is-dragover');
                        });
                    });
                    dropzone.addEventListener('drop', function(e) {
                        if (e.dataTransfer.files.length > 0) {
                            resumeInput.files = e.dataTransfer.files;
                            showFileName(e.dataTransfer.files[0]);
                        }
                    });
                }

                /* Resume source toggle (library vs upload) */
                const srcLibrary = document.getElementById('src_library');
                const srcUpload = document.getElementById('src_upload');
                const uploadBlock = document.getElementById('uploadBlock');
                const resumeSelect = document.getElementById('resume_id');

                function refreshResumeSourceUI() {
                    const usingUpload = !srcLibrary || srcLibrary.checked === false;
                    if (uploadBlock) {
                        uploadBlock.style.display = usingUpload ? 'block' : 'none';
                    }
                    if (resumeInput) {
                        resumeInput.required = usingUpload;
                    }
                    if (resumeSelect) {
                        resumeSelect.disabled = usingUpload;
                    }
                }

                if (srcLibrary && srcUpload) {
                    srcLibrary.addEventListener('change', refreshResumeSourceUI);
                    srcUpload.addEventListener('change', refreshResumeSourceUI);
                }
                refreshResumeSourceUI();

            });

        </script>
        @endpush