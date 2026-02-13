@extends('layouts.user_index')
@section('title', 'Profile')
@section('content')
    <div class="container my-5">
        <div class="row">

            <!-- LEFT COLUMN -->
            <div class="col-lg-4">

                <!-- PROFILE HEADER -->
                <div class="card rounded-4 shadow-lg bg-white mb-4">
                    <div class="card-body text-center p-5">

                        {{-- Profile Image --}}
                        <div class="mb-3 text-center">

                            @php
                                $userImg =
                                    $profile && $profile->profile_image
                                        ? asset('uploads/user_profile/' . $profile->profile_image)
                                        : asset('admins/dist/img/default.png');
                            @endphp

                            <img src="{{ $userImg }}" class="rounded-circle border border-3 border-primary shadow-sm"
                                style="width:150px; height:150px; object-fit:cover;">

                        </div>


                        {{-- Name --}}
                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>

                        {{-- Designation (Dynamic if added later) --}}
                        <p class="text-muted small mb-2">
                            {{ $profile->designation ?? 'Professional Candidate' }}
                        </p>

                        {{-- Badge --}}
                        <span class="badge bg-success bg-opacity-75 rounded-pill px-3 py-2 fw-semibold mb-4">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified & Available
                        </span>

                        {{-- Stats Row --}}


                        {{-- Buttons --}}
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.add_profile') }}" class="btn btn-primary btn-sm fw-bold rounded-3">
                                <i class="fa-solid fa-pen-to-square me-2"></i> Update Profile
                            </a>

                        </div>

                    </div>
                </div>

                <!-- CONTACT INFORMATION -->
                <div class="card rounded-3 shadow-lg mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="mb-0 text-dark fw-bold ">
                            <i class="bi bi-info-circle ms-3 text-primary"></i> Details & Contact
                        </h5>
                    </div>

                    <li class="list-group-item d-flex align-items-center">
                        <strong>
                            <i class="bi bi-envelope text-primary"></i> Email:
                        </strong>
                        <a href="#" class="text-decoration-none text-dark ms-2">
                            {{ $user->email }}
                        </a>
                    </li>


                    <li class="list-group-item">
                        <strong><i class="bi bi-telephone me-2 text-primary"></i> Phone:</strong>
                        {{ $user->phone }}
                    </li>

                    <li class="list-group-item">
                        <strong><i class="bi bi-geo-alt me-2 text-primary"></i> Location:</strong>
                        {{ $user->address }}
                    </li>

                    <li class="list-group-item">
                        <strong><i class="bi bi-linkedin me-2 text-primary"></i> LinkedIn:</strong>
                        <a href="#" target="_blank" class="text-decoration-none text-dark">/in/janedoe</a>
                    </li>
                    </ul>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-8">

                <!-- SUMMARY -->
                <div class="card rounded-3 shadow-lg mb-4 p-4">
                    <h5 class="card-title text-dark border-bottom pb-2 mb-3 fw-bold">
                        <i class="bi bi-file-text me-2 text-primary"></i> Professional Summary
                    </h5>

                    <p class="text-muted lead">
                        {{ optional($profile)->professional_summary ?? 'No summary added yet.' }}
                    </p>

                </div>

                <div class="card rounded-3 shadow-lg mb-4 p-4">

                    <h5 class="card-title text-dark border-bottom pb-2 mb-3 fw-bold">
                        <i class="bi bi-briefcase-fill text-primary me-2"></i> Experience
                    </h5>

                    @php
                        $experience = trim($profile->experience ?? '');
                        $lines = $experience ? explode("\n", $experience) : [];
                    @endphp

                    @if (!empty($lines))
                        <div class="p-3 bg-light rounded border shadow-sm">

                            {{-- MAIN EXPERIENCE (Line 1) --}}
                            <div class="mb-3">
                                <span class="badge bg-primary text-white px-3 py-2" style="font-size:14px;">
                                    <i class="bi bi-clock-history me-1"></i>
                                    {{ $lines[0] }}
                                </span>
                            </div>

                            {{-- JOB DETAILS (From Line 2+) --}}
                            <div class="mt-2">

                                @foreach ($lines as $i => $line)
                                    @if ($i > 0)
                                        <div class="d-flex mb-2">
                                            <i class="bi bi-dot text-primary"
                                                style="font-size: 1.8rem; line-height: 1;"></i>
                                            <p class="mb-0 text-secondary" style="font-size: 15px; font-weight: 500;">
                                                {{ trim($line) }}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach

                            </div>

                        </div>
                    @else
                        <p class="text-muted">No experience added.</p>
                    @endif

                </div>



                <!-- SKILLS -->
                <div class="card rounded-3 shadow-lg mb-4 p-4">
                    <h5 class="card-title text-dark border-bottom pb-2 mb-3 fw-bold">
                        <i class="bi bi-tools me-2 text-primary"></i> Core Competencies & Skills
                    </h5>

                    <div class="d-flex flex-wrap">
                        @if (!empty(optional($profile)->core_skills))
                            @foreach (explode(',', optional($profile)->core_skills) as $skill)
                                <span
                                    class="badge text-primary bg-light border border-info p-2 px-3 fw-semibold rounded-pill m-1">
                                    {{ trim($skill) }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-muted m-0">No skills added yet.</p>
                        @endif
                    </div>

                </div>

                <!-- EDUCATION -->
                <div class="card rounded-3 shadow-lg mb-4 p-4">
                    <h5 class="card-title text-dark border-bottom pb-2 mb-3 fw-bold">
                        <i class="bi bi-mortarboard me-2 text-primary"></i> Education
                    </h5>

                    @php
                        $education = $profile && $profile->education ? json_decode($profile->education, true) : [];
                    @endphp

                    @if (!empty($education))
                        @foreach ($education as $edu)
                            <div class="mb-3 border-start border-primary border-4 ps-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bolder text-dark mb-0">
                                        {{ $edu['degree'] ?? 'Degree not provided' }}
                                    </h6>

                                    <small class="text-muted fw-semibold">
                                        {{ $edu['year'] ?? '—' }}
                                    </small>
                                </div>

                                <p class="mb-1 text-primary">
                                    {{ $edu['institute'] ?? 'Institute not provided' }}
                                </p>

                                <small class="text-muted">
                                    {{ $edu['specialization'] ?? '' }}
                                </small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No education added.</p>
                    @endif
                </div>


            </div>
        </div>
    </div>
@endsection
