@extends('layouts.user_index')
@section('title', 'Saved Jobs')

@section('content')

<style>
.job-card {
    transition: all 0.3s ease;
}
.job-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
</style>


<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="bg-white p-4 shadow-sm rounded-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-bookmark text-primary me-2"></i>
                        Saved Jobs
                    </h4>
                </div>

                @forelse($savedJobs as $saved)

                    @php $job = $saved->job; @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4 job-card">
                        <div class="card-body">

                            <div class="row align-items-center">

                                <!-- Left Content -->
                                <div class="col-md-8">

                                    <h5 class="fw-bold text-dark mb-1">
                                        {{ $job->title }}
                                    </h5>

                                    <p class="text-muted mb-2">
                                        {{ $job->admin->company_name ?? 'Company Name' }}
                                    </p>

                                    <div class="d-flex flex-wrap text-muted small">

                                        <span class="me-3">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ ucfirst($job->location) }}
                                        </span>

                                        <span class="me-3">
                                            <i class="fas fa-briefcase text-primary me-1"></i>
                                            {{ $job->type ?? 'N/A' }}
                                        </span>

                                        <span class="me-3">
                                            <i class="fas fa-user-clock text-info me-1"></i>
                                            {{ $job->experience ?? 'N/A' }}
                                        </span>

                                        <span>
                                            <i class="fas fa-rupee-sign text-success me-1"></i>
                                            {{ $job->salary ?? 'Not Disclosed' }} LPA
                                        </span>

                                    </div>

                                </div>

                                <!-- Right Buttons -->
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                                    <a href="{{ route('user.job_single', $job->id) }}"
                                       class="btn btn-primary btn-sm rounded-pill me-2">
                                        <i class="fas fa-eye me-1"></i>
                                        View Details
                                    </a>

                                    <a href="{{ route('user.unsave.job', $job->id) }}"
                                       class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        Remove
                                    </a>

                                </div>

                            </div>

                        </div>
                    </div>

                @empty

                    <div class="text-center py-5">
                        <i class="far fa-bookmark fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold">No Saved Jobs</h5>
                        <p class="text-muted">Start saving jobs you’re interested in.</p>

                        <a href="{{ route('user.jobs') }}" 
                           class="btn btn-success rounded-pill px-4">
                            Browse Jobs
                        </a>
                    </div>

                @endforelse

            </div>
        </div>
    </div>
</div>

@endsection