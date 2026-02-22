@extends('layouts.user_index')
@section('title', 'Saved Jobs')

@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-sm border-0 rounded">

                    <div class="card-body">

                        <!-- Heading -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="font-weight-bold mb-0">
                                <i class="fas fa-bookmark text-primary mr-2"></i>
                                Saved Jobs
                            </h4>
                        </div>

                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif


                        @forelse($savedJobs as $saved)
                            @php $job = $saved->job; @endphp

                            <div class="card mb-4 shadow-sm border-0">
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
                                                    {{ $job->salary ?? 'Not Disclosed' }} LPA
                                                </span>

                                            </div>

                                        </div>

                                        <!-- Right Section -->
                                        <div class="col-md-4 text-md-right mt-3 mt-md-0">

                                            <a href="{{ route('user.job_single', $job->id) }}"
                                                class="btn btn-primary btn-sm mr-2">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>

                                            <!-- Remove (DELETE) -->
                                            <form method="POST" action="{{ route('saved.destroy', $job->id) }}"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash-alt mr-1"></i>
                                                    Remove
                                                </button>
                                            </form>

                                        </div>

                                    </div>

                                </div>
                            </div>

                        @empty

                            <div class="text-center py-5">
                                <i class="far fa-bookmark fa-3x text-muted mb-3"></i>
                                <h5 class="font-weight-bold">No Saved Jobs</h5>
                                <p class="text-muted">Start saving jobs you're interested in.</p>

                                <a href="{{ route('user.jobs') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-briefcase me-1"> Browse Jobs
                                </a>
                            </div>
                        @endforelse

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
