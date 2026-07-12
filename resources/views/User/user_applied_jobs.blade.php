@extends('layouts.User_layout')
@section('title', 'Applied Jobs')

@section('content')

<div class="container py-4">

    @if (session('success'))
    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif
    @if (session('info'))
    <div class="alert alert-info shadow-sm border-0">{{ session('info') }}</div>
    @endif

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-briefcase"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Applied Jobs</h1>
                <small class="text-muted">Track the status of every job you've applied to.</small>
            </div>
        </div>
        <span class="badge badge-primary badge-pill px-3 py-2">
            {{ $applications->total() }} Total
        </span>
    </div>

    <!-- Search / Filter -->
    <form action="{{ route('user.job_applied') }}" method="GET" class="card border-0 shadow-sm rounded p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-7 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-left-0" placeholder="Search by job title or company...">
                </div>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                    <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block rounded-pill">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </div>
        @if (request()->filled('search') || request()->filled('status'))
        <div class="mt-2">
            <a href="{{ route('user.job_applied') }}" class="text-muted small">
                <i class="fas fa-times mr-1"></i> Clear filters
            </a>
        </div>
        @endif
    </form>

    <!-- Applications Card -->
    <div class="card border-0 shadow-sm rounded">

        @if ($applications->count() > 0)

        <div class="table-responsive p-3">
            <table class="table table-striped table-hover align-middle mb-0 sa-applied-table">

                <thead class="table-light">
                    <tr>
                        <th class="font-weight-bold">#</th>
                        <th class="font-weight-bold">Job Title</th>
                        <th class="font-weight-bold">Cover Letter</th>
                        <th class="font-weight-bold">Resume</th>
                        <th class="font-weight-bold">Status</th>
                        <th class="font-weight-bold">Applied On</th>
                        <th class="font-weight-bold">Review</th>
                        <th class="font-weight-bold">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($applications as $index => $application)
                    <tr>
                        <td class="font-weight-bold">{{ $index + 1 }}</td>

                        <td class="font-weight-bold text-primary">
                            {{ $application->job->title ?? 'N/A' }}
                        </td>

                        <td class="text-muted text-truncate u-maxw-280px">
                            {{ $application->cover_letter ?: '—' }}
                        </td>

                        <td class="text-center">
                            <a href="{{ asset('storage/' . $application->resume) }}" target="_blank"
                                class="sa-circle-btn sa-circle-btn--success" title="View Resume"
                                data-toggle="tooltip" data-placement="top">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>

                        <td>
                            @switch($application->status)
                            @case('pending')
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                            @break

                            @case('shortlisted')
                            <span class="badge bg-success px-3 py-2 rounded-pill">Shortlisted</span>
                            @break

                            @case('hired')
                            <span class="badge bg-primary px-3 py-2 rounded-pill">Hired</span>
                            @break

                            @case('rejected')
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Rejected</span>
                            @break

                            @default
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">Unknown</span>
                            @endswitch
                        </td>

                        <td class="text-muted text-nowrap">
                            <div class="sa-applied-date">
                                {{ $application->created_at ? $application->created_at->format('d M Y') : '—' }}
                            </div>
                            <a href="{{ route('application.timeline', $application->id) }}"
                                class="sa-timeline-link">
                                <i class="fas fa-stream"></i> View Timeline
                            </a>
                        </td>

                        <td>
                            @if ($application->status !== 'hired')
                            <span class="text-muted">—</span>
                            @elseif (! $application->testimonial)
                            <a href="{{ route('user.review.create', $application->id) }}"
                                class="btn btn-sm btn-outline-primary rounded-pill sa-review-btn">
                                <i class="fas fa-star"></i> Write a Review
                            </a>
                            @elseif ($application->testimonial->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Review Pending</span>
                            @elseif ($application->testimonial->status === 'approved')
                            <span class="badge bg-success px-3 py-2 rounded-pill">Review Published</span>
                            @else
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">Review Declined</span>
                            @endif
                        </td>

                        <td>
                            @if ($application->status === 'pending')
                            <form action="{{ route('application.withdraw', $application->id) }}" method="POST"
                                onsubmit="return confirm('Withdraw this application? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="fas fa-times mr-1"></i> Withdraw
                                </button>
                            </form>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-center p-3">
            {{ $applications->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="p-5 text-center text-muted">
            <i class="fas fa-folder-open fa-3x mb-3"></i>

            @if (request()->filled('search') || request()->filled('status'))
            <p class="fs-5 mb-3">No applications match your search.</p>
            <a href="{{ route('user.job_applied') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-times mr-1"></i> Clear Filters
            </a>
            @else
            <p class="fs-5 mb-3">You have not applied to any jobs yet.</p>
            <a href="{{ route('user.jobs') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-briefcase mr-1"></i> Browse Jobs
            </a>
            @endif
        </div>

        @endif

    </div>

</div>

@endsection