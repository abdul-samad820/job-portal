@extends('layouts.User_layout')
@section('title', 'Compare Jobs')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-balance-scale"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Compare Jobs</h1>
            </div>
        </div>
        <a href="{{ route('user.jobs') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left mr-1"></i> Back to Jobs
        </a>
    </div>

    @if ($jobs->count() < 2)
    <div class="alert alert-warning shadow-sm border-0">
        Select at least 2 jobs from the listing page to compare them side by side.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th class="font-weight-bold" style="width: 180px;">Criteria</th>
                    @foreach ($jobs as $job)
                    <th class="font-weight-bold">
                        <a href="{{ route('user.job_single', $job->id) }}">{{ $job->title }}</a>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-weight-semibold">Company</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->admin->company_name ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Location</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->location ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Salary Range</td>
                    @foreach ($jobs as $job)
                    <td>
                        @if ($job->min_salary || $job->max_salary)
                        ₹{{ number_format($job->min_salary) }} – ₹{{ number_format($job->max_salary) }} LPA
                        @else
                        Not disclosed
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Job Type</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->type ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Experience Needed</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->experience ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Category</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->category->name ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Required Skills</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->required_skills ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Application Deadline</td>
                    @foreach ($jobs as $job)
                    <td>{{ $job->last_date ? $job->last_date->format('d M Y') : 'Open' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="font-weight-semibold">Action</td>
                    @foreach ($jobs as $job)
                    <td>
                        <a href="{{ route('user.job_single', $job->id) }}" class="btn btn-primary btn-sm rounded-pill">
                            View & Apply
                        </a>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    @endif

</div>

@endsection
