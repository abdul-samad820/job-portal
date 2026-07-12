@extends('layouts.User_layout')
@section('title', 'Resume Score')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Resume / ATS Score</h1>
                <small class="text-muted">A heuristic score based on your profile content — the same signals an ATS (Applicant Tracking
            System) and most recruiters skim for.</small>
            </div>
        </div>
    </div>

    @if (session('error'))
    <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded mb-4">
        <div class="card-body p-5 text-center">
            @php
            $ringColor = $score >= 75 ? '#22c55e' : ($score >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="mx-auto mb-3 u-w-140px-h-140px position-relative"
                style="background: conic-gradient({{ $ringColor }} {{ $score * 3.6 }}deg, #e5e7eb 0deg); border-radius: 50%; display:flex; align-items:center; justify-content:center;">
                <div class="bg-white rounded-circle u-w-110px-h-110px d-flex align-items-center justify-content-center shadow-sm">
                    <h2 class="font-weight-bold mb-0" style="color: {{ $ringColor }}; font-size: 2.4rem;">{{ $score }}</h2>
                </div>
            </div>
            <p class="text-muted mb-0 font-weight-semibold">out of 100</p>
        </div>
    </div>

    @if ($job)
    <div class="card border-0 shadow-sm rounded mb-4">
        <div class="card-body p-4">
            <h6 class="font-weight-bold mb-2">Match for: {{ $job->title }}</h6>
            @if ($matchPercent !== null)
            <h3 class="font-weight-bold text-primary">{{ $matchPercent }}%</h3>
            <p class="text-muted small mb-2">Keyword match with required skills</p>

            @if (count($matchedSkills) > 0)
            <p class="mb-1 small"><strong>Matched:</strong>
                @foreach ($matchedSkills as $s)
                <span class="badge bg-success mr-1">{{ $s }}</span>
                @endforeach
            </p>
            @endif

            @if (count($missingSkills) > 0)
            <p class="mb-0 small"><strong>Missing:</strong>
                @foreach ($missingSkills as $s)
                <span class="badge bg-danger mr-1">{{ $s }}</span>
                @endforeach
            </p>
            @endif
            @else
            <p class="text-muted small mb-0">This job doesn't list specific required skills to compare against.</p>
            @endif
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded">
        <div class="card-body p-4">
            <h6 class="font-weight-bold mb-3">Breakdown & Suggestions</h6>

            @foreach ($breakdown as $item)
            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex justify-content-between">
                    <span class="font-weight-semibold">{{ $item['label'] }}</span>
                    <span class="text-muted">{{ $item['points'] }} / {{ $item['max'] }}</span>
                </div>
                <div class="progress u-h-6px my-2">
                    <div class="progress-bar bg-primary"
                        style="width: {{ $item['max'] > 0 ? ($item['points'] / $item['max']) * 100 : 0 }}%">
                    </div>
                </div>
                <small class="text-muted">{{ $item['tip'] }}</small>
            </div>
            @endforeach

            <a href="{{ route('user.profile') }}" class="btn btn-primary rounded-pill mt-2">
                <i class="fas fa-edit mr-1"></i> Update Profile
            </a>
        </div>
    </div>

</div>

@endsection