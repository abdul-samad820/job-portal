@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
@endpush
@section('content')
    <div class="position-relative" style="height:100vh; min-height:500px; width:100%; overflow:hidden;">
        <img src="{{ asset('admins/dist/img/group_image.jpg') }}" class="position-absolute w-100 h-100"
            style="object-fit: cover; object-position:center; top:0; left:0;">
        <div class="position-absolute w-100 h-100" style="top:0; left:0; background:rgba(255,255,255,0.45);"></div>
        <div class="hero-text position-absolute w-100 text-center px-3">
            <h1 class="font-weight-bold hero-title">
                Find your job without <br> any hassle.
            </h1>

            <p class="text-muted mt-3" style="font-size:16px;">
                Jobs & Job search. Find jobs in global companies. Executive jobs & work.
            </p>

        </div>
    </div>

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                Most Demanding Categories
            </h2>

        </div>
        <div style="margin-top:50px;"></div>

        <div class="coverflow-container">

            <span class="coverflow-arrow arrow-left">&lsaquo;</span>
            <span class="coverflow-arrow arrow-right">&rsaquo;</span>

            <div id="coverflowTrack" class="coverflow-track">

                @foreach ($all_categories as $cat)
                    <div class="coverflow-item">


                        <div class="p-2 text-center">
                            <img src="{{ $cat->category_image ? Storage::url($cat->category_image) : asset('default/category.png') }}"
                                style="width:55px; height:55px; object-fit:cover; border-radius:6px;" alt="Category Image">

                            <h6 class="mt-2 font-weight-bold" style="font-size:14px;">
                                {{ $cat->name }}
                            </h6>

                            <small class="text-muted">
                                {{ $cat->jobs_count }} Jobs
                            </small>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                New Job Listing
            </h2>

            <a href="{{ route('user.jobs') }}" class="btn-hire font-weight-bold text-nowrap">
                Explore all jobs →
            </a>

        </div>


        <div class="job-list-wrapper">
            @foreach ($recentJobs as $job)
                <div class="job-card">

                    <div class="row align-items-center">

                        <!-- Logo + Title -->
                        <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center mb-3 mb-lg-0">
                            <img src="{{ $job->job_image ? Storage::url($job->job_image) : asset('default/logo.png') }}"
                                class="job-img">

                            <div class="ml-3">
                                <h6 class="job-title mb-1">{{ $job->title }}</h6>
                                <span class="job-badge">{{ $job->type }}</span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-lg-3 col-md-6 col-6 text-md-center mb-2 mb-md-0">
                            <div class="job-meta">
                                {{ $job->created_at->format('d M Y') }}
                            </div>
                            <div class="job-location">
                                {{ $job->location }}
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-2 col-md-6 col-6 text-md-center">
                            <span class="job-category">
                                {{ $job->category->name ?? 'No Category' }}
                            </span>
                        </div>

                        <!-- Button -->
                        <div class="col-lg-2 col-md-12 text-lg-right text-md-center mt-3 mt-lg-0">
                            <a href="{{ route('apply_form_job_application', ['id' => $job->id]) }}"
                                class="btn-apply-modern">
                                Apply Now
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="py-5" style="background:#f8fbff;">
        <div class="container">
            <h2 class="font-weight-bold border-bottom border-primary pb-2 d-inline-block">
                Trusted by leading startups
            </h2>

            <div class="row">

                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                    <div class="testimonial-card">
                        <img src="{{ asset('admins/dist/img/girl-image.jpg') }}">
                        <h6>Gabbie</h6>
                        <small>Designer</small>
                        <p>Great platform for hiring.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                    <div class="testimonial-card">
                        <img src="{{ asset('admins/dist/img/user4-128x128.jpg') }}">
                        <h6>Sarah</h6>
                        <small>Team Lead</small>
                        <p>Improved our workflow.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                    <div class="testimonial-card">
                        <img src="{{ asset('admins/dist/img/user2-160x160.jpg') }}">
                        <h6>James</h6>
                        <small>Manager</small>
                        <p>Highly recommended tool.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4" data-animate>
                    <div class="testimonial-card">
                        <img src="{{ asset('admins/dist/img/user1-128x128.jpg') }}">
                        <h6>Alex</h6>
                        <small>HR</small>
                        <p>We use it daily.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const track = document.getElementById("coverflowTrack");
            const items = document.querySelectorAll(".coverflow-item");

            if (track && items.length > 0) {

                let index = Math.floor(items.length / 2);

                function updateCoverflow() {
                    items.forEach((item, i) => {
                        item.classList.remove("active", "left", "right");

                        if (i === index) item.classList.add("active");
                        else if (i < index) item.classList.add("left");
                        else item.classList.add("right");
                    });

                    const itemWidth = window.innerWidth < 768 ? 150 : 230;
                    const offset = -(index * itemWidth) + (window.innerWidth / 2 - itemWidth / 2);
                    track.style.transform = `translateX(${offset}px)`;
                }

                updateCoverflow();

                const leftBtn = document.querySelector(".arrow-left");
                const rightBtn = document.querySelector(".arrow-right");

                if (leftBtn) {
                    leftBtn.onclick = () => {
                        index = Math.max(0, index - 1);
                        updateCoverflow();
                    };
                }

                if (rightBtn) {
                    rightBtn.onclick = () => {
                        index = Math.min(items.length - 1, index + 1);
                        updateCoverflow();
                    };
                }

                setInterval(() => {
                    index = (index + 1) % items.length;
                    updateCoverflow();
                }, 3000);
            }

            /* ========== SCROLL ANIMATION ========== */

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("show");
                    }
                });
            }, {
                threshold: 0.2
            });

            document.querySelectorAll("[data-animate]").forEach(function(el) {
                observer.observe(el);
            });

        });
    </script>
@endpush
