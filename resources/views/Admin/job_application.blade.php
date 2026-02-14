@extends('layouts.index')
@section('title', 'Job Applications')

@section('content')

<style>
    .table-responsive { overflow: visible !important; }
</style>

<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12">

            <!-- HEADER CARD -->
            <!-- Soft Header Section -->
<div class="p-4 rounded shadow-sm mb-4" 
     style="background:#f7f9ff; border-left:5px solid #007bff;">

    <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">

        <div>
            <h1 class="h4 font-weight-bold text-dark mb-1 d-flex align-items-center">
                <i class="fa fa-file-alt text-primary mr-2"></i>
                Job Applications
            </h1>
            <p class="text-muted small mb-0">
                View, manage and update all job applications.
            </p>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
            <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active font-weight-bold">
                    Applications
                </li>
            </ol>
        </nav>

    </div>

    <!-- Search -->
    <form action="{{ route('job_application') }}" 
          method="GET"
          class="d-flex align-items-center mt-3 w-100"
          style="max-width:370px;">

        <input type="search"
               name="search"
               class="form-control flex-grow-1 mr-2"
               placeholder="Search by user or job..."
               value="{{ request('search') }}">

        <button class="btn btn-primary px-3" type="submit">
            <i class="fa fa-search"></i>
        </button>

    </form>

</div>

            <!-- MAIN TABLE CARD -->
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                    <th>Resume</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach ($applications as $app)
                            @php
                                $profile = $app->user->profile;
                                $education = $profile && $profile->education 
                                    ? json_decode($profile->education, true) 
                                    : [];
                            @endphp

                                <tr>

                                    <!-- USER WITH MODAL BUTTON -->
                                    <td>
                                        {{ $app->user->name ?? 'Deleted User' }} <br>
                                        <button class="btn btn-link p-0 mt-1 text-primary small"
                                                data-toggle="modal"
                                                data-target="#userModal{{ $app->id }}">
                                            View Details
                                        </button>
                                    </td>

                                    <!-- JOB TITLE -->
                                    <td>{{ $app->job->title ?? 'Deleted Job' }}</td>

                                    <!-- STATUS DROPDOWN -->
                                   <td>
    <form action="{{ route('admin.application.updateStatus', $app->id) }}"
          method="POST" class="form-inline">
        @csrf
        @method('POST')

        <!-- Hidden input -->
        <input type="hidden" name="status" id="statusInput{{ $app->id }}" value="{{ $app->status }}">

        <div class="dropdown mr-2">
            <button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                <span id="statusText{{ $app->id }}">{{ ucfirst($app->status) }}</span>
            </button>

            <div class="dropdown-menu p-2 shadow">
                <a class="dropdown-item"
                   href="#"
                   onclick="setStatus('{{ $app->id }}','pending')">
                   <i class="fa fa-hourglass-half text-secondary"></i> Pending
                </a>

                <a class="dropdown-item"
                   href="#"
                   onclick="setStatus('{{ $app->id }}','shortlisted')">
                   <i class="fa fa-user text-info"></i> Shortlisted
                </a>

                <a class="dropdown-item"
                   href="#"
                   onclick="setStatus('{{ $app->id }}','hired')">
                   <i class="fa fa-check-circle text-success"></i> Hired
                </a>

                <a class="dropdown-item"
                   href="#"
                   onclick="setStatus('{{ $app->id }}','rejected')">
                   <i class="fa fa-times-circle text-danger"></i> Rejected
                </a>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
            Update
        </button>
    </form>
</td>

                                    <!-- RESUME -->
                                    <td>
                                        <a href="{{ asset($app->resume) }}" target="_blank" class="text-primary">View</a> |
                                        <a href="{{ asset($app->resume) }}" download class="text-success">Download</a>
                                    </td>

                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                </tr>


                                <!-- USER DETAIL MODAL -->
                                <div class="modal fade" id="userModal{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content shadow">

                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title">Applicant Details — {{ $app->user->name }}</h5>
                                                <button class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">

                                                <!-- PROFILE IMAGE -->
                                                <div class="text-center mb-3">
                                                    <img src="{{ $profile && $profile->profile_image 
                                                                ? asset('uploads/user_profile/' . $profile->profile_image)
                                                                : asset('admins/dist/img/default.png') }}"
                                                        class="rounded-circle shadow"
                                                        width="100" height="100" style="object-fit:cover;">

                                                    <h5 class="mt-2">{{ $app->user->name }}</h5>
                                                    <p class="text-muted">{{ $profile->designation ?? 'Candidate' }}</p>
                                                </div>

                                                <hr>

                                                <!-- SUMMARY -->
                                                <h6 class="font-weight-bold">Professional Summary</h6>
                                                <p>{{ $profile->professional_summary ?? 'No summary added.' }}</p>

                                                <hr>

                                                <!-- EXPERIENCE -->
                                                <h6 class="font-weight-bold">Experience</h6>
                                                @if ($profile && $profile->experience)
                                                    @foreach (explode("\n", $profile->experience) as $line)
                                                        <p>• {{ $line }}</p>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No experience provided.</p>
                                                @endif

                                                <hr>

                                                <!-- SKILLS -->
                                                <h6 class="font-weight-bold">Skills</h6>
                                                @if ($profile && $profile->core_skills)
                                                    @foreach (explode(',', $profile->core_skills) as $skill)
                                                        <span class="badge badge-info p-2 m-1">{{ trim($skill) }}</span>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No skills added.</p>
                                                @endif

                                                <hr>

                                                <!-- EDUCATION -->
                                                <h6 class="font-weight-bold">Education</h6>
                                                @if ($education)
                                                    @foreach ($education as $edu)
                                                        <div class="mb-3 pl-3 border-left border-primary">
                                                            <strong>{{ $edu['degree'] }}</strong><br>
                                                            {{ $edu['institute'] }}<br>
                                                            <small>{{ $edu['year'] }}</small>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No education added.</p>
                                                @endif

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @endforeach

                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted small">
                            Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} entries
                        </span>

                        <div>{{ $applications->links('pagination::bootstrap-4') }}</div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
@push('scripts')
    <script>
function setStatus(id, value) {
    document.getElementById('statusInput' + id).value = value;
    document.getElementById('statusText' + id).innerText =
        value.charAt(0).toUpperCase() + value.slice(1);
}
</script>
@endpush