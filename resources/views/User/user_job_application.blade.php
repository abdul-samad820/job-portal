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
                        <h4 class="fw-semibold text-dark mb-4 border-bottom pb-2">
                            <i class="fas fa-user-edit me-2 text-primary"></i> Application Form
                        </h4>

                        <!-- Job Summary Card -->
                        <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3 text-dark"><i class="fas fa-briefcase me-2 text-primary"></i>
                                    Job Details</h5>
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li><strong>Company:</strong> {{ $job->admin->company_name ?? 'Not specified' }}</li>
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
                            <!-- Job Info -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary">Position</label>
                                <input type="text" class="form-control bg-light" value="{{ $job->title }}" readonly>
                            </div>

                            <!-- Cover Letter -->
                            <div class="mb-4">
                                <label for="cover_letter" class="form-label fw-semibold text-secondary">Cover Letter <span
                                        class="text-danger">*</span></label>
                                <textarea name="cover_letter" id="cover_letter" class="form-control shadow-sm" rows="5"
                                    placeholder="Write a short and professional cover letter..." required></textarea>
                            </div>

                            <!-- Resume Upload -->
                            <div class="mb-4">
                                <label for="resume" class="form-label fw-semibold text-secondary">Upload Resume <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="resume" id="resume" class="form-control shadow-sm"
                                    accept=".pdf,.doc,.docx" required>
                                <small class="text-muted d-block mt-1">Accepted formats: PDF, DOC, DOCX | Max size:
                                    2MB</small>
                            </div>

                            <!-- Hidden Status -->
                            <input type="hidden" name="status" value="pending">

                            <!-- Submit -->
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold rounded-pill shadow-sm">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Application
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert for delete confirmation (future-proof)
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "Are You Sure?",
                        text: "This will delete your application permanently.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Auto-hide alerts
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endpush
