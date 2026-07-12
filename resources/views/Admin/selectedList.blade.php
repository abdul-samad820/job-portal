@extends('layouts.Admin_layout')
@section('title', 'Selected Candidates')
@section('content')
<div class="container-fluid py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fa fa-user-check"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Selected Candidates</h1>
                <small class="text-muted">View all shortlisted and hired applicants.</small>
            </div>
        </div>
        <div class="d-flex align-items-center">
            @if ($selectedApplicants->isNotEmpty())
            <a href="{{ route('admin.selectedList.export') }}" class="btn btn-outline-success btn-sm mr-2">
                <i class="fas fa-file-csv mr-1"></i> Export CSV
            </a>
            @endif
            <span class="badge badge-primary badge-pill px-3 py-2">
                {{ $selectedApplicants->count() }} Total
            </span>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-4">

            @if ($selectedApplicants->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Selected Candidates Yet</h5>
                <p class="text-muted small">
                    Shortlisted or hired candidates will appear here.
                </p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Candidate</th>
                            <th>Email</th>
                            <th>Job Position</th>
                            <th>Status</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($selectedApplicants as $index => $app)
                        @php
                        $profile = $app->user->profile ?? null;
                        $userImg =
                        $profile && $profile->profile_image
                        ? asset('storage/user_profile/' . $profile->profile_image)
                        : asset('admins/dist/img/default.png');
                        @endphp

                        <tr>

                            <td>{{ $index + 1 }}</td>

                            <!-- Candidate -->
                            <td>
                                <div class="d-flex align-items-center">

                                    <img src="{{ $userImg }}" width="45" height="45" class="rounded-circle mr-3 u-fit-cover" alt="Applicant photo">

                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $app->user->name ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            Candidate
                                        </small>
                                    </div>

                                </div>
                            </td>

                            <!-- Email -->
                            <td class="text-muted">
                                {{ $app->user->email ?? 'N/A' }}
                            </td>

                            <!-- Job -->
                            <td>
                                <span class="font-weight-bold text-dark">
                                    {{ $app->job->title ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td>
                                @if ($app->status == 'hired')
                                <span class="status-badge hired">
                                    <i class="fas fa-check-circle"></i>
                                    Hired
                                </span>
                                @elseif($app->status == 'shortlisted')
                                <span class="status-badge shortlisted">
                                    <i class="fas fa-user-tie"></i>
                                    Shortlisted
                                </span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="text-muted">
                                {{ $app->created_at->format('d M Y') }}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @endif

        </div>
    </div>

</div>
@endsection