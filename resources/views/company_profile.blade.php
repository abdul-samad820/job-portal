@extends('layouts.landing_page')
@section('title', $company->company_name)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($company->description ?? ($company->company_name.' is hiring on Job Hub. Browse open roles and follow for new job alerts.')), 155))
@section('meta_canonical', route('company.show', $company->slug))
@if($company->profile_image)
@section('meta_image', \Illuminate\Support\Facades\Storage::url('admins/' . $company->profile_image))
@endif

@push('schema')
@php
    $orgSchema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Organization',
        'name' => $company->company_name,
        'url' => route('company.show', $company->slug),
        'description' => $company->description,
    ];

    if ($company->profile_image) {
        $orgSchema['logo'] = \Illuminate\Support\Facades\Storage::url('admins/' . $company->profile_image);
    }

    if ($company->location) {
        $orgSchema['address'] = [
            '@type' => 'PostalAddress',
            'addressLocality' => $company->location,
            'addressCountry' => 'IN',
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<div class="u-bg-f5f8ff-pad-40px-0">
    <div class="container u-mt-130px">
        <div class="main-wrapper p-4 p-md-5">

            <!-- COMPANY HERO -->
            <div class="job-hero mb-5">
                <div class="d-flex align-items-center flex-wrap">
                    <div class="company-logo-wrap mr-3 mb-2">
                        <img src="{{ $company->profile_image
                                ? \Illuminate\Support\Facades\Storage::url('admins/' . $company->profile_image)
                                : asset('admins/dist/img/default.png') }}"
                            class="company-logo-img" alt="{{ $company->company_name }} logo"
                            onerror="this.onerror=null;this.src='{{ asset('admins/dist/img/default.png') }}';">
                    </div>
                    <div class="mb-2">
                        <h2 class="job-title mb-1">
                            {{ $company->company_name }}
                            @if ($company->is_verified)
                            <i class="fas fa-check-circle text-primary" title="Verified Company"></i>
                            @endif
                        </h2>
                        <div>
                            @if ($company->location)
                            <span class="meta-badge"><i class="fa fa-map-marker-alt mr-1"></i>{{ $company->location }}</span>
                            @endif
                            <span class="meta-badge"><i class="fa fa-briefcase mr-1"></i>{{ $totalActiveJobs }} active job{{ $totalActiveJobs === 1 ? '' : 's' }}</span>
                            <span class="meta-badge"><i class="fa fa-users mr-1"></i>{{ $followerCount }} follower{{ $followerCount === 1 ? '' : 's' }}</span>
                        </div>
                    </div>

                    <div class="ml-md-auto mb-2">
                        @auth('user')
                        <form method="POST" action="{{ route('user.following.toggle', $company->id) }}">
                            @csrf
                            <button type="submit" class="btn {{ $isFollowing ? 'btn-outline-primary' : 'btn-primary' }}">
                                <i class="fas {{ $isFollowing ? 'fa-check' : 'fa-plus' }} mr-1"></i>
                                {{ $isFollowing ? 'Following' : 'Follow Company' }}
                            </button>
                        </form>
                        @else
                        <a href="{{ route('user.login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus mr-1"></i> Follow Company
                        </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LEFT: About + Jobs -->
                <div class="col-lg-8">

                    @if ($company->description)
                    <div class="section-card">
                        <h5 class="section-title">About {{ $company->company_name }}</h5>
                        <p class="text-muted">{{ $company->description }}</p>
                    </div>
                    @endif

                    @if ($company->expertise)
                    <div class="section-card">
                        <h5 class="section-title">Specialization</h5>
                        @foreach (explode(',', $company->expertise) as $item)
                        @if (trim($item) !== '')
                        <span class="skill-tag">{{ trim($item) }}</span>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    <div class="section-card">
                        <h5 class="section-title">Open Positions</h5>

                        @forelse ($activeJobs as $job)
                        <div class="job-card-premium mb-3">
                            <div class="card-body">
                                <a href="{{ route('user.job_single', $job->id) }}" class="job-title-link mb-1 d-inline-block text-decoration-none">
                                    {{ $job->title }}
                                </a>
                                <div class="job-meta-row mt-2">
                                    <span class="job-meta-pill">
                                        <i class="fas fa-map-marker-alt text-danger"></i> {{ ucfirst($job->location) }}
                                    </span>
                                    <span class="job-meta-pill">
                                        <i class="fas fa-briefcase text-secondary"></i> {{ $job->type }}
                                    </span>
                                    <span class="job-meta-pill">
                                        <i class="fas fa-rupee-sign text-success"></i>
                                        @if($job->min_salary && $job->max_salary)
                                        ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{ number_format($job->max_salary / 100000, 1) }}L
                                        @elseif($job->min_salary)
                                        ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                                        @else
                                        Not disclosed
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted mb-0">No active job openings right now — check back soon.</p>
                        @endforelse

                        {{ $activeJobs->links() }}
                    </div>

                    @if ($testimonials->isNotEmpty())
                    <div class="section-card">
                        <h5 class="section-title">What people say</h5>
                        <div class="row">
                            @foreach ($testimonials as $t)
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded h-100">
                                    <p class="text-muted mb-2">&ldquo;{{ $t->review }}&rdquo;</p>
                                    <p class="mb-0 font-weight-bold small">{{ $t->name }}</p>
                                    @if ($t->designation)
                                    <p class="mb-0 text-muted small">{{ $t->designation }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                <!-- RIGHT: Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-card-v2">
                        <h5 class="section-title mb-3">Company Snapshot</h5>
                        <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-map-marker-alt mr-2"></i>{{ $company->location ?? 'Location not specified' }}</li>
                            <li class="mb-2"><i class="fas fa-briefcase mr-2"></i>{{ $totalActiveJobs }} active job{{ $totalActiveJobs === 1 ? '' : 's' }}</li>
                            <li class="mb-2"><i class="fas fa-users mr-2"></i>{{ $followerCount }} follower{{ $followerCount === 1 ? '' : 's' }}</li>
                            @if ($company->is_verified)
                            <li class="mb-2 text-primary"><i class="fas fa-check-circle mr-2"></i>Verified company</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
