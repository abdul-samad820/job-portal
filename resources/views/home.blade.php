@extends('layouts.app')

@section('content')
    <style>
        .coverflow-container {
            perspective: 900px;
            overflow: hidden;
            width: 100%;
            position: relative;
            padding: 60px 0;
        }

        .coverflow-track {
            display: flex;
            justify-content: center;
            transition: transform .4s ease;
            transform-style: preserve-3d;
        }

        .coverflow-item {
            width: 180px;
            /* a bit bigger */
            margin: 0 25px;
            /* MORE SPACE BETWEEN CARDS */
            text-align: center;
            transition: transform .4s ease, opacity .4s ease;
        }

        .coverflow-item.active {
            transform: scale(1.25) translateZ(70px);
        }

        .coverflow-item.left {
            transform: rotateY(30deg) scale(.9);
            opacity: .6;
        }

        .coverflow-item.right {
            transform: rotateY(-30deg) scale(.9);
            opacity: .6;
        }

        .coverflow-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 34px;
            /* bigger arrows */
            cursor: pointer;
            z-index: 10;
            color: #444;
            padding: 10px;
        }

        .arrow-left {
            left: -10px;
        }

        /* PULL INSIDE */
        .arrow-right {
            right: -10px;
        }

        .testimonial-card {
    background: #d9ecff;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    height: 100%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    transition: 0.3s;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
}

.testimonial-card img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}


    </style>

    <!-- HERO SECTION -->
    <div class="position-relative" style="height:100vh; min-height:500px; width:100%; overflow:hidden;">

        <!-- Fullscreen Responsive Image -->
        <img src="{{ asset('admins/dist/img/group_image.jpg') }}" class="position-absolute w-100 h-100"
            style="object-fit: cover; object-position:center; top:0; left:0;">

        <!-- Light Overlay -->
        <div class="position-absolute w-100 h-100" style="top:0; left:0; background:rgba(255,255,255,0.45);"></div>

        <!-- HERO TEXT -->
        <div class="position-absolute w-100 text-center px-3" style="top:50%; left:0; transform:translateY(-50%); z-index:5;">

            <h1 class="font-weight-bold" style="font-size: clamp(32px, 5vw, 62px); line-height:1.2;">
                Find your job without <br> any hassle.
            </h1>

            <p class="text-muted mt-3" style="font-size:16px;">
                Jobs & Job search. Find jobs in global companies. Executive jobs & work.
            </p>

        </div>

    </div>

    <!-- ============================ -->
    <!-- MOST DEMANDING CATEGORIES    -->
    <!-- ============================ -->

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold">Most Demanding Categories</h2>
        </div>
        <div style="margin-top:50px;"></div>

        <div class="coverflow-container">

            <span class="coverflow-arrow arrow-left">&lsaquo;</span>
            <span class="coverflow-arrow arrow-right">&rsaquo;</span>

            <div id="coverflowTrack" class="coverflow-track">

                @foreach ($all_categories as $cat)
                    <div class="coverflow-item">
                        <div class="p-2">
                            <img src="{{ $cat->category_image ? asset('uploads/categories/' . $cat->category_image) : asset('default/category.png') }}"
                                style="width:55px; height:55px; object-fit:cover; border-radius:6px;">
                            <h6 class="mt-2 font-weight-bold" style="font-size:14px;">{{ $cat->name }}</h6>
                            <small class="text-muted">{{ $cat->jobs_count }} Jobs</small>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>




        <!-- ============================ -->
        <!-- NEW JOB LISTING              -->
        <!-- ============================ -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold">New Job Listing</h2>
            <a href="{{ route('user.jobs') }}" class="btn-hire font-weight-bold">Explore all jobs →</a>
        </div>

        <div class="job-list-box">
            @foreach ($recentJobs as $job)
                <div
                    class="job-row d-flex align-items-center justify-content-between flex-wrap mb-4 py-3 px-2 shadow-sm rounded">

                    <!-- Logo + Title -->
                    <div class="d-flex align-items-center col-md-4 col-12 mb-2">
                        <div class="job-logo mr-3">
                            <img src="{{ $job->job_image ? asset('uploads/job/' . $job->job_image) : asset('default/logo.png') }}"
                                width="65" height="65" style=" border-radius: 8px;">
                        </div>

                        <div>
                            <h6 class="font-weight-bold mb-1">{{ $job->title }}</h6>
                            <small class="text-muted">{{ $job->type }}</small>
                        </div>
                    </div>

                    <!-- Date + Location -->
                    <div class="col-md-4 col-12 text-md-center mb-2">
                        <small class="text-muted">
                            {{ $job->created_at->format('d M Y') }} • {{ $job->location }}
                        </small>
                    </div>

                    <!-- Category -->
                    <div class="col-md-2 col-6 text-md-center">
                        <small class="font-weight-bold">
                            {{ $job->category->name ?? 'No Category' }}
                        </small>
                    </div>

                    <!-- Apply Button -->
                    <div class="col-md-2 col-6 text-right">
                        <a href="{{ route('apply_form_job_application', ['id' => $job->id]) }}" class="apply-btn">
                            APPLY
                        </a>
                    </div>

                </div>
            @endforeach

        </div>

    </div>

   <div class="container my-5">
    <h2 class="text-center font-weight-bold mb-5">
        Trusted by leading startups
    </h2>

    <div id="testimonialCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">

        <div class="carousel-inner">

            <!-- ITEM -->
            <div class="carousel-item active">
                <div class="row">

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/girl-image.jpg') }}">
                            <h6>Gabbie</h6>
                            <small>Designer</small>
                            <p>Great platform for hiring.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/user4-128x128.jpg') }}">
                            <h6>Sarah</h6>
                            <small>Team Lead</small>
                            <p>Improved our workflow.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/user2-160x160.jpg') }}">
                            <h6>James</h6>
                            <small>Manager</small>
                            <p>Highly recommended tool.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/user1-128x128.jpg') }}">
                            <h6>Alex</h6>
                            <small>HR</small>
                            <p>We use it daily.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SECOND ITEM -->
            <div class="carousel-item">
                <div class="row">

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/team1.jpg') }}">
                            <h6>Elsa</h6>
                            <small>CEO</small>
                            <p>Boosted productivity.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/user7-128x128.jpg') }}">
                            <h6>Lisa</h6>
                            <small>Manager</small>
                            <p>Very smooth experience.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/team2.jpg') }}">
                            <h6>Ned</h6>
                            <small>Lead</small>
                            <p>Hiring made simple.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="testimonial-card">
                            <img src="{{ asset('admins/dist/img/team3.jpg') }}">
                            <h6>Mike</h6>
                            <small>Designer</small>
                            <p>Great UI and support.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- <!-- Controls -->
        <a class="carousel-control-prev" href="#testimonialCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
        </a>

        <a class="carousel-control-next" href="#testimonialCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
        </a> --}}

    </div>
