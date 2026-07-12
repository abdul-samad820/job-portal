@extends('layouts.Admin_layout')
@section('title', 'Candidates')

@section('content')

<div class="container-fluid py-4">

    @if (session('success'))
    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
    @endif

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-users',
        'title' => 'Open to Work Candidates',
        'subtitle' => "Job seekers who've marked themselves available — invite them to apply for one of your open roles.",
    ])

    <div class="card shadow-sm border-0 rounded mb-4">
        <div class="card-body">
            <div class="sa-toolbar mb-0">
                <form method="GET" action="{{ route('admin.candidates.index') }}" class="form-inline">
                    <input type="text" name="skill" class="form-control form-control-sm mr-2" placeholder="Filter by skill (e.g. React)"
                        value="{{ request('skill') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($candidates as $candidate)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded h-100">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-1">{{ $candidate->name }}</h6>
                    <p class="text-muted small mb-2">
                        {{ Str::limit($candidate->profile->professional_summary ?? 'No summary provided.', 90) }}
                    </p>

                    @if ($candidate->profile && $candidate->profile->core_skills)
                    <p class="mb-3">
                        @foreach (array_slice(array_filter(array_map('trim', explode(',', $candidate->profile->core_skills))), 0, 5) as $skill)
                        <span class="badge badge-light border mr-1 mb-1">{{ $skill }}</span>
                        @endforeach
                    </p>
                    @endif

                    <button type="button" class="btn btn-primary btn-sm rounded-pill" data-toggle="modal"
                        data-target="#inviteModal{{ $candidate->id }}" {{ $myJobs->count() === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-1"></i> Invite to Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Invite Modal -->
        <div class="modal fade" id="inviteModal{{ $candidate->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.candidates.invite', $candidate->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Invite {{ $candidate->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Job</label>
                                <select name="job_id" class="form-control" required>
                                    <option value="">-- Choose a job --</option>
                                    @foreach ($myJobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Message (optional)</label>
                                <textarea name="message" class="form-control" rows="3"
                                    placeholder="e.g. Your profile looks like a great fit for this role..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Send Invite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="p-5 text-center text-muted bg-white shadow-sm rounded">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p class="fs-5 mb-0">No candidates are currently marked "Open to Work".</p>
            </div>
        </div>
        @endforelse
    </div>

    {{ $candidates->links() }}

</div>

@endsection