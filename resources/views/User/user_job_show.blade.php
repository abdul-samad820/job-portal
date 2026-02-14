@extends('layouts.app')
@section('title', 'Job Portal')

@section('content')

    <!-- HERO SEARCH HEADER -->
    <div class="w-100 py-5"
        style="margin-top:90px; 
            background: linear-gradient(90deg, #D9F0FF, #E8F7FF, #F3FBFF); 
            border-bottom:1px solid #d8e6f3;">

        <div class="container text-center">

            <h2 class="font-weight-bold mb-3" style="color:#1b3d6d;">
                Find a role that matches your ambition
            </h2>

            <p class="text-muted mb-4">
                Search thousands of curated openings across industries, expertise levels, and locations.
            </p>

            <!-- SEARCH BAR (submits basic filters) -->
            <form method="GET" action="{{ route('user.jobs.filter') }}">
                <div class="row justify-content-center">

                    <div class="col-md-3 col-6 mb-2">
                        <input type="text" name="search" class="form-control shadow-sm"
                            style="height:48px; font-size:14px;" placeholder="Job title or company"
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 col-6 mb-2">
                        <select name="category" class="form-control shadow-sm" style="height:48px; font-size:14px;">
                            <option value="">Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-6 mb-2">
                        <select name="role" class="form-control shadow-sm" style="height:48px; font-size:14px;">
                            <option value="">Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 col-3 mb-2">
                        <button class="btn btn-primary w-100 shadow-sm" style="height:48px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <!-- Keep current sort when top search submits -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                </div>
            </form>

        </div>
    </div>


    <!-- FILTER + JOBS -->
    <div class="container mt-5">
        <div class="row">

            <!-- FILTER SIDEBAR (Form submits all filter inputs) -->
            <div class="col-md-3">
                <form method="GET" action="{{ route('user.jobs.filter') }}">
                    <div class="p-4 shadow-sm" style="background:white; border-radius:18px; border:1px solid #e5eef6;">

                        <h5 class="font-weight-bold mb-3" style="color:#1b3d6d;">Filters</h5>

                        <hr>

                        <!-- Job Type -->
                        <h6 class="font-weight-bold">Job Type</h6>
                        @foreach (['Full-time', 'Part-time', 'Internship', 'Contract'] as $jt)
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" name="job_type[]"
                                    value="{{ $jt }}"
                                    {{ in_array($jt, (array) request('job_type', [])) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $jt }}</label>
                            </div>
                        @endforeach

                        <hr>

                        <!-- Experience -->
                        <h6 class="font-weight-bold">Experience</h6>
                        @foreach (['Fresher', '1 Year', '2 Years', '3 Years', '3+ Years'] as $exp)
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" name="experience[]"
                                    value="{{ $exp }}"
                                    {{ in_array($exp, (array) request('experience', [])) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $exp }}</label>
                            </div>
                        @endforeach

                        <hr>

                        <!-- Salary (min + max) -->
                        <h6 class="font-weight-bold">Salary (LPA)</h6>

                        <div class="d-flex align-items-center mb-2">
                            <input type="number" name="min_salary" id="minSalary" min="0"
                                value="{{ request('min_salary', 2) }}" class="form-control" style="max-width:90px;">
                            <span class="mx-2">-</span>
                            <input type="number" name="max_salary" id="maxSalary" min="0"
                                value="{{ request('max_salary', 20) }}" class="form-control" style="max-width:90px;">
                            <span class="ml-2 small">LPA</span>
                        </div>

                        <input type="range" min="2" max="50" id="salaryRange"
                            value="{{ request('max_salary', 20) }}" class="w-100 mt-2">

                        <small id="salaryLabel" class="text-muted"> {{ request('min_salary', 2) }} -
                            {{ request('max_salary', 20) }} LPA</small>

                        <hr>

                        <!-- Category (duplicate chooser for convenience) -->
                        <h6 class="font-weight-bold">Category</h6>
                        <select name="category" class="form-control mb-3">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category') == $cat->id ? 'selected' : 'cat' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Role -->
                        <h6 class="font-weight-bold">Role</h6>
                        <select name="role" class="form-control mb-3">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Keep sort param when filter form submits -->
                        <input type="hidden" name="sort" value="{{ request('sort') }}">

                        <button type="submit" class="btn btn-success w-100 mb-2">Apply Filters</button>
                        <a href="{{ route('user.jobs') }}" class="btn btn-danger w-100">Reset</a>
                    </div>
                </form>
            </div>


            <!-- JOBS LIST -->
            <div class="col-md-9">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark">
                        {{ $jobs->total() }} Jobs Found
                    </h5>

                    <form id="sortForm" method="GET" action="{{ route('user.jobs.filter') }}" class="m-0 p-0">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="role" value="{{ request('role') }}">
                        <input type="hidden" name="min_salary" value="{{ request('min_salary') }}">
                        <input type="hidden" name="max_salary" value="{{ request('max_salary') }}">

                        <select name="sort" class="form-control" style="max-width:220px;"
                            onchange="document.getElementById('sortForm').submit();">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="salary_low_high" {{ request('sort') == 'salary_low_high' ? 'selected' : '' }}>
                                Salary (Low → High)</option>
                            <option value="salary_high_low" {{ request('sort') == 'salary_high_low' ? 'selected' : '' }}>
                                Salary (High → Low)</option>
                        </select>
                    </form>
                </div>
                @forelse ($jobs as $job)
                    <div class="card shadow-sm border-0 rounded-lg mb-4 position-relative">
                        <div class="card-body p-4">

                            <!-- HEADER -->
                            <div class="d-flex justify-content-between align-items-start">

                                <!-- LEFT SIDE -->
                                <div>
                                    <h5 class="text-dark mb-1">{{ $job->title }}</h5>

                                    @if (isset($appliedJobIds) && in_array($job->id, $appliedJobIds))
                                        <span class="badge badge-success">Applied</span>
                                    @endif
                                </div>

                                <!-- BOOKMARK (TOP RIGHT) -->
                                <div>
                                    @if (isset($savedJobIds) && in_array($job->id, $savedJobIds))
                                        <a href="{{ route('user.unsave.job', $job->id) }}" title="Remove from Saved">
                                            <i class="fas fa-bookmark text-success" style="font-size:18px;"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('user.save.job', $job->id) }}" title="Save Job">
                                            <i class="far fa-bookmark text-muted" style="font-size:18px;"></i>
                                        </a>
                                    @endif
                                </div>

                            </div>

                            <!-- COMPANY -->
                            <p class="text-muted small mt-2 mb-2">
                                {{ $job->admin->company_name ?? 'Company' }}
                            </p>

                            <!-- JOB META -->
                            <div class="d-flex flex-wrap text-muted small mb-2">

                                <span class="mr-3">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                    {{ ucfirst($job->location) }}
                                </span>

                                <span class="mr-3">
                                    <i class="fas fa-rupee-sign text-success mr-1"></i>
                                    {{ $job->salary ?? 'Not Disclosed' }} LPA
                                </span>

                                <span class="mr-3">
                                    <i class="fas fa-user-clock text-info mr-1"></i>
                                    {{ $job->experience ?? 'N/A' }}
                                </span>

                                <span>
                                    <i class="fas fa-briefcase text-primary mr-1"></i>
                                    {{ $job->type ?? 'N/A' }}
                                </span>

                            </div>

                            <!-- DESCRIPTION -->
                            <p class="text-secondary small mt-3 mb-3" style="min-height:60px;">
                                {{ Str::limit($job->description, 120) }}
                            </p>

                            <!-- VIEW DETAILS (BOTTOM RIGHT) -->
                            <div class="text-right">
                                <a href="{{ route('user.job_single', $job->id) }}"
                                    class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                                    View Details
                                </a>
                            </div>

                        </div>

                        <!-- GRADIENT BOTTOM LINE -->
                        <div class="position-absolute"
                            style="bottom:0; left:0; right:0; height:4px; 
                background: linear-gradient(90deg, #007bff, #00d4ff); 
                border-radius:0 0 8px 8px;">
                        </div>

                    </div>

                @empty

                    <div class="alert alert-info text-center shadow-sm rounded-lg py-4">
                        <i class="fas fa-info-circle"></i> No matching jobs found.
                    </div>
                @endforelse

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <div class="shadow-sm p-2 rounded-pill bg-white">
                        {{ $jobs->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <!-- jQuery (Bootstrap 4 dependency) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(function() {
            // Salary slider <-> number inputs sync
            function updateSalaryLabel() {
                var min = parseInt($('#minSalary').val() || 2, 10);
                var max = parseInt($('#maxSalary').val() || $('#salaryRange').val() || 20, 10);
                $('#salaryLabel').text(min + ' - ' + max + ' LPA');

                // keep sort form hidden inputs in sync
                $('#sortMinSalary').val(min);
                $('#sortMaxSalary').val(max);
            }

            // when slider changes, update max input & label
            $('#salaryRange').on('input change', function() {
                var v = $(this).val();
                $('#maxSalary').val(v);
                updateSalaryLabel();
            });

            // when number inputs change, sync slider & label
            $('#minSalary').on('input change', function() {
                var min = parseInt($(this).val() || 2, 10);
                var max = parseInt($('#maxSalary').val() || $('#salaryRange').val() || 20, 10);
                if (min > max) {
                    // keep sane: don't allow min > max
                    $('#minSalary').val(max);
                }
                updateSalaryLabel();
            });

            $('#maxSalary').on('input change', function() {
                var max = parseInt($(this).val() || 2, 10);
                $('#salaryRange').val(max);
                updateSalaryLabel();
            });

            // initialize label on load
            updateSalaryLabel();

            // ensure sortForm picks fresh values if user changes filters without page reload (edge-case)
            $('#sortForm').on('submit', function() {
                $('#sortMinSalary').val($('#minSalary').val() || 2);
                $('#sortMaxSalary').val($('#maxSalary').val() || $('#salaryRange').val() || 20);

                // rebuild job_type & experience hidden inputs (remove old then add current)
                $(this).find('input[name="job_type[]"]').remove();
                $(this).find('input[name="experience[]"]').remove();

                $('input[name="job_type[]"]:checked').each(function() {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'job_type[]',
                        value: $(this).val()
                    }).appendTo('#sortForm');
                });
                $('input[name="experience[]"]:checked').each(function() {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'experience[]',
                        value: $(this).val()
                    }).appendTo('#sortForm');
                });
            });
        });
    </script>
@endpush
