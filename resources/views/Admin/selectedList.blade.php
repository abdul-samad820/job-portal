@extends('layouts.index')
@section('title', 'Selected List')

@section('content')
    <div class="container-fluid py-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Selected Applicants</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-white border rounded px-3 py-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Selected List</li>
                </ol>
            </nav>
        </div>

        <!-- Main Card -->
        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">

                @if ($selectedApplicants->isEmpty())
                    <div class="alert alert-info text-center py-3 mb-0">
                        <i class="fa fa-info-circle mr-1"></i> No selected applicants found.
                    </div>
                @else
                    <!-- Table -->
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($selectedApplicants as $index => $app)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td class="font-weight-bold text-dark">
                                            {{ $app->user->name ?? 'N/A' }}
                                        </td>

                                        <td>{{ $app->user->email ?? 'N/A' }}</td>

                                        <td>{{ $app->job->title ?? 'N/A' }}</td>

                                        <td>
                                            @if ($app->status == 'hired')
                                                <span class="badge badge-success px-3 py-2">Hired</span>
                                            @elseif($app->status == 'shortlisted')
                                                <span class="badge badge-warning px-3 py-2 text-dark">Shortlisted</span>
                                            @endif
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
