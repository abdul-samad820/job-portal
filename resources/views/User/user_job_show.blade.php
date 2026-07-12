@extends('layouts.landing_page')
@section('title', 'Browse Jobs')
@section('meta_description', 'Browse the latest job openings on Job Hub across every category, location, and experience level. Find and apply to your next role today.')
@section('meta_canonical', route('user.jobs'))
@if(request()->query())
@section('meta_robots', 'noindex, follow')
@endif

@section('content')

@if (!empty($isRecommended))
<div class="container u-mt-90px">
    <div class="alert alert-primary d-flex justify-content-between align-items-center shadow-sm rounded-lg mb-0">
        <span>
            <i class="fas fa-lightbulb mr-2"></i>
            @if (!empty($noProfileSkills))
                Add your core skills to your profile so we can recommend jobs for you.
                <a href="{{ route('user.profile') }}" class="font-weight-bold">Update profile &rarr;</a>
            @else
                Showing jobs matched to the skills on your profile.
            @endif
        </span>
        <a href="{{ route('user.jobs') }}" class="btn btn-sm btn-outline-primary">View all jobs</a>
    </div>
</div>
@endif

<!-- HERO SEARCH HEADER -->
<div class="w-100 py-5 {{ !empty($isRecommended) ? 'mt-4 u-bg-linear-gradien-bb-1px-solid-d8e6' : 'u-mt-90px-bg-linear-gradien-bb-1px-solid-d8e6' }}">

    <div class="container text-center">

        <h2 class="font-weight-bold mb-3 u-color-1b3d6d">
            Find a role that matches your ambition
        </h2>

        <p class="text-muted mb-4">
            Search thousands of curated openings across industries, expertise levels, and locations.
        </p>

        <!-- SEARCH BAR (submits basic filters) -->
        <form method="GET" action="{{ route('user.jobs.filter') }}">
            <div class="row justify-content-center">

                <div class="col-md-4 col-12 mb-2">
                    <input type="text" name="search" aria-label="Job title or company" class="form-control shadow-sm u-h-48px-fs-0-933rem" placeholder="Job title or company" value="{{ request('search') }}">
                </div>

                <div class="col-md-3 col-6 mb-2">
                    <select name="category" aria-label="Filter by category" class="form-control shadow-sm u-h-48px-fs-0-933rem">
                        <option value="">Category</option>
                        @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-6 mb-2">
                    <select name="role" aria-label="Filter by role" class="form-control shadow-sm u-h-48px-fs-0-933rem">
                        <option value="">Role</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role')==$role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-12 mb-2">
                    <button type="submit" class="job-search-btn">
                        <i class="fas fa-search"></i> <span>Search</span>
                    </button>
                </div>

                <!-- Keep current sort when top search submits -->
                <input type="hidden" name="sort" value="{{ request('sort') }}">

            </div>
        </form>

    </div>
</div>

@if (!empty($trendingJobs) && $trendingJobs->count() > 0)
<div class="container mt-5">
    <h4 class="font-weight-bold mb-3 u-color-1b3d6d">
        <i class="fas fa-fire text-danger mr-2"></i> Trending Jobs
    </h4>
    <div class="row">
        @foreach ($trendingJobs as $tjob)
        <div class="col-md-4 col-sm-6 mb-3">
            <a href="{{ route('user.job_single', $tjob->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="font-weight-bold text-dark mb-1">{{ $tjob->title }}</h6>
                            <span class="badge bg-danger text-white u-fs-0-7rem">
                                <i class="fas fa-eye mr-1"></i>{{ $tjob->views_count }}
                            </span>
                        </div>
                        <p class="text-muted small mb-1">{{ $tjob->admin->company_name ?? 'Company' }}</p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $tjob->location ?? 'Remote' }}
                        </p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif


<!-- FILTER + JOBS -->
<div class="container mt-5">
    <div class="row">

        <!-- FILTER SIDEBAR (Form submits all filter inputs) -->
        <div class="col-md-3">
            <form method="GET" action="{{ route('user.jobs.filter') }}">
                <div class="p-4 shadow-sm u-bg-white-radius-18px-border-1px-solid-e5ee">

                    <h5 class="font-weight-bold mb-3 u-color-1b3d6d">Filters</h5>

                    <hr>

                    <!-- Job Type -->
                    <h6 class="font-weight-bold">Job Type</h6>
                    @foreach (['Full-time', 'Part-time', 'Internship', 'Contract'] as $jt)
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" id="jobType{{ Str::slug($jt) }}" name="job_type[]" value="{{ $jt }}" {{
                            in_array($jt, (array) request('job_type', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="jobType{{ Str::slug($jt) }}">{{ $jt }}</label>
                    </div>
                    @endforeach

                    <hr>

                    <!-- Experience -->
                    <h6 class="font-weight-bold">Experience</h6>
                    @foreach (['Fresher', '1 Year', '2 Years', '3 Years', '3+ Years'] as $exp)
                    <div class="form-check mb-1">
                        <input id="experience{{ Str::slug($exp) }}" type="checkbox" class="form-check-input" name="experience[]" value="{{ $exp }}" {{
                            in_array($exp, (array) request('experience', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="experience{{ Str::slug($exp) }}">{{ $exp }}</label>
                    </div>
                    @endforeach

                    <hr>

                    <!-- Salary (min + max) -->
                    <h6 class="font-weight-bold">Salary (LPA)</h6>

                    <div class="d-flex align-items-center mb-2">
                        <input type="number" name="min_salary" id="minSalary" min="0" max="50" aria-label="Minimum salary in LPA" value="{{ request('min_salary','') }}" class="form-control u-maxw-90px" placeholder="Min">
                        <span class="mx-2">-</span>
                        <input type="number" name="max_salary" id="maxSalary" min="0" max="50" aria-label="Maximum salary in LPA" value="{{ request('max_salary','') }}" class="form-control u-maxw-90px" placeholder="Max">
                        <span class="ml-2 small">LPA</span>
                    </div>

                    <input type="range" min="0" max="50" id="salaryRange" aria-label="Maximum salary slider" value="{{ request('max_salary') ?? 20 }}"
                        class="w-100 mt-2">

                    <small id="salaryLabel" class="text-muted"> {{ request('min_salary', 0)}} - {{ request('max_salary'
                        , 20 )}} LPA</small>

                    <hr>
                    <!-- Category -->
                    <h6 class="font-weight-bold">Category</h6>

                    <div class="premium-select mb-3">
                        <select name="category" aria-label="Filter by category" class="form-control">
                            <option value="">Select Category</option>

                            @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- Role -->
                    <h6 class="font-weight-bold">Role</h6>

                    <div class="premium-select mb-3">
                        <select name="role" aria-label="Filter by role" class="form-control">
                            <option value="">Select Role</option>

                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role')==$role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keep sort param when filter form submits -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                    <button type="submit" class="btn btn-success w-100 mb-2">Apply Filters</button>
                    <a href="{{ route('user.jobs') }}" class="btn btn-danger w-100">Reset</a>
                </div>
            </form>
        </div>


        <!-- JOBS LIST -->
        <div class="col-md-9">

        <div class="jobs-panel">

            <div class="jobs-panel-header">
                <h5 class="jobs-count">
                    <span class="jobs-count-num">{{ $jobs->total() }}</span> Jobs Found
                </h5>

                <form id="sortForm" method="GET" action="{{ route('user.jobs.filter') }}" class="m-0 p-0">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="hidden" id="sortMinSalary" name="min_salary" value="{{ request('min_salary') ?? '' }}">
                    <input type="hidden" id="sortMaxSalary" name="max_salary" value="{{ request('max_salary') ?? '' }}">
                    <div class="premium-select u-maxw-220px">

                        <select name="sort" aria-label="Sort jobs by" class="form-control" onchange="$('#sortForm').trigger('submit');">

                            <option value="latest" {{ request('sort')=='latest' ? 'selected' : '' }}>
                                Latest
                            </option>

                            <option value="salary_low_high" {{ request('sort')=='salary_low_high' ? 'selected' : '' }}>
                                Salary (Low → High)
                            </option>

                            <option value="salary_high_low" {{ request('sort')=='salary_high_low' ? 'selected' : '' }}>
                                Salary (High → Low)
                            </option>

                        </select>

                    </div>
                </form>
            </div>

            <!-- BULK APPLY BAR -->
            <div id="bulkApplyBar" class="status-bar bar-apply d-none">
                <span><span id="bulkApplyCount">0</span> job(s) selected</span>
                <button type="button" id="bulkApplyBtn" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-paper-plane mr-1"></i> Apply to Selected
                </button>
            </div>

            <!-- COMPARE BAR -->
            <div id="compareBar" class="status-bar bar-compare d-none">
                <span><span id="compareCount">0</span> / 3 job(s) selected to compare</span>
                <button type="button" id="compareBtn" class="btn btn-outline-dark btn-sm px-4">
                    <i class="fas fa-balance-scale mr-1"></i> Compare
                </button>
            </div>

            @forelse ($jobs as $job)
            <div class="job-card-premium mb-4">
                <div class="card-body">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-start">

                        <!-- LEFT SIDE -->
                        <div class="d-flex align-items-start">
                            <div class="job-select-group mr-2">
                                @if (!(isset($appliedJobIds) && in_array($job->id, $appliedJobIds)) && ! $job->is_expired)
                                <label class="job-select-toggle bulk-toggle" title="Select for bulk apply">
                                    <input type="checkbox" class="bulk-apply-checkbox" value="{{ $job->id }}">
                                    <i class="fas fa-paper-plane"></i>
                                </label>
                                @endif
                                <label class="job-select-toggle compare-toggle" title="Select to compare">
                                    <input type="checkbox" class="compare-checkbox" value="{{ $job->id }}">
                                    <i class="fas fa-balance-scale"></i>
                                </label>
                            </div>
                            <div>
                                <a href="{{ route('user.job_single', $job->id) }}" class="job-title-link mb-1 d-inline-block text-decoration-none">{{ $job->title }}</a>

                                @if (isset($appliedJobIds) && in_array($job->id, $appliedJobIds))
                                <span class="badge-applied-pill ml-1">Applied</span>
                                @endif
                            </div>
                        </div>

                        <!-- BOOKMARK (TOP RIGHT) -->
                        <div>
                            @if ($job->isSavedByUser())
                            <form method="POST" action="{{ route('saved.destroy', $job->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 bookmark-btn is-saved">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('saved.store', $job->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 bookmark-btn">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            </form>
                            @endif
                        </div>

                    </div>

                    <!-- COMPANY -->
                    <div class="d-flex align-items-center mt-2">
                        <div class="company-avatar">{{ strtoupper(substr($job->admin->company_name ?? 'C', 0, 1)) }}</div>
                        <p class="job-company mb-0 ml-2">
                            @if (optional($job->admin)->slug)
                            <a href="{{ route('company.show', $job->admin->slug) }}" class="text-dark text-decoration-none">{{ $job->admin->company_name ?? 'Company' }}</a>
                            @else
                            {{ $job->admin->company_name ?? 'Company' }}
                            @endif
                            @if (optional($job->admin)->is_verified)
                            <i class="fas fa-check-circle text-primary ml-1" title="Verified Company"></i>
                            @endif
                        </p>
                    </div>

                    <!-- JOB META -->
                    <div class="job-meta-row">

                        <span class="job-meta-pill">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ ucfirst($job->location) }}
                        </span>

                        <span class="job-meta-pill">
                            <i class="fas fa-rupee-sign text-success"></i>
                            @if($job->min_salary && $job->max_salary)
                            ₹{{ number_format($job->min_salary / 100000, 1) }}L - ₹{{ number_format($job->max_salary /
                            100000, 1) }}L
                            @elseif($job->min_salary)
                            ₹{{ number_format($job->min_salary / 100000, 1) }}L+
                            @else
                            Salary not disclosed
                            @endif
                        </span>

                        <span class="job-meta-pill">
                            <i class="fas fa-user-clock text-info"></i>
                            {{ $job->experience ?? 'N/A' }}
                        </span>

                        <span class="job-meta-pill">
                            <i class="fas fa-briefcase text-primary"></i>
                            {{ $job->type ?? 'N/A' }}
                        </span>

                    </div>

                    <!-- DESCRIPTION -->
                    <p class="job-desc mb-3 u-minh-60px">
                        {{ Str::limit($job->description, 120) }}
                    </p>

                    <!-- VIEW DETAILS (BOTTOM RIGHT) -->
                    <div class="text-right">
                        <a href="{{ route('user.job_single', $job->id) }}"
                            class="btn btn-view-details">
                            View Details
                        </a>
                    </div>

                </div>
            </div>

            @empty

            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                @if (!empty($isRecommended) && empty($noProfileSkills))
                    No jobs currently match your profile's skills. Check back soon, or browse all jobs below.
                @else
                    No matching jobs found.
                @endif
            </div>
            @endforelse

            <!-- Pagination -->
            <div class="pagination-wrap">
                {{ $jobs->links('pagination::bootstrap-4') }}
            </div>

        </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')

<script>
    $(function () {

    // ─── Bulk Apply ─────────────────────────────────────────────────────
    function refreshBulkApplyBar() {
        var checked = $('.bulk-apply-checkbox:checked');
        if (checked.length > 0) {
            $('#bulkApplyBar').removeClass('d-none');
            $('#bulkApplyCount').text(checked.length);
        } else {
            $('#bulkApplyBar').addClass('d-none');
        }
    }

    $(document).on('change', '.bulk-apply-checkbox', refreshBulkApplyBar);

    // ─── Job Comparison ─────────────────────────────────────────────────
    function refreshCompareBar() {
        var checked = $('.compare-checkbox:checked');
        if (checked.length > 0) {
            $('#compareBar').removeClass('d-none');
            $('#compareCount').text(checked.length);
        } else {
            $('#compareBar').addClass('d-none');
        }
    }

    $(document).on('change', '.compare-checkbox', function () {
        var checked = $('.compare-checkbox:checked');
        if (checked.length > 3) {
            this.checked = false;
            alert('You can compare up to 3 jobs at a time.');
        }
        refreshCompareBar();
    });

    $('#compareBtn').on('click', function () {
        var ids = $('.compare-checkbox:checked').map(function () {
            return this.value;
        }).get();

        if (ids.length < 2) {
            alert('Select at least 2 jobs to compare.');
            return;
        }

        var params = ids.map(function (id) {
            return 'job_ids[]=' + encodeURIComponent(id);
        }).join('&');

        window.location.href = '{{ route('user.compare') }}?' + params;
    });

    $('#bulkApplyBtn').on('click', function () {
        var ids = $('.bulk-apply-checkbox:checked').map(function () {
            return this.value;
        }).get();

        if (ids.length === 0) {
            return;
        }

        var params = ids.map(function (id) {
            return 'job_ids[]=' + encodeURIComponent(id);
        }).join('&');

        window.location.href = '{{ route('user.bulk_apply.form') }}?' + params;
    });

    // ─── Salary Label Sync ──────────────────────────────────────────────
    function updateSalaryLabel() {
        var minVal = $('#minSalary').val();
        var maxVal = $('#maxSalary').val();

        // Show label using actual values or defaults for display only
        var displayMin = minVal !== '' ? minVal : 0;
        var displayMax = maxVal !== '' ? maxVal : 20;
        $('#salaryLabel').text(displayMin + ' - ' + displayMax + ' LPA');

        //  Sync to sortForm — send empty string if not set 
        $('#sortMinSalary').val(minVal);
        $('#sortMaxSalary').val(maxVal);
    }

    // Slider → only updates max input
    $('#salaryRange').on('input change', function () {
        $('#maxSalary').val($(this).val());
        updateSalaryLabel();
    });

    // Min input validation
    $('#minSalary').on('input change', function () {
        var min = parseFloat($(this).val());
        var max = parseFloat($('#maxSalary').val());

        // Don't let min exceed max
        if (!isNaN(min) && !isNaN(max) && min > max) {
            $(this).val(max);
        }
        updateSalaryLabel();
    });

    // Max input → also sync slider
    $('#maxSalary').on('input change', function () {
        var val = $(this).val();
        $('#salaryRange').val(val !== '' ? val : 20);
        updateSalaryLabel();
    });

    //  Initialize label correctly on page load
    updateSalaryLabel();


    // ─── Sort Form Submit ───────────────────────────────────────────────
    //  Works because we use $('#sortForm').trigger('submit') in onchange
    $('#sortForm').on('submit', function (e) {

        // Sync salary from sidebar inputs to sort form hidden inputs
        $('#sortMinSalary').val($('#minSalary').val());
        $('#sortMaxSalary').val($('#maxSalary').val());

        // Remove any old job_type hidden inputs then rebuild from checked boxes
        $(this).find('input[name="job_type[]"]').remove();
        $('input[name="job_type[]"]:checked').each(function () {
            $('<input>').attr({
                type  : 'hidden',
                name  : 'job_type[]',
                value : $(this).val()
            }).appendTo('#sortForm');
        });

        //  Remove any old experience hidden inputs then rebuild from checked boxes
        $(this).find('input[name="experience[]"]').remove();
        $('input[name="experience[]"]:checked').each(function () {
            $('<input>').attr({
                type  : 'hidden',
                name  : 'experience[]',
                value : $(this).val()
            }).appendTo('#sortForm');
        });

        return true; 
    });

});
</script>
@endpush