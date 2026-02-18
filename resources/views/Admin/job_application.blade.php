@extends('layouts.index')
@section('title', 'Job Applications')

@section('content')

    <div class="container-fluid py-4">

        {{-- ================= HEADER ================= --}}
        <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary" style="border-width:4px !important;">

            <div class="d-flex justify-content-between flex-wrap align-items-center">

                <div>
                    <h4 class="font-weight-bold text-dark mb-1">
                        <i class="fa fa-file-alt text-primary mr-2"></i>
                        Job Applications
                    </h4>
                    <small class="text-muted">
                        View, manage and update all job applications.
                    </small>
                </div>

                <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                    <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active font-weight-bold">
                            Applications
                        </li>
                    </ol>
                </nav>

            </div>

            {{-- Search --}}
            <div class="mt-3">
                <form action="{{ route('job_application') }}" method="GET" class="form-inline d-none d-md-flex">
                    <input type="search" name="search" class="form-control mr-2" placeholder="Search by user or job..."
                        value="{{ request('search') }}" style="max-width:280px;">
                    <button class="btn btn-outline-primary">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

        </div>

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="card shadow-sm d-none d-md-block">
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
                                <tr>
                                    <td>
                                        {{ $app->user->name ?? 'Deleted User' }} <br>
                                        <button class="btn btn-link p-0 text-primary small" data-toggle="modal"
                                            data-target="#userModal{{ $app->id }}">
                                            View Details
                                        </button>
                                    </td>

                                    <td>{{ $app->job->title ?? 'Deleted Job' }}</td>

                                    <!-- STATUS DROPDOWN -->
                                    <td>
                                        <form action="{{ route('admin.application.updateStatus', $app->id) }}"
                                            method="POST"> @csrf @method('POST')

                                            <div class="d-md-flex align-items-center">
                                                <!-- Hidden input --> <input type="hidden" name="status"
                                                    id="statusInput{{ $app->id }}" value="{{ $app->status }}">
                                                <div class="dropdown mr-2"> <button class="btn btn-light dropdown-toggle"
                                                        type="button" data-toggle="dropdown"> <span
                                                            id="statusText{{ $app->id }}">{{ ucfirst($app->status) }}</span>
                                                    </button>
                                                    <div class="dropdown-menu p-2 shadow"> <a class="dropdown-item"
                                                            href="#"
                                                            onclick="setStatus('{{ $app->id }}','pending')"> <i
                                                                class="fa fa-hourglass-half text-secondary"></i> Pending
                                                        </a> <a class="dropdown-item" href="#"
                                                            onclick="setStatus('{{ $app->id }}','shortlisted')"> <i
                                                                class="fa fa-user text-info"></i> Shortlisted </a> <a
                                                            class="dropdown-item" href="#"
                                                            onclick="setStatus('{{ $app->id }}','hired')"> <i
                                                                class="fa fa-check-circle text-success"></i> Hired </a> <a
                                                            class="dropdown-item" href="#"
                                                            onclick="setStatus('{{ $app->id }}','rejected')"> <i
                                                                class="fa fa-times-circle text-danger"></i> Rejected </a>
                                                    </div>
                                                </div> <button type="submit" class="btn btn-primary btn-sm"> Update
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($app->resume) }}" target="_blank">View</a> |
                                        <a href="{{ route('admin.resume.download', $app->id) }}" class="text-success">
                                            Download
                                        </a>
                                    </td>

                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- ================= MOBILE CARD VIEW ================= --}}
        <div class="d-block d-md-none">
            @foreach ($applications as $app)
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">

                        {{-- Top Section : Name + Date --}}
                        <div class="mb-2">
                            <h6 class="font-weight-bold mb-0">
                                {{ $app->user->name ?? 'Deleted User' }}
                            </h6>
                            <small class="text-muted">
                                Applied on {{ $app->created_at->format('d M Y') }}
                            </small>
                        </div>

                        <hr class="my-2">

                        {{-- Job Title --}}
                        <p class="mb-2">
                            <strong>Job:</strong><br>
                            {{ $app->job->title ?? 'Deleted Job' }}
                        </p>

                        {{-- Resume --}}
                        <p class="mb-3">
                            <strong>Resume:</strong><br>
                            <a href="{{ Storage::url($app->resume) }}" target="_blank">
                                View
                            </a>
                            |
                            <a href="{{ route('admin.resume.download', $app->id) }}" class="text-success">
                                Download
                            </a>
                        </p>

                        {{-- Status Update Section --}}
                        <form action="{{ route('admin.application.updateStatus', $app->id) }}" method="POST">
                            @csrf

                            <input type="hidden" name="status" id="statusInputMobile{{ $app->id }}"
                                value="{{ $app->status }}">

                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">
                                    Application Status
                                </label>

                                <select class="form-control form-control-sm"
                                    onchange="document.getElementById('statusInputMobile{{ $app->id }}').value = this.value">

                                    <option value="pending" {{ $app->status == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="shortlisted" {{ $app->status == 'shortlisted' ? 'selected' : '' }}>
                                        Shortlisted
                                    </option>

                                    <option value="hired" {{ $app->status == 'hired' ? 'selected' : '' }}>
                                        Hired
                                    </option>

                                    <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>
                                        Rejected
                                    </option>

                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                Update Status
                            </button>
                        </form>

                        {{-- View Details Button --}}
                        <button class="btn btn-outline-primary btn-sm btn-block mt-3" data-toggle="modal"
                            data-target="#userModal{{ $app->id }}">
                            View Applicant Details
                        </button>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= MODALS ================= --}}
        @foreach ($applications as $app)
            @php
                $profile = $app->user->profile ?? null;
                $education = $profile && $profile->education ? json_decode($profile->education, true) : [];
            @endphp

            <div class="modal fade" id="userModal{{ $app->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content shadow">

                        <div class="modal-header bg-light">
                            <h5 class="modal-title">Applicant Details — {{ $app->user->name }}
                            </h5>
                            <button class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">

                            @php
                                $profileImage =
                                    $profile && $profile->profile_image
                                        ? Storage::url('user_profile/' . $profile->profile_image)
                                        : asset('admins/dist/img/default.png');
                            @endphp

                            <div class="text-center mb-4">

                                <img src="{{ $profileImage }}" class="rounded-circle shadow-sm border" width="110"
                                    height="110" style="object-fit:cover; border:3px solid #f8f9fa;"
                                    alt="User Profile">

                                <h5 class="mt-3 mb-1 font-weight-bold">
                                    {{ $app->user->name }}
                                </h5>

                                <small class="text-muted d-block">
                                    {{ $profile->designation ?? 'Candidate' }}
                                </small>

                            </div>
                            <hr>

                            <!-- SUMMARY -->
                            <h6 class="font-weight-bold">Professional Summary</h6>
                            <p>{{ $profile->professional_summary ?? 'No summary added.' }}</p>

                            <hr>

                            <h6 class="font-weight-bold">Cover Letter</h6>
                            @if ($app->cover_letter)
                                <p class="text-justify">
                                    {!! nl2br(e($app->cover_letter)) !!}
                                </p>
                            @else
                                <p class="text-muted">No cover letter provided.</p>
                            @endif

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

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">
                Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }}
                of {{ $applications->total() }} entries
            </span>

            {{ $applications->links('pagination::bootstrap-4') }}
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
