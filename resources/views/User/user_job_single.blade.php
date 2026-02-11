@extends('layouts.app')
@section('title', 'Job Details')

@section('content')

    <style>
        /* LIGHT BLUE MAIN THEME */
        .light-card {
            background: #F4F8FF !important;
            border: 1px solid #d9e6ff !important;
            border-radius: 12px;
        }

        .section-title {
            color: #2b4eff !important;
            font-weight: 600 !important;
        }

        .blue-badge {
            background: #e9f1ff;
            border: 1px solid #c7d9ff;
            color: #3056d3;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .apply-btn-blue {
            background: #2b4eff;
            border-color: #2b4eff;
            color: #fff;
        }

        .apply-btn-blue:hover {
            background: #1d3bcc;
            border-color: #1d3bcc;
        }

        /* RELATED JOBS SECTION */
        .related-wrap {
            background: #eaf5ff;
            border-radius: 16px;
            padding: 40px 20px;
        }

        .arrow-btn {
            background: white;
            border: 1px solid #d0d8e8;
            padding: 8px 14px;
            border-radius: 50%;
            color: #2b4eff;
            transition: 0.2s ease-in-out;
        }

        .arrow-btn:hover {
            background: #2b4eff;
            color: white;
        }

        /* Job Card (Jobi Style) */
        .job-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #dde7f3;
            padding: 22px;
            transition: 0.3s ease-in-out;
            height: 100%;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.08);
            border-color: #b6d4f0;
        }

        .ribbon-badge {
            background: #dff1ff;
            color: #0077c8;
            font-size: 12px;
            padding: 5px 12px;
            font-weight: 600;
            border-radius: 6px;
            display: inline-block;
        }

        .job-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 10px;
            color: #1a1f36;
        }

        .salary-text {
            color: #00a651;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .location-text {
            color: #6c7583;
            font-size: 14px;
        }
    </style>


    <div class="container" style="margin-top:130px;">
        <div class="p-4 p-md-5 bg-white shadow rounded" style="border-radius:16px;">

            <div class="row">

                <!-- LEFT CONTENT -->
                <div class="col-lg-8">

                    <p class="text-muted small mb-1">{{ $singlejob->created_at->format('d M Y') }}</p>

                    <h2 class="font-weight-bold section-title">{{ $singlejob->title }}</h2>

                    <!-- Overview -->
                    <div class="p-4 mb-4 light-card shadow-sm">
                        <h5 class="section-title mb-3">
                            <i class="fas fa-info-circle text-primary mr-2"></i> Overview
                        </h5>
                        <p class="text-muted">{{ $singlejob->overview ?? 'No overview available.' }}</p>
                    </div>

                    <!-- Description -->
                    <div class="p-4 mb-4 light-card shadow-sm">
                        <h5 class="section-title mb-3">
                            <i class="fas fa-briefcase text-primary mr-2"></i> Job Description
                        </h5>
                        <p class="text-muted">{{ $singlejob->description }}</p>
                    </div>

                    <!-- Responsibilities -->
                    <div class="p-4 mb-4 light-card shadow-sm">
                        <h5 class="section-title mb-3">
                            <i class="fas fa-check-circle text-primary mr-2"></i> Responsibilities
                        </h5>
                        @if ($singlejob->responsibilities)
                            <ul class="text-muted mt-2">
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

                    <!-- Skills -->
                    <div class="p-4 mb-4 light-card shadow-sm">
                        <h5 class="section-title mb-3">
                            <i class="fas fa-star text-warning mr-2"></i> Required Skills
                        </h5>
                        @if ($singlejob->required_skills)
                            <ul class="text-muted mt-2">
                                @foreach (explode(',', $singlejob->required_skills) as $item)
                                    @if (trim($item) !== '')
                                        <li>{{ trim($item) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Not provided</p>
                        @endif

                    </div>

                </div>

                <!-- RIGHT SIDEBAR -->
                <div class="col-lg-4">
                    <div class="p-4 light-card shadow-sm">

                        <div class="text-center mb-3">
                            <img src="{{ $singlejob->admin->profile_image
                                ? asset('uploads/admins/' . $singlejob->admin->profile_image)
                                : asset('admins/dist/img/default.png') }}"
                                class="img-circle elevation-2" style="width:40px; height:40px; object-fit:cover;">


                            <h5 class="font-weight-bold text-primary">
                                {{ $singlejob->admin->company_name ?? 'Not specified' }}
                            </h5>

                            <button class="btn btn-primary btn-sm rounded-pill">Visit website</button>
                        </div>

                        <div class="row small mb-3">
                            <div class="col-6">
                                <p><strong>Salary</strong></p>
                                <p class="text-muted">{{ $singlejob->salary }} LPA</p>
                            </div>

                            <div class="col-6">
                                <p><strong>Experience</strong></p>
                                <p class="text-muted">{{ $singlejob->experience }}</p>
                            </div>

                            <div class="col-6">
                                <p><strong>Location</strong></p>
                                <p class="text-muted">{{ $singlejob->location }}</p>
                            </div>

                            <div class="col-6">
                                <p><strong>Job Type</strong></p>
                                <p class="text-muted">{{ $singlejob->type }}</p>
                            </div>

                            <div class="col-6">
                                <p><strong>Date</strong></p>
                                <p class="text-muted">{{ $singlejob->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <span class="badge blue-badge">{{ $singlejob->role->name }}</span>

                        <a href="{{ route('apply_form_job_application', ['id' => $singlejob->id]) }}"
                            class="btn apply-btn-blue btn-block rounded-pill shadow-sm mt-3">
                            Apply Now
                        </a>

                    </div>
                </div>

            </div>


            <!-- RELATED JOBS -->
            <div class="related-wrap my-5">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">Related Jobs</h3>
                    <div>
                        <a class="arrow-btn mr-2" href="#jobSlider" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
                        <a class="arrow-btn" href="#jobSlider" data-slide="next"><i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>

                <div id="jobSlider" class="carousel slide" data-ride="carousel" data-interval="2500">
                    <div class="carousel-inner">

                        @foreach ($jobs->chunk(3) as $index => $chunk)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="row">

                                    @foreach ($chunk as $job)
                                        <div class="col-md-4 mb-4">
                                            <div class="job-card">

                                                <span class="ribbon-badge">{{ $job->type }}</span>

                                                <h5 class="job-title">{{ $job->title }}</h5>

                                                <p class="salary-text text-primary">{{ $job->salary }} LPA</p>

                                                <p class="location-text">{{ $job->location }}</p>

                                                <a href="{{ route('user.job_single', $job->id) }}"
                                                    class="btn btn-primary btn-sm px-4 mt-2">
                                                    Apply
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

        </div> <!-- white box end -->

    </div> <!-- container end -->


@endsection
