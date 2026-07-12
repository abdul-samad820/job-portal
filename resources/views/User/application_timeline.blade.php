@extends('layouts.User_layout')
@section('title', 'Application Timeline')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h3 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-stream mr-2 text-primary"></i> Application Timeline
        </h3>
        <a href="{{ route('user.job_applied') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Applied Jobs
        </a>
    </div>

    <div class="card border-0 shadow rounded mb-4">
        <div class="card-body p-4">
            <h5 class="font-weight-bold text-primary mb-1">{{ $application->job->title ?? 'N/A' }}</h5>
            <p class="text-muted mb-0">
                Applied on {{ $application->created_at->format('d M Y, h:i A') }}
            </p>
        </div>
    </div>

    <div class="card border-0 shadow rounded">
        <div class="card-body p-4">

            @if ($application->statusHistory->count() > 0)
            <ul class="list-unstyled mb-0">
                @foreach ($application->statusHistory as $entry)
                <li class="d-flex mb-4">
                    <div class="mr-3">
                        @php
                        $iconMap = [
                            'pending' => ['fa-clock', 'bg-warning text-dark'],
                            'shortlisted' => ['fa-check', 'bg-success text-white'],
                            'hired' => ['fa-trophy', 'bg-primary text-white'],
                            'rejected' => ['fa-times', 'bg-danger text-white'],
                        ];
                        [$icon, $badgeClass] = $iconMap[$entry->to_status] ?? ['fa-info', 'bg-secondary text-white'];
                        @endphp
                        <span
                            class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $badgeClass }} u-w-40px-h-40px">
                            <i class="fas {{ $icon }}"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 font-weight-bold text-dark">
                            @if ($entry->from_status)
                            Status changed: {{ ucfirst($entry->from_status) }} → {{ ucfirst($entry->to_status) }}
                            @else
                            Application submitted ({{ ucfirst($entry->to_status) }})
                            @endif
                        </p>
                        <small class="text-muted">{{ $entry->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted mb-0">No status history recorded yet.</p>
            @endif

        </div>
    </div>

</div>

@endsection
