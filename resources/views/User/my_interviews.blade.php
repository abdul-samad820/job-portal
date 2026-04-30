@extends('layouts.User_layout')
@section('content')

<div class="container mi-page">

    {{-- ── HEADER ── --}}
    <div class="mi-header d-flex align-items-center justify-content-between flex-wrap">

        <!-- LEFT -->
        <div class="d-flex align-items-center">
            <div class="mi-header-icon">
                <i class="fas fa-calendar-check"></i>
            </div>

            <div class="ml-3">
                <div class="mi-header-title">My Interviews</div>
                <div class="mi-header-sub">
                    Track your upcoming and past interview schedule
                </div>
            </div>
        </div>

        <!-- RIGHT STATS -->
        <div class="d-flex align-items-center mt-3 mt-md-0" style="gap:10px;">

            <div class="pill success">
                <i class="fas fa-circle mr-1"></i>
                {{ $upcoming->count() }} Upcoming
            </div>

            <div class="pill neutral">
                {{ $past->count() }} Past
            </div>

        </div>
    </div>

    {{-- ── UPCOMING INTERVIEWS ── --}}
    <div class="mb-5">
        <div class="mi-section-label upcoming">
            <i class="fas fa-circle" style="font-size:8px;"></i>
            Upcoming Interviews
        </div>

        @if($upcoming->isEmpty())
        <div class="mi-empty">
            <div class="mi-empty-icon">
                <i class="far fa-calendar-times"></i>
            </div>
            <p>No upcoming interviews scheduled yet.</p>
            <small class="text-muted" style="font-size:12px;">
                Apply for jobs to get interview calls!
            </small>
        </div>
        @else
        @foreach($upcoming as $interview)
        @php
        $s = $interview->status;
        $isOnline = $interview->mode === 'online';
        @endphp
        <div class="mi-card">
            <div class="mi-card-bar {{ $s }}"></div>

            <div class="mi-card-body">

                {{-- Top row --}}
                <div class="d-flex align-items-center flex-wrap mb-2" style="gap:8px;">
                    <span class="mi-badge {{ $s }}">
                        <i class="fas fa-{{ $s === 'scheduled' ? 'calendar-check' : ($s === 'rescheduled' ? 'sync-alt' : 'times') }}"></i>
                        {{ ucfirst($s) }}
                    </span>
                    <h6 class="mi-job-title">
                        {{ $interview->application->job->title }}
                    </h6>
                </div>

                {{-- Company --}}
                <p class="mi-company">
                    <i class="fas fa-building mr-1"></i>
                    {{ $interview->application->job->admin->company_name ?? 'N/A' }}
                </p>

                {{-- Meta pills --}}
                <div class="mi-meta">
                    <span class="mi-pill">
                        <i class="fas fa-calendar-day"></i>
                        {{ $interview->formatted_date_time }}
                    </span>
                    <span class="mi-pill">
                        <i class="fas fa-{{ $isOnline ? 'video' : 'map-marker-alt' }}"></i>
                        {{ ucfirst($interview->mode) }}
                    </span>
                    @if($interview->location)
                    @if($isOnline)
                    <a href="{{ $interview->location }}" target="_blank" class="mi-pill link">
                        <i class="fas fa-external-link-alt"></i>
                        Join Meeting
                    </a>
                    @else
                    <span class="mi-pill">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $interview->location }}
                    </span>
                    @endif
                    @endif
                </div>

                {{-- Notes --}}
                @if($interview->notes)
                <div class="mi-notes">
                    <i class="fas fa-sticky-note mr-1"></i>
                    {{ $interview->notes }}
                </div>
                @endif

            </div>

            {{-- Right: Time box --}}
            <div class="mi-time">
                <div class="mi-time-box upcoming">
                    <div class="mi-time-label">Interview in</div>
                    <div class="mi-time-value">
                        {{ $interview->interview_date->diffForHumans(null, true) }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    {{-- ── PAST INTERVIEWS ── --}}
    <div>
        <div class="mi-section-label past">
            <i class="fas fa-history" style="font-size:11px;"></i>
            Past Interviews
        </div>

        @if($past->isEmpty())
        <div class="mi-empty">
            <div class="mi-empty-icon">
                <i class="far fa-clock"></i>
            </div>
            <p>No past interviews found.</p>
        </div>
        @else
        @foreach($past as $interview)
        @php
        $s = $interview->status;
        $isOnline = $interview->mode === 'online';
        $timeBox = $s === 'cancelled' ? 'cancelled' : ($s === 'completed' ? 'completed' : 'past');
        $timeText = $s === 'cancelled' ? 'Cancelled' : ($s === 'completed' ? 'Completed' : $interview->interview_date->diffForHumans());
        @endphp
        <div class="mi-card" style="opacity:.85;">
            <div class="mi-card-bar {{ $s }}"></div>

            <div class="mi-card-body">

                <div class="d-flex align-items-center flex-wrap mb-2" style="gap:8px;">
                    <span class="mi-badge {{ $s }}">
                        <i class="fas fa-{{ $s === 'completed' ? 'check' : ($s === 'cancelled' ? 'times' : 'sync-alt') }}"></i>
                        {{ ucfirst($s) }}
                    </span>
                    <h6 class="mi-job-title">
                        {{ $interview->application->job->title }}
                    </h6>
                </div>

                <p class="mi-company">
                    <i class="fas fa-building mr-1"></i>
                    {{ $interview->application->job->admin->company_name ?? 'N/A' }}
                </p>

                <div class="mi-meta">
                    <span class="mi-pill">
                        <i class="fas fa-calendar-day"></i>
                        {{ $interview->formatted_date_time }}
                    </span>
                    <span class="mi-pill">
                        <i class="fas fa-{{ $isOnline ? 'video' : 'map-marker-alt' }}"></i>
                        {{ ucfirst($interview->mode) }}
                    </span>
                </div>

                @if($interview->notes)
                <div class="mi-notes">
                    <i class="fas fa-sticky-note mr-1"></i>
                    {{ $interview->notes }}
                </div>
                @endif

            </div>

            <div class="mi-time">
                <div class="mi-time-box {{ $timeBox }}">
                    <div class="mi-time-label">
                        {{ $s === 'completed' ? 'Status' : ($s === 'cancelled' ? 'Status' : 'Held') }}
                    </div>
                    <div class="mi-time-value">{{ $timeText }}</div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>

</div>
@endsection
