@extends('layouts.index')
@section('title', 'Selected Candidates')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
@endpush
@section('content')
    <div class="container-fluid py-4">

        <!-- PREMIUM HEADER -->
        <div class="p-4 rounded shadow-sm mb-4" style="background:#f8faff; border-left:5px solid #007bff;">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>
                    <h4 class="font-weight-bold mb-1 d-flex align-items-center">
                        <i class="fas fa-user-check text-success mr-2"></i>
                        Selected Candidates
                    </h4>
                    <small class="text-muted">
                        View all shortlisted and hired applicants.
                    </small>
                </div>

                <div class="badge badge-primary px-4 py-3">
                    {{ $selectedApplicants->count() }} Total
                </div>

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

                                                <img src="{{ $userImg }}" width="45" height="45"
                                                    class="rounded-circle mr-3" style="object-fit:cover;">

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
