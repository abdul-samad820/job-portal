@extends('layouts.User_layout')
@section('content')

<div class="card interview-card mb-3">
    <div class="card-body p-4">

        <div class="row align-items-center">

            {{-- LEFT --}}
            <div class="col-md-8">
                <div class="d-flex">

                    {{-- STATUS --}}
                    <span class="badge interview-badge status-{{ $interview->status }}">
                        {{ ucfirst($interview->status) }}
                    </span>

                    <div class="ml-3 w-100">

                        {{-- TITLE --}}
                        <h5 class="job-title mb-1">
                            {{ $interview->application->job->title }}
                        </h5>

                        {{-- COMPANY --}}
                        <p class="company mb-2">
                            <i class="fas fa-building mr-1"></i>
                            {{ $interview->application->job->admin->company_name ?? 'N/A' }}
                        </p>

                        {{-- META --}}
                        <div class="meta small">

                            <span>
                                <i class="far fa-calendar-alt"></i>
                                {{ $interview->formatted_date_time }}
                            </span>

                            <span>
                                <i class="fas fa-briefcase"></i>
                                {{ ucfirst($interview->mode) }}
                            </span>

                            @if($interview->location)
                            <span>
                                @if($interview->mode === 'online')
                                <i class="fas fa-video"></i>
                                Online
                                @else
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $interview->location }}
                                @endif
                            </span>
                            @endif

                        </div>

                        {{-- NOTES --}}
                        @if($interview->notes)
                        <div class="notes mt-3">
                            <i class="far fa-sticky-note mr-1"></i>
                            {{ $interview->notes }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-md-4 text-md-right mt-3 mt-md-0">

                {{-- COUNTDOWN --}}
                @if($interview->status === 'cancelled')
                <div class="countdown cancelled mb-2">
                    Cancelled
                </div>
                @elseif($interview->is_upcoming)
                <div class="countdown upcoming mb-2">
                    ⏰ {{ $interview->interview_date->diffForHumans() }}
                </div>
                @else
                <div class="countdown completed mb-2">
                    {{ $interview->interview_date->diffForHumans() }}
                </div>
                @endif

                {{-- BUTTON --}}
                @if($interview->mode === 'online' && $interview->location)
                <a href="{{ $interview->location }}" target="_blank" class="btn btn-primary btn-sm action-btn">
                    Join Meeting
                </a>
                @else
                <a href="#" class="btn btn-outline-primary btn-sm action-btn">
                    View Details
                </a>
                @endif

            </div>

        </div>

    </div>
</div>

@endsection