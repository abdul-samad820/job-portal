@extends('layouts.User_layout')
@section('title', 'Resume Builder')

@section('content')

<div class="container py-4">

    <div class="sa-page-header d-print-none">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-file-alt"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Resume Builder</h1>
            </div>
        </div>
        <div>
            <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary btn-sm rounded-pill mr-2">
                <i class="fas fa-edit mr-1"></i> Edit Profile
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-print mr-1"></i> Print / Save as PDF
            </button>
        </div>
    </div>

    <div class="alert alert-info d-print-none shadow-sm border-0 mb-4">
        This resume is generated from your profile. Update your <a href="{{ route('user.profile') }}">profile</a>
        to change what appears here, then hit "Print / Save as PDF".
    </div>

    <div class="d-print-none mb-3">
        <span class="font-weight-semibold mr-2">Template:</span>
        <a href="{{ route('user.resume_builder', ['template' => 'classic']) }}"
            class="btn btn-sm rounded-pill mr-1 {{ $template === 'classic' ? 'btn-primary' : 'btn-outline-secondary' }}">Classic</a>
        @if ($modernUnlocked)
        <a href="{{ route('user.resume_builder', ['template' => 'modern']) }}"
            class="btn btn-sm rounded-pill {{ $template === 'modern' ? 'btn-primary' : 'btn-outline-secondary' }}">Modern</a>
        @else
        <button type="button" class="btn btn-sm rounded-pill btn-outline-secondary" disabled
            title="Unlock at 10+ referrals (Bronze badge)">
            <i class="fas fa-lock mr-1"></i> Modern
        </button>
        <small class="text-muted d-block mt-1">
            <i class="fas fa-medal mr-1"></i> Refer 10 friends to unlock the Modern template —
            <a href="{{ route('user.referrals') }}">see your progress</a>.
        </small>
        @endif
    </div>

    <div id="resumeSheet" class="bg-white shadow rounded p-5 mx-auto resume-tpl-{{ $template }}" style="max-width: 850px;">

        <!-- Header -->
        <div class="text-center border-bottom pb-3 mb-4">
            <h2 class="font-weight-bold mb-1">{{ $user->name }}</h2>
            <p class="text-muted mb-0">
                {{ $user->email }}
                @if ($user->phone) &nbsp;|&nbsp; {{ $user->phone }} @endif
                @if ($user->address) &nbsp;|&nbsp; {{ $user->address }} @endif
            </p>
        </div>

        <!-- Summary -->
        @if ($profile && $profile->professional_summary)
        <div class="mb-4">
            <h5 class="font-weight-bold text-primary border-bottom pb-1 mb-2">Professional Summary</h5>
            <p class="mb-0">{{ $profile->professional_summary }}</p>
        </div>
        @endif

        <!-- Skills -->
        @if (count($skills) > 0)
        <div class="mb-4">
            <h5 class="font-weight-bold text-primary border-bottom pb-1 mb-2">Skills</h5>
            <p class="mb-0">
                @foreach ($skills as $skill)
                <span class="badge badge-light border mr-1 mb-1">{{ $skill }}</span>
                @endforeach
            </p>
        </div>
        @endif

        <!-- Experience -->
        @if (count($experience) > 0)
        <div class="mb-4">
            <h5 class="font-weight-bold text-primary border-bottom pb-1 mb-2">Experience</h5>
            @foreach ($experience as $exp)
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <strong>{{ $exp['role'] ?? '' }} @if(!empty($exp['company'])) — {{ $exp['company'] }} @endif</strong>
                    <span class="text-muted small">{{ $exp['duration'] ?? '' }}</span>
                </div>
                @if (!empty($exp['description']))
                <p class="mb-0 text-muted small">{{ $exp['description'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Education -->
        @if (count($education) > 0)
        <div class="mb-4">
            <h5 class="font-weight-bold text-primary border-bottom pb-1 mb-2">Education</h5>
            @foreach ($education as $edu)
            <div class="mb-2 d-flex justify-content-between">
                <span>
                    <strong>{{ $edu['degree'] ?? '' }}</strong>
                    @if(!empty($edu['institute'])) — {{ $edu['institute'] }} @endif
                </span>
                <span class="text-muted small">{{ $edu['year'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Projects -->
        @if (count($projects) > 0)
        <div class="mb-4">
            <h5 class="font-weight-bold text-primary border-bottom pb-1 mb-2">Projects</h5>
            @foreach ($projects as $proj)
            <div class="mb-2">
                <strong>{{ $proj['name'] ?? ($proj['title'] ?? '') }}</strong>
                @if (!empty($proj['description']))
                <p class="mb-0 text-muted small">{{ $proj['description'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @if (empty($profile?->professional_summary) && count($skills) === 0 && count($experience) === 0 && count($education) === 0)
        <div class="text-center text-muted py-5">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <p class="mb-0">Your profile is empty. <a href="{{ route('user.profile') }}">Fill it out</a> to generate your resume.</p>
        </div>
        @endif

    </div>

</div>

<style>
    @media print {
        .u-navbar-wrap, .main-sidebar, .content-header, footer, .d-print-none {
            display: none !important;
        }
        #resumeSheet {
            box-shadow: none !important;
            max-width: 100% !important;
        }
    }

    /* Modern template: accent side bar + different header style */
    .resume-tpl-modern {
        border-left: 6px solid #2b6dd6;
    }
    .resume-tpl-modern h2 {
        color: #2b6dd6;
        letter-spacing: 0.5px;
    }
    .resume-tpl-modern h5 {
        border-bottom: 2px solid #2b6dd6 !important;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }
</style>

@endsection
