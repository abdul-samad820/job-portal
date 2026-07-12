@extends('layouts.landing_page')
@section('title', $singlejob->title . ' at ' . ($singlejob->admin->company_name ?? 'a company in ' . $singlejob->location))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($singlejob->overview ?? $singlejob->description), 155))
@section('meta_canonical', route('user.job_single', $singlejob->id))
@if($singlejob->job_image)
@section('meta_image', \Illuminate\Support\Facades\Storage::url($singlejob->job_image))
@endif

@push('schema')
@php
    $employmentTypeMap = [
        'Full-time' => 'FULL_TIME',
        'Part-time' => 'PART_TIME',
        'Internship' => 'INTERN',
        'Contract' => 'CONTRACTOR',
    ];

    $jobPosting = [
        '@context' => 'https://schema.org/',
        '@type' => 'JobPosting',
        'title' => $singlejob->title,
        'description' => $singlejob->description ?? $singlejob->overview,
        'identifier' => [
            '@type' => 'PropertyValue',
            'name' => 'Job Hub',
            'value' => (string) $singlejob->id,
        ],
        'datePosted' => $singlejob->created_at->toDateString(),
        'employmentType' => $employmentTypeMap[$singlejob->type] ?? 'OTHER',
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => $singlejob->admin->company_name ?? 'Job Hub Employer',
        ],
        'jobLocation' => [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $singlejob->location,
                'addressCountry' => 'IN',
            ],
        ],
    ];

    if ($singlejob->admin->slug ?? null) {
        $jobPosting['hiringOrganization']['sameAs'] = route('company.show', $singlejob->admin->slug);
    }

    if ($singlejob->last_date) {
        $jobPosting['validThrough'] = $singlejob->last_date->toIso8601String();
    }

    if ($singlejob->min_salary || $singlejob->max_salary) {
        $jobPosting['baseSalary'] = [
            '@type' => 'MonetaryAmount',
            'currency' => 'INR',
            'value' => [
                '@type' => 'QuantitativeValue',
                'minValue' => (float) ($singlejob->min_salary ?? $singlejob->max_salary),
                'maxValue' => (float) ($singlejob->max_salary ?? $singlejob->min_salary),
                'unitText' => 'YEAR',
            ],
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($jobPosting, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<div class="u-bg-f5f8ff-pad-40px-0">
    <div class="container u-mt-130px">
        <div class="main-wrapper p-4 p-md-5">

            <!-- HERO -->
            <div class="job-hero mb-5">

                <p class="text-muted small mb-2">
                    Posted {{ $singlejob->created_at->diffForHumans() }}
                </p>

                <h2 class="job-title mb-3">
                    {{ $singlejob->title }}
                </h2>

                <div>
                    <span class="meta-badge">
                        <i class="fa fa-map-marker-alt mr-1"></i>
                        {{ $singlejob->location }}
                    </span>

                    <span class="meta-badge">
                        <i class="fa fa-briefcase mr-1"></i>
                        {{ $singlejob->type }}
                    </span>

                    <span class="meta-badge text-success font-weight-bold">
                        @if($singlejob->min_salary && $singlejob->max_salary)
                        ₹{{ number_format($singlejob->min_salary / 100000, 1) }}L - ₹{{
                        number_format($singlejob->max_salary / 100000, 1) }}L
                        @elseif($singlejob->min_salary)
                        ₹{{ number_format($singlejob->min_salary / 100000, 1) }}L+
                        @elseif($singlejob->max_salary)
                        Up to ₹{{ number_format($singlejob->max_salary / 100000, 1) }}L
                        @else
                        Salary not disclosed
                        @endif
                    </span>
                </div>

            </div>

            <div class="row">

                <!-- LEFT -->
                <div class="col-lg-8">

                    <div class="section-card">
                        <h5 class="section-title">Overview</h5>
                        <p class="text-muted">
                            {{ $singlejob->overview ?? 'No overview available.' }}
                        </p>
                    </div>

                    <div class="section-card">
                        <h5 class="section-title">Job Description</h5>
                        <p class="text-muted">
                            {{ $singlejob->description }}
                        </p>
                    </div>

                    <div class="section-card">
                        <h5 class="section-title">Responsibilities</h5>

                        @if ($singlejob->responsibilities)
                        <ul class="responsibility-list mt-3">
                            @foreach (explode(',', $singlejob->responsibilities) as $item)
                            @if (trim($item) !== '')
                            <li>{{ trim($item) }}</li>
                            @endif
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted">Not provided</p>
                        @endif
                    </div>

                    <div class="section-card">
                        <h5 class="section-title">Required Skills</h5>

                        @if ($singlejob->required_skills)
                        @foreach (explode(',', $singlejob->required_skills) as $item)
                        @if (trim($item) !== '')
                        <span class="skill-tag">
                            {{ trim($item) }}
                        </span>
                        @endif
                        @endforeach
                        @else
                        <p class="text-muted">Not provided</p>
                        @endif
                    </div>

                </div>

                <!-- RIGHT SIDEBAR -->
                <!-- RIGHT SIDEBAR -->
                <div class="col-lg-4">

                    <div class="sidebar-card-v2">

                        <div class="text-center">
                            <div class="company-logo-wrap mx-auto mb-3">
                                @if (optional($singlejob->admin)->slug)
                                <a href="{{ route('company.show', $singlejob->admin->slug) }}">
                                @endif
                                <img src="{{ $singlejob->admin->profile_image
                                        ? Storage::url('admins/' . $singlejob->admin->profile_image)
                                        : asset('admins/dist/img/default.png') }}" class="company-logo-img" alt="{{ $singlejob->admin->company_name ?? 'Company' }} logo" onerror="this.onerror=null;this.src='{{ asset('admins/dist/img/default.png') }}';">
                                @if (optional($singlejob->admin)->slug)
                                </a>
                                @endif
                            </div>

                            <h5 class="sidebar-company-name mb-1">
                                @if (optional($singlejob->admin)->slug)
                                <a href="{{ route('company.show', $singlejob->admin->slug) }}" class="text-dark text-decoration-none">{{ $singlejob->admin->company_name ?? 'Not specified' }}</a>
                                @else
                                {{ $singlejob->admin->company_name ?? 'Not specified' }}
                                @endif
                                @if (optional($singlejob->admin)->is_verified)
                                <i class="fas fa-check-circle text-primary" title="Verified Company"></i>
                                @endif
                            </h5>

                            @auth('user')
                            @php
                            $isFollowing = \App\Models\CompanyFollow::where('user_id', auth('user')->id())
                            ->where('admin_id', $singlejob->admin_id)->exists();
                            @endphp
                            <form action="{{ route('user.following.toggle', $singlejob->admin_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm rounded-pill px-4 {{ $isFollowing ? 'btn-outline-secondary' : 'btn-outline-primary' }}">
                                    @if ($isFollowing)
                                    <i class="fas fa-check mr-1"></i> Following
                                    @else
                                    <i class="fas fa-plus mr-1"></i> Follow Company
                                    @endif
                                </button>
                            </form>
                            @endauth
                        </div>

                        <div class="sidebar-divider"></div>

                        <div class="sidebar-info-grid">
                            <div class="sidebar-info-item">
                                <i class="fas fa-user-clock"></i>
                                <div>
                                    <span class="sidebar-info-label">Experience</span>
                                    <span class="sidebar-info-value">{{ $singlejob->experience }}</span>
                                </div>
                            </div>
                            <div class="sidebar-info-item">
                                <i class="fas fa-rupee-sign"></i>
                                <div>
                                    <span class="sidebar-info-label">Salary</span>
                                    <span class="sidebar-info-value">
                                        @if($singlejob->min_salary && $singlejob->max_salary)
                                        ₹{{ number_format($singlejob->min_salary / 100000, 1) }}L - ₹{{
                                        number_format($singlejob->max_salary / 100000, 1) }}L
                                        @elseif($singlejob->min_salary)
                                        ₹{{ number_format($singlejob->min_salary / 100000, 1) }}L+
                                        @elseif($singlejob->max_salary)
                                        Up to ₹{{ number_format($singlejob->max_salary / 100000, 1) }}L
                                        @else
                                        Not disclosed
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="sidebar-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <span class="sidebar-info-label">Location</span>
                                    <span class="sidebar-info-value">{{ $singlejob->location }}</span>
                                </div>
                            </div>
                            <div class="sidebar-info-item">
                                <i class="fas fa-briefcase"></i>
                                <div>
                                    <span class="sidebar-info-label">Job Type</span>
                                    <span class="sidebar-info-value">{{ $singlejob->type }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-divider"></div>

                        <div class="text-center mb-3">
                            <span class="meta-badge d-inline-block mb-0">
                                {{ $singlejob->role->name }}
                            </span>
                        </div>

                        <a href="{{ route('apply_form_job_application', ['id' => $singlejob->id]) }}" class="btn-apply-modern btn-block-apply justify-content-center">
                            Apply Now
                        </a>

                        @auth('user')
                        <div class="sidebar-links-row">
                            <a href="{{ route('user.resume_score', $singlejob->id) }}" class="sidebar-link">
                                <i class="fas fa-clipboard-check mr-1"></i>Check ATS Match
                            </a>
                            <span class="sidebar-link-sep">•</span>
                            <button type="button" class="sidebar-link sidebar-link-btn" data-toggle="modal" data-target="#reportJobModal">
                                <i class="fas fa-flag mr-1"></i>Report this job
                            </button>
                        </div>
                        @endauth
                    </div>

                </div>
            </div>

            @auth('user')
            <!-- Report Job Modal -->
            <div class="modal fade" id="reportJobModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('jobs.report', $singlejob->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Report this job</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Reason</label>
                                    <select name="reason" class="form-control" required>
                                        <option value="">Select a reason</option>
                                        @foreach(\App\Models\JobReport::REASONS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Additional details (optional)</label>
                                    <textarea name="details" class="form-control" rows="3" maxlength="1000"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            <!-- RELATED JOBS -->
            <div class="related-wrap mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold mb-0">
                        Related Jobs
                    </h3>
                    <div>
                        <a href="#relatedCarousel" data-slide="prev" class="btn btn-light btn-sm mr-2">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        <a href="#relatedCarousel" data-slide="next" class="btn btn-light btn-sm">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                <div id="relatedCarousel" class="carousel slide" data-ride="carousel" data-interval="3500" data-pause="hover">

                    <div class="carousel-inner">

                        @foreach ($jobs->chunk(3) as $index => $chunk)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="row">

                                @foreach ($chunk as $job)
                                <div class="col-md-4 mb-4">
                                    <div class="related-job-card h-100">

                                        <span class="related-job-badge mb-2">
                                            {{ $job->type }}
                                        </span>

                                        <h6 class="related-job-title">
                                            {{ $job->title }}
                                        </h6>

                                        <p class="related-job-salary mb-1">
                                            @if($job->min_salary && $job->max_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{
                                            number_format($job->max_salary / 100000, 1) }}L
                                            @elseif($job->min_salary)
                                            ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                                            @elseif($job->max_salary)
                                            Up to ₹{{ number_format($job->max_salary / 100000, 1) }}L
                                            @else
                                            Salary not disclosed
                                            @endif
                                        </p>

                                        <p class="related-job-location mb-3">
                                            <i class="fa fa-map-marker-alt mr-1"></i>
                                            {{ $job->location }}
                                        </p>

                                        <a href="{{ route('user.job_single', $job->id) }}" class="related-job-btn">
                                            View Job
                                        </a>

                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    </div>

    @endsection
