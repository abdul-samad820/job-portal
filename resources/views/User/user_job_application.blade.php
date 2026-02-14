@extends('layouts.app')
@section('title', 'Application Form ')
@section('content')

    <div class="container py-5" style="margin-top:90px;">

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="fw-bold text-primary mb-0">
                        <i class="fas fa-file-signature me-2"></i> Apply for {{ $job->title ?? 'Job' }}
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('user.jobs') }}">Jobs</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Apply</li>
                        </ol>
                    </nav>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
                @endif

                <!-- Application Card -->
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <div class="card border-0 shadow-lg rounded-4">
                            <div class="card-body p-5">

                                @if ($alreadyApplied)
                                    <div class="alert alert-info shadow-sm d-flex align-items-center">
                                        <i class="fas fa-check-circle text-info mr-2"></i>
                                        <strong>You have already applied for this job.</strong>
                                    </div>
                                @endif

                                <h4 class="fw-semibold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-user-edit me-2 text-primary"></i> Application Form
                                </h4>

                                <!-- Job Summary Card -->
                                <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
                                    <div class="card-body p-4">
                                        <h5 class="fw-semibold mb-3 text-dark"><i
                                                class="fas fa-briefcase me-2 text-primary"></i>
                                            Job Details</h5>
                                        <ul class="list-unstyled mb-0 text-muted">
                                            <li><strong>Company:</strong> {{ $job->admin->company_name ?? 'Not specified' }}
                                            </li>
                                            <li><strong>Location:</strong> {{ $job->location ?? 'Remote / Flexible' }}</li>
                                            <li><strong>Salary:</strong>
                                                {{ $job->salary ? $job->salary . ' LPA' : 'Not disclosed' }}</li>

                                            <li><strong>Type:</strong> {{ ucfirst($job->type ?? 'Full-time') }}</li>
                                            <li><strong>Last Date:</strong>
                                                {{ \Carbon\Carbon::parse($job->last_date)->format('d M Y') }}</li>
                                        </ul>
                                    </div>
                                </div>

                                <form action="{{ route('apply_job-application', $job->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <!-- Position -->
                                    <div class="mb-4 mt-2">
                                        <label class="font-weight-semibold">Position</label>
                                        <input type="text" class="form-control bg-light" value="{{ $job->title }}"
                                            readonly>
                                    </div>

                                    <!-- Cover Letter -->
                                    <div class="mb-4">
                                        <label class="font-weight-semibold">
                                            Cover Letter <span class="text-danger">*</span>
                                        </label>

                                        <textarea name="cover_letter" id="cover_letter" class="form-control" rows="5" maxlength="500"
                                            placeholder="Write a short professional cover letter..." required></textarea>

                                        <small class="text-muted">
                                            <span id="letterCount">0</span>/500 characters
                                        </small>
                                    </div>

                                    <!-- Resume Upload -->
                                    <div class="mb-4">
                                        <label class="font-weight-semibold">
                                            Upload Resume <span class="text-danger">*</span>
                                        </label>

                                        <input type="file" name="resume" id="resume" class="form-control"
                                            accept=".pdf" required>

                                        <small id="fileName" class="text-success d-block mt-1"></small>
                                        <small class="text-muted">
                                            Only PDF allowed | Max size: 2MB
                                        </small>
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

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    /* Cover Letter Counter */
                    const textarea = document.getElementById('cover_letter');
                    const counter = document.getElementById('letterCount');

                    textarea.addEventListener('input', function() {
                        counter.innerText = this.value.length;
                    });

                    /* Resume File Name Preview */
                    const resumeInput = document.getElementById('resume');
                    const fileName = document.getElementById('fileName');

                    resumeInput.addEventListener('change', function() {
                        if (this.files.length > 0) {
                            fileName.innerText = "Selected: " + this.files[0].name;
                        }
                    });

                    /* Disable Button After Submit */
                    const form = document.querySelector('form');
                    const submitBtn = document.getElementById('submitBtn');

                    form.addEventListener('submit', function() {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Submitting...';
                    });

                });
            </script>
        @endpush
