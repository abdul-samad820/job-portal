@extends('layouts.Admin_layout')

@section('title', 'Search Results')

@section('content')

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fa fa-search"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Search Results</h1>
                @if ($q !== '')
                    <small class="text-muted">Showing results for "{{ $q }}"</small>
                @else
                    <small class="text-muted">Use the search bar at the top to find jobs and applicants.</small>
                @endif
            </div>
        </div>
    </div>

@if ($q === '')
    <div class="text-muted">Type something above to search your jobs and applicants.</div>
@else

    <h5 class="font-weight-bold mb-3">Jobs matching "{{ $q }}" ({{ $jobs->count() }})</h5>
    @if ($jobs->isEmpty())
        <p class="text-muted mb-4">No matching jobs found.</p>
    @else
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Last Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                        <td>{{ $job->title }}</td>
                        <td>{{ $job->location }}</td>
                        <td>{{ ucfirst($job->type) }}</td>
                        <td>{{ $job->last_date ? \Carbon\Carbon::parse($job->last_date)->format('d M Y') : '-' }}</td>
                        <td><a href="{{ route('admin.job_edit', $job->id) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h5 class="font-weight-bold mb-3">Applicants matching "{{ $q }}" ({{ $applicants->count() }})</h5>
    @if ($applicants->isEmpty())
        <p class="text-muted">No matching applicants found.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Applied For</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applicants as $application)
                    <tr>
                        <td>{{ $application->user->name ?? 'N/A' }}</td>
                        <td>{{ $application->user->email ?? 'N/A' }}</td>
                        <td>{{ $application->job->title ?? 'N/A' }}</td>
                        <td>{{ ucfirst($application->status) }}</td>
                        <td><a href="{{ route('job_application') }}" class="btn btn-sm btn-outline-primary">View in list</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endif

@endsection