@extends('layouts.User_layout')
@section('title', 'Profile')
@section('content')
<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-user"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">My Profile</h1>
                <small class="text-muted">This is how your profile looks to companies and recruiters.</small>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-lg-4">

            <!-- PROFILE HEADER -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body text-center p-4">

                    @php
                    $userImg =
                    $profile && $profile->profile_image
                    ? Storage::url('user_profile/' . $profile->profile_image)
                    : asset('admins/dist/img/default.png');
                    $isOpenToWork = $profile->open_to_work ?? false;
                    // Computed locally — composer data from layouts.User_layout
                    // isn't available yet while this child view's own
                    // @section content is being rendered.
                    $navBadgeTier = \App\Services\BadgeLimitService::tierFor($user);
                    @endphp

                    <div class="mb-3 text-center">
                        <span class="position-relative d-inline-block">
                            <img src="{{ $userImg }}" class="rounded-circle border u-w-150px-h-150px-fit-cover"
                                style="border-width: 3px !important; border-color: var(--accent, #2563eb) !important;"
                                alt="{{ $user->name }}'s profile photo">
                            @if ($navBadgeTier)
                            @php
                                $profileBadgeMeta = [
                                    'bronze' => ['icon' => 'fa-medal', 'color' => '#cd7f32'],
                                    'silver' => ['icon' => 'fa-medal', 'color' => '#adadad'],
                                    'gold' => ['icon' => 'fa-trophy', 'color' => '#e6b800'],
                                ][$navBadgeTier];
                            @endphp
                            <span class="d-inline-flex align-items-center justify-content-center"
                                style="position:absolute; bottom:6px; right:6px; width:34px; height:34px;
                                    border-radius:50%; background: {{ $profileBadgeMeta['color'] }};
                                    border:3px solid #fff; box-shadow:0 2px 4px rgba(0,0,0,0.25);"
                                title="{{ ucfirst($navBadgeTier) }} Referrer">
                                <i class="fas {{ $profileBadgeMeta['icon'] }}" style="font-size:14px; color:#fff;"></i>
                            </span>
                            @endif
                        </span>
                    </div>

                    {{-- Name --}}
                    <h4 class="font-weight-bold text-dark mb-1">{{ $user->name }}</h4>

                    {{-- Designation --}}
                    <p class="text-muted small mb-2">
                        {{ $profile->designation ?? 'Professional Candidate' }}
                    </p>

                    {{-- Availability badge — reflects the same open_to_work
                         value as the toggle below, so the two never contradict --}}
                    @if ($isOpenToWork)
                    <span class="badge bg-success rounded-pill px-3 py-2 font-weight-bold mb-4">
                        <i class="fas fa-check-circle mr-1"></i> Open to Work
                    </span>
                    @else
                    <span class="badge bg-secondary rounded-pill px-3 py-2 font-weight-bold mb-4">
                        <i class="fas fa-moon mr-1"></i> Not Actively Looking
                    </span>
                    @endif

                    {{-- Open to Work toggle --}}
                    <form action="{{ route('user.open_to_work.toggle') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="custom-control custom-switch d-flex align-items-center justify-content-center">
                            <input type="checkbox" class="custom-control-input" id="openToWorkSwitch"
                                onchange="this.form.submit()" {{ $isOpenToWork ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold ml-2" for="openToWorkSwitch">
                                @if ($isOpenToWork)
                                <span class="text-success"><i class="fas fa-circle mr-1 u-fs-0-6rem"></i> Open to Work</span>
                                @else
                                <span class="text-muted"><i class="fas fa-circle mr-1 u-fs-0-6rem"></i> Not looking currently</span>
                                @endif
                            </label>
                        </div>
                    </form>

                    {{-- Buttons --}}
                    <a href="{{ route('user.add_profile') }}" class="btn btn-primary btn-block font-weight-bold rounded-pill">
                        <i class="fas fa-pen mr-2"></i> Update Profile
                    </a>

                </div>
            </div>

            <!-- CONTACT INFORMATION -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <strong><i class="fas fa-info-circle text-primary mr-2"></i>Details & Contact</strong>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-envelope text-primary mr-2"></i>
                        <strong class="mr-1">Email:</strong>
                        <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark text-truncate">
                            {{ $user->email }}
                        </a>
                    </li>

                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-phone text-primary mr-2"></i>
                        <strong class="mr-1">Phone:</strong>
                        {{ $user->phone ?: 'Not added' }}
                    </li>

                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                        <strong class="mr-1">Location:</strong>
                        {{ $user->address ?: 'Not added' }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-8">

            <!-- SUMMARY -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-align-left text-primary mr-2"></i> Professional Summary
                    </h6>

                    <p class="text-muted mb-0">
                        {{ optional($profile)->professional_summary ?: 'No summary added yet.' }}
                    </p>
                </div>
            </div>

            <!-- EXPERIENCE -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-briefcase text-primary mr-2"></i> Experience
                    </h6>

                    @php $experience = $profile->experience ?? []; @endphp

                    @if (!empty($experience))
                    @foreach ($experience as $exp)
                    <div class="mb-3 pl-3" style="border-left: 3px solid #22c55e;">
                        <h6 class="font-weight-bold mb-0">{{ $exp['role'] ?? '' }}</h6>
                        <p class="mb-1 text-primary">{{ $exp['company'] ?? '' }}</p>
                        <small class="text-muted d-block mb-1">{{ $exp['duration'] ?? '' }}</small>
                        <p class="mb-0 text-secondary">{{ $exp['description'] ?? '' }}</p>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">No experience added.</p>
                    @endif
                </div>
            </div>

            <!-- PROJECTS -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-project-diagram text-primary mr-2"></i> Projects
                    </h6>

                    @php $projects = $profile->projects ?? []; @endphp

                    @if (!empty($projects))
                    @foreach ($projects as $proj)
                    <div class="mb-3 pl-3" style="border-left: 3px solid #f59e0b;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <h6 class="font-weight-bold mb-0">{{ $proj['title'] ?? '' }}</h6>
                            @if (!empty($proj['link']))
                            <a href="{{ $proj['link'] }}" target="_blank" rel="noopener" class="small">
                                <i class="fas fa-external-link-alt mr-1"></i>View Project
                            </a>
                            @endif
                        </div>
                        @if (!empty($proj['tech']))
                        <small class="text-muted d-block mb-1">{{ $proj['tech'] }}</small>
                        @endif
                        <p class="mb-0 text-secondary">{{ $proj['description'] ?? '' }}</p>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">
                        No projects added yet.
                        <a href="{{ route('user.add_profile') }}">Add one</a> to strengthen your profile.
                    </p>
                    @endif
                </div>
            </div>

            <!-- SKILLS -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-tools text-primary mr-2"></i> Core Competencies & Skills
                    </h6>

                    <div class="d-flex flex-wrap">
                        @php
                        $skills = optional($profile)->core_skills ? explode(',', $profile->core_skills) : [];
                        @endphp

                        @if (!empty($skills))
                        @foreach ($skills as $skill)
                        @continue(trim($skill) === '')
                        <span class="badge bg-light text-dark border font-weight-bold text-uppercase mr-2 mb-2 px-3 py-2">
                            {{ trim($skill) }}
                        </span>
                        @endforeach
                        @else
                        <p class="text-muted m-0">No skills added yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- EDUCATION -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-graduation-cap text-primary mr-2"></i> Education
                    </h6>

                    @php $education = $profile->education ?? []; @endphp

                    @if (!empty($education))
                    @foreach ($education as $edu)
                    <div class="mb-3 pl-3" style="border-left: 3px solid var(--accent, #2563eb);">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <h6 class="font-weight-bold mb-0">{{ $edu['degree'] ?? 'Degree not provided' }}</h6>
                            <small class="text-muted font-weight-bold">{{ $edu['year'] ?? '—' }}</small>
                        </div>
                        <p class="mb-0 text-primary">{{ $edu['institute'] ?? 'Institute not provided' }}</p>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">No education added.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection