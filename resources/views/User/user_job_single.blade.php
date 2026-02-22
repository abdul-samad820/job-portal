@extends('layouts.app')
@section('title', 'Job Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
@endpush

@section('content')
    <div style="background:#f5f8ff; padding:40px 0;">
        <div class="container" style="margin-top:130px;">
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
                            {{ $singlejob->salary }} LPA
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
                    <div class="col-lg-4">

                        <div class="sidebar-card text-center">

                            <img src="{{ $singlejob->admin->profile_image
                                ? Storage::url('admins/' . $singlejob->admin->profile_image)
                                : asset('admins/dist/img/default.png') }}"
                                class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;">

                            <h5 class="font-weight-bold text-primary">
                                {{ $singlejob->admin->company_name ?? 'Not specified' }}
                            </h5>

                            <hr>

                            <div class="row text-left small mb-3">
                                <div class="col-6 mb-2">
                                    <strong>Experience</strong><br>
                                    {{ $singlejob->experience }}
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Salary</strong><br>
                                    ₹ {{ $singlejob->salary }} LPA
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Location</strong><br>
                                    {{ $singlejob->location }}
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Job Type</strong><br>
                                    {{ $singlejob->type }}
                                </div>
                            </div>

                            <span class="meta-badge d-inline-block mb-3">
                                {{ $singlejob->role->name }}
                            </span>

                            <a href="{{ route('apply_form_job_application', ['id' => $singlejob->id]) }}"
                                class="btn apply-btn btn-block text-secondary shadow-sm">
                                Apply Now
                            </a>

                        </div>

                    </div>

                </div>

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

                    <div id="relatedCarousel" class="carousel slide" data-ride="carousel" data-interval="3500"
                        data-pause="hover">

                        <div class="carousel-inner">

                            @foreach ($jobs->chunk(3) as $index => $chunk)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <div class="row">

                                        @foreach ($chunk as $job)
                                            <div class="col-md-4 mb-4">
                                                <div class="card h-100 shadow-sm border-0">

                                                    <div class="card-body">

                                                        <span class="badge badge-light text-primary mb-2">
                                                            {{ $job->type }}
                                                        </span>

                                                        <h6 class="font-weight-bold">
                                                            {{ $job->title }}
                                                        </h6>

                                                        <p class="text-success font-weight-bold mb-1">
                                                            ₹ {{ $job->salary }} LPA
                                                        </p>

                                                        <p class="text-muted small mb-3">
                                                            <i class="fa fa-map-marker-alt mr-1"></i>
                                                            {{ $job->location }}
                                                        </p>

                                                        <a href="{{ route('user.job_single', $job->id) }}"
                                                            class="btn btn-outline-primary btn-sm btn-block">
                                                            View Job
                                                        </a>

                                                    </div>

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