</div>



    <!-- ========== Fade-Up Animation (JS inline) ========== -->
@endsection
<script>
    document.addEventListener("DOMContentLoaded", () => {

        const track = document.getElementById("coverflowTrack");
        const items = document.querySelectorAll(".coverflow-item");

        let index = Math.floor(items.length / 2);

        function updateCoverflow() {
            items.forEach((item, i) => {
                item.classList.remove("active", "left", "right");

                if (i === index) item.classList.add("active");
                else if (i < index) item.classList.add("left");
                else item.classList.add("right");
            });

            const offset = -(index * 230) + (window.innerWidth / 2 - 120);
            track.style.transform = `translateX(${offset}px)`;
        }

        updateCoverflow();

        document.querySelector(".arrow-left").onclick = () => {
            index = Math.max(0, index - 1);
            updateCoverflow();
        };
        document.querySelector(".arrow-right").onclick = () => {
            index = Math.min(items.length - 1, index + 1);
            updateCoverflow();
        };

        setInterval(() => {
            index = (index + 1) % items.length;
            updateCoverflow();
        }, 3000);

    });

    (function() {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) {
                    e.target.style.opacity = "1";
                    e.target.style.transform = "translateY(0)";
                }
            });
        }, {
            threshold: 0.15
        });

        document.querySelectorAll("[data-animate]").forEach(el => io.observe(el));

        // Accordion arrows
        $('#acc').on('show.bs.collapse', e => {
            $(e.target).prev().find('.arrow').text('▴');
        });
        $('#acc').on('hide.bs.collapse', e => {
            $(e.target).prev().find('.arrow').text('▾');
        });
    })();
</script>
