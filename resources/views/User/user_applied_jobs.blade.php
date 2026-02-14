@extends('layouts.user_index')
@section('title', 'Applied Jobs')

@section('content')

    <div class="container py-4">

        <!-- Header -->
        <div class="bg-white shadow-sm rounded p-4 mb-4" style="border-left:5px solid #007bff;">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <h3 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                    Applied Jobs
                </h3>

                <span class="badge badge-primary px-4 py-2" style="font-size:14px; border-radius:30px;">
                    Total Applications: {{ $applications->count() }}
                </span>

            </div>

        </div>

        <!-- Applications Card -->
        <div class="card border-0 shadow rounded-4">

            @if ($applications->count() > 0)

                <div class="table-responsive p-3">
                    <table class="table table-striped table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">#</th>
                                <th class="fw-semibold">Job Title</th>
                                <th class="fw-semibold">Cover Letter</th>
                                <th class="fw-semibold">Resume</th>
                                <th class="fw-semibold">Status</th>
                                <th class="fw-semibold">Applied On</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($applications as $index => $application)
                                <tr>
                                    <td class="fw-semibold">{{ $index + 1 }}</td>

                                    <td class="fw-semibold text-primary">
                                        {{ $application->job->title ?? 'N/A' }}
                                    </td>

                                    <td class="text-muted text-truncate" style="max-width: 280px;">
                                        {{ $application->cover_letter ?: '—' }}
                                    </td>

                                    <td>
                                        <a href="{{ asset('storage/' . $application->resume) }}" target="_blank"
                                            class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="fas fa-file-pdf me-1"></i> View
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

                                    <td class="text-muted">
                                        {{ $application->created_at ? $application->created_at->format('d M Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                    <p class="fs-5 mb-3">You have not applied to any jobs yet.</p>

                    <a href="{{ route('user.jobs') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-briefcase me-1"></i> Browse Jobs
                    </a>
                </div>

            @endif

        </div>

    </div>

@endsection
