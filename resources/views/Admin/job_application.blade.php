@extends('layouts.Admin_layout')
@section('title', 'Job Applications')
@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-file-alt',
        'title' => 'Job Applications',
        'subtitle' => 'View, manage and update all job applications.',
    ])

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="card shadow-sm border-0 rounded d-none d-md-block">
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <form action="{{ route('job_application') }}" method="GET" class="form-inline mb-0" style="flex: 1 1 320px; max-width: 420px;">
                    <div class="input-group input-group-sm w-100">
                        <input type="search" name="search" class="form-control" aria-label="Search by user or job" placeholder="Search by user or job..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- ============ BULK ACTIONS (desktop) ============ --}}
                {{-- Checkboxes below reference this form by id (HTML5 form="")
                     so they can live inside the table without nesting forms. --}}
                <form id="bulkStatusForm" method="POST" action="{{ route('admin.application.bulkUpdateStatus') }}"
                    class="d-flex align-items-center bg-light rounded-pill px-3 py-2 mb-0">
                    @csrf
                    <span id="bulkSelectedCount" class="badge badge-light border text-muted font-weight-normal mr-2 px-3 py-2">0 selected</span>
                    <select name="status" class="form-control form-control-sm u-maxw-180px mr-2" required
                        style="height: 38px; line-height: 1.4; padding-top: 6px; padding-bottom: 6px;">
                        <option value="">Set status to...</option>
                        <option value="shortlisted">Shortlisted</option>
                        <option value="rejected">Rejected</option>
                        <option value="hired">Hired</option>
                        <option value="pending">Pending</option>
                    </select>
                    <button type="submit" id="bulkApplyBtn" class="btn btn-sm btn-primary rounded-pill px-3" disabled
                        style="height: 45px;"
                        onclick="return confirm('Update all selected applications? Each applicant will get an email/notification.')">
                        Apply to Selected
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive u-ovf-visible">
                <table class="table table-hover align-middle">

                    <thead class="thead-light">
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="selectAllApplications"></th>
                            <th>User</th>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Resume</th>
                            <th>Interview</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($applications as $app)
                        <tr>
                            <td>
                                <input type="checkbox" name="application_ids[]" value="{{ $app->id }}"
                                    form="bulkStatusForm" class="app-row-checkbox">
                            </td>
                            <td>
                                <span class="d-block font-weight-semibold text-dark">{{ $app->user->name ?? 'Deleted User' }}</span>
                                <button class="btn btn-link p-0 text-primary u-fs-0-8rem text-nowrap" data-toggle="modal" data-target="#userModal{{ $app->id }}">
                                    View Details
                                </button>
                            </td>

                            <td>{{ $app->job->title ?? 'Deleted Job' }}</td>

                            <!-- STATUS DROPDOWN -->
                            <td class="align-middle">
                                <form action="{{ route('admin.application.updateStatus', $app->id) }}" method="POST">
                                    @csrf @method('POST')

                                    <div class="d-flex align-items-center justify-content-start">
                                        <!-- Hidden input --> <input type="hidden" name="status" id="statusInput{{ $app->id }}" value="{{ $app->status }}">
                                        <div class="dropdown mr-2"> <button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown"> <span id="statusText{{ $app->id }}">{{ ucfirst($app->status) }}</span>
                                            </button>
                                            <div class="dropdown-menu p-2 shadow"> <a class="dropdown-item" href="#" onclick="setStatus('{{ $app->id }}','pending')"> <i class="fa fa-hourglass-half text-secondary"></i> Pending
                                                </a> <a class="dropdown-item" href="#" onclick="setStatus('{{ $app->id }}','shortlisted')"> <i class="fa fa-user text-info"></i> Shortlisted </a> <a class="dropdown-item" href="#" onclick="setStatus('{{ $app->id }}','hired')"> <i class="fa fa-check-circle text-success"></i> Hired </a> <a class="dropdown-item" href="#" onclick="setStatus('{{ $app->id }}','rejected')"> <i class="fa fa-times-circle text-danger"></i> Rejected </a>
                                            </div>
                                        </div> <button type="submit" class="btn btn-primary btn-sm"> Update
                                        </button>
                                    </div>
                                </form>
                            </td>

                            <td class="text-nowrap">
                                <a href="{{ route('admin.application.resume', $app->id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary mr-1" title="View Resume">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.resume.download', $app->id) }}"
                                    class="btn btn-sm btn-outline-success" title="Download Resume">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                            <td>

                                <div class="d-flex flex-nowrap align-items-center" style="gap: 4px;">

                                    @if($app->status === 'shortlisted')

                                    {{-- Schedule / Reschedule --}}
                                    <a href="{{ route('admin.interview.create', $app->id)}}" class="btn btn-info btn-sm">
                                        {{ $app->interview ? 'Reschedule' : 'Schedule' }}
                                    </a>

                                    @endif

                                    {{-- Cancel / Done --}}
                                    @if($app->interview &&
                                    in_array($app->interview->status, ['scheduled','rescheduled']))

                                    <form method="POST" action="{{ route('admin.interview.cancel', $app->interview->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-danger btn-sm">Cancel</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.interview.complete', $app->interview->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm">Done</button>
                                    </form>

                                    @endif

                                </div>

                            </td>

                            <td>{{ $app->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="font-weight-bold text-muted">No applications yet</h5>
                                <p class="text-muted mb-0">Applications will show up here once candidates start applying to your jobs.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">
                    Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }}
                    of {{ $applications->total() }} entries
                </span>
                {{ $applications->links('pagination::bootstrap-4') }}
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
                    <a href="{{ route('admin.application.resume', $app->id) }}" target="_blank">View</a>
                    <span class="text-muted mx-1">|</span>
                    <a href="{{ route('admin.resume.download', $app->id) }}" class="text-success">
                        Download
                    </a>
                </p>

                {{-- Status Update Section --}}
                <form action="{{ route('admin.application.updateStatus', $app->id) }}" method="POST">
                    @csrf

                    <input type="hidden" name="status" id="statusInputMobile{{ $app->id }}" value="{{ $app->status }}">

                    <div class="form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small font-weight-bold mb-0" for="statusSelect{{ $app->id }}">
                                Application Status
                            </label>
                            @php
                                $mobileStatusBadgeMap = [
                                    'pending' => 'badge-secondary',
                                    'shortlisted' => 'badge-info',
                                    'hired' => 'badge-success',
                                    'rejected' => 'badge-danger',
                                ];
                            @endphp
                            <span class="badge badge-pill {{ $mobileStatusBadgeMap[$app->status] ?? 'badge-secondary' }}" id="statusBadgeMobile{{ $app->id }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </div>

                        <select class="form-control" id="statusSelect{{ $app->id }}" aria-label="Update application status"
                            onchange="updateMobileStatus('{{ $app->id }}', this.value)">

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

                {{--  INTERVIEW ACTIONS --}}
                @if($app->status === 'shortlisted')

              <div class="mb-2 mt-3">
                    <a href="{{ route('admin.interview.create', $app->id)}}" class="btn btn-info btn-sm btn-block">
                        {{ $app->interview ? 'Reschedule Interview' : 'Schedule Interview' }}
                    </a>
                </div>

                @endif

                @if($app->interview &&
                in_array($app->interview->status, ['scheduled','rescheduled']))

                <div class="d-flex">

                    <form method="POST" action="{{ route('admin.interview.cancel', $app->interview->id) }}" class="w-50 mr-1">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger btn-sm btn-block">Cancel</button>
                    </form>

                    <form method="POST" action="{{ route('admin.interview.complete', $app->interview->id) }}" class="w-50 ml-1">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success btn-sm btn-block">Done</button>
                    </form>

                </div>

                @endif

                {{-- View Details Button --}}
                <button class="btn btn-outline-primary btn-sm btn-block mt-3" data-toggle="modal" data-target="#userModal{{ $app->id }}">
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
    $education = $profile->education ?? [];
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
                    <div class="row text-center mb-3">

                        <div class="col-md-4">
                            <strong>Email</strong><br>
                            {{ $app->user->email }}
                        </div>

                        <div class="col-md-4">
                            <strong>Phone</strong><br>
                            {{ $app->user->phone ?? 'N/A' }}
                        </div>

                        <div class="col-md-4">
                            <strong>Location</strong><br>
                            {{ $app->user->address ?? 'N/A' }}
                        </div>

                    </div>
                    <hr>
                    @php
                    $profileImage =
                    $profile && $profile->profile_image
                    ? Storage::url('user_profile/' . $profile->profile_image)
                    : asset('admins/dist/img/default.png');
                    @endphp

                    <div class="text-center mb-4">

                        <img src="{{ $profileImage }}" class="rounded-circle shadow-sm border u-fit-cover-border-3px-solid-f8f9" width="110" height="110" alt="User Profile">

                        <h5 class="mt-3 mb-1 font-weight-bold">
                            {{ $app->user->name }}
                        </h5>

                        <small class="text-muted d-block">
                            {{ $profile->designation ?? 'Candidate' }}
                        </small>

                    </div>

                    <div class="card border-0 shadow-sm mb-3">

                        <div class="card-header bg-white">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-chart-line text-success mr-2"></i>
                                Candidate Evaluation
                            </h6>
                        </div>

                        <div class="card-body">

                            {{-- Skill Match Section --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong>Skill Match</strong>
                                    <span class="font-weight-bold">
                                        {{ $app->match_percentage }}%
                                    </span>
                                </div>

                                <div class="progress u-h-8px-radius-6px">
                                    <div class="progress-bar 
                    {{ $app->match_percentage >= 70 ? 'bg-success' : ($app->match_percentage >= 40 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $app->match_percentage }}%">
                                    </div>
                                </div>

                                <small class="text-muted">
                                    Match calculated based on required vs candidate skills.
                                </small>
                            </div>

                            <hr>

                            {{-- Timeline Section --}}
                            <h6 class="font-weight-bold mb-3">
                                <i class="fa fa-clock text-primary mr-2"></i>
                                Application Timeline
                            </h6>

                            <div class="row text-center">

                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Applied On</small>
                                    <strong>{{ $app->created_at->format('d M Y') }}</strong>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Status Updated</small>
                                    <strong>
                                        {{ optional($app->status_updated_at)->format('d M Y') ?? 'Not updated' }}
                                    </strong>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <small class="text-muted d-block">Updated By</small>
                                    <strong>
                                        {{ optional($app->updatedBy)->company_name ?? 'System' }}
                                    </strong>
                                </div>

                            </div>

                        </div>
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
                    @php
                    $experience = $profile->experience ?? [];
                    @endphp

                    @foreach ($experience as $exp)
                    <p>• {{ $exp['role'] ?? '' }} at {{ $exp['company'] ?? '' }}</p>
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
                       {{ $edu['institute'] ?? $edu['institution'] ?? 'N/A' }}<br>
                        <small>{{ $edu['year'] }}</small>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">No education added.</p>
                    @endif
                    <hr>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold">
                                Recruiter Notes
                            </h6>
                            <small class="text-muted">
                                Private (Only visible to admin)
                            </small>
                        </div>

                        <div class="card-body">

                            <form action="{{ route('admin.application.note', $app->id) }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <textarea name="admin_note" rows="4" class="form-control form-control-sm" aria-label="Admin note about this candidate" placeholder="Write internal notes about this candidate...">{{ $app->admin_note }}</textarea>
                                </div>

                                <div class="text-right">
                                    <button class="btn btn-primary btn-sm px-4">
                                        <i class="fa fa-save mr-1"></i>
                                        Save Note
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
    function setStatus(id, value) {
        document.getElementById('statusInput' + id).value = value;
        document.getElementById('statusText' + id).innerText =
            value.charAt(0).toUpperCase() + value.slice(1);
    }

    function updateMobileStatus(id, value) {
        document.getElementById('statusInputMobile' + id).value = value;
        const badge = document.getElementById('statusBadgeMobile' + id);
        if (badge) {
            const badgeClassMap = {
                pending: 'badge-secondary',
                shortlisted: 'badge-info',
                hired: 'badge-success',
                rejected: 'badge-danger'
            };
            badge.className = 'badge badge-pill ' + (badgeClassMap[value] || 'badge-secondary');
            badge.textContent = value.charAt(0).toUpperCase() + value.slice(1);
        }
    }

    (function () {
        const selectAll = document.getElementById('selectAllApplications');
        const applyBtn = document.getElementById('bulkApplyBtn');
        const countLabel = document.getElementById('bulkSelectedCount');

        function rowCheckboxes() {
            return document.querySelectorAll('.app-row-checkbox');
        }

        function refresh() {
            const checked = document.querySelectorAll('.app-row-checkbox:checked').length;
            countLabel.textContent = checked + ' selected';
            applyBtn.disabled = checked === 0;
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes().forEach(cb => cb.checked = selectAll.checked);
                refresh();
            });
        }

        document.addEventListener('change', function (e) {
            if (e.target.classList && e.target.classList.contains('app-row-checkbox')) {
                refresh();
            }
        });

        refresh();
    })();
</script>
@endpush