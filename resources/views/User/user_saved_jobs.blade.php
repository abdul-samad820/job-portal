@extends('layouts.User_layout')
@section('title', 'Saved Jobs')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow border-0 rounded">
                <div class="card-body p-4">

                    <!-- Heading -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="sa-page-icon mr-3"><i class="fas fa-bookmark"></i></div>
                        <h4 class="font-weight-bold mb-0">Saved Jobs</h4>
                    </div>

                    <!-- Flash Messages -->
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif


                    @forelse($savedJobs as $saved)

                    @if(!$saved->job)
                    @continue
                    @endif

                    @php $job = $saved->job; @endphp

                    <div class="card mb-4 border-0 shadow-sm hover-card">
                        <div class="card-body">

                            <div class="row align-items-center">

                                <!-- Left Section -->
                                <div class="col-md-8">

                                    <h5 class="font-weight-bold mb-1">
                                        {{ $job->title }}
                                    </h5>

                                    <p class="text-muted mb-2">
                                        {{ $job->admin->company_name ?? 'Company Name' }}
                                    </p>

                                    <div class="text-muted small">

                                        <span class="mr-3">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                            {{ ucfirst($job->location) }}
                                        </span>

                                        <span class="mr-3">
                                            <i class="fas fa-briefcase text-primary mr-1"></i>
                                            {{ $job->type ?? 'N/A' }}
                                        </span>

                                        <span class="mr-3">
                                            <i class="fas fa-user-clock text-info mr-1"></i>
                                            {{ $job->experience ?? 'N/A' }}
                                        </span>

                                        <span>
                                            <i class="fas fa-rupee-sign text-success mr-1"></i>
                                            @if($job->min_salary && $job->max_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{
                                            number_format($job->max_salary / 100000, 1) }}L
                                            @elseif($job->min_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                                            @elseif($job->max_salary)
                                            Up to ₹{{ number_format($job->max_salary / 100000, 1) }}L
                                            @else
                                            Salary not disclosed
                                            @endif
                                        </span>

                                    </div>
                                </div>

                                <!-- Right Section -->
                                <div class="col-md-4 mt-3 mt-md-0">

                                    <div class="d-flex flex-wrap justify-content-md-end sa-saved-actions">

                                        <a href="{{ route('user.job_single', $job->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>

                                        <a href="{{ route('apply.from.saved', $job->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-paper-plane mr-1"></i>
                                            Apply Now
                                        </a>

                                        <!-- Remove -->
                                        <form method="POST" action="{{ route('saved.destroy', $job->id) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to remove this job?')"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                Remove
                                            </button>
                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    @empty

                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="far fa-bookmark fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold">No Saved Jobs</h5>
                        <p class="text-muted">
                            Start saving jobs you're interested in.
                        </p>

                        <a href="{{ route('user.jobs') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-briefcase mr-1"></i>
                            Browse Jobs
                        </a>
                    </div>

                    @endforelse

                </div>

                <div class="mt-3">
                    {{ $savedJobs->links() }}
                </div>
            </div>

        </div>
    </div>
</div>

@endsection