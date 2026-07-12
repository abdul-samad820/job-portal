@extends('layouts.superadmin')
@section('title', 'Job Moderation')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-briefcase',
        'title' => 'All Jobs',
        'subtitle' => 'Every job across every company. Hide or remove anything that violates platform policy.',
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <div class="sa-filter-pills">
                    <a href="{{ route('superadmin.jobs') }}" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                    <a href="{{ route('superadmin.jobs', ['status' => 'live']) }}" class="btn btn-sm {{ $status == 'live' ? 'btn-primary' : 'btn-outline-secondary' }}">Live</a>
                    <a href="{{ route('superadmin.jobs', ['status' => 'expired']) }}" class="btn btn-sm {{ $status == 'expired' ? 'btn-primary' : 'btn-outline-secondary' }}">Expired</a>
                    <a href="{{ route('superadmin.jobs', ['status' => 'hidden']) }}" class="btn btn-sm {{ $status == 'hidden' ? 'btn-primary' : 'btn-outline-secondary' }}">Hidden</a>
                    <a href="{{ route('superadmin.jobs', ['status' => 'reported']) }}" class="btn btn-sm {{ $status == 'reported' ? 'btn-primary' : 'btn-outline-secondary' }}">Reported</a>
                </div>
                <form method="GET" class="form-inline">
                    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                    <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Search title or company..." value="{{ $search }}">
                    <button class="btn btn-sm btn-outline-primary mr-2">Search</button>
                    <a href="{{ route('superadmin.jobs.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-csv mr-1"></i> Export CSV
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Job</th>
                        <th>Company</th>
                        <th style="width:100px">Reports</th>
                        <th style="width:110px">Status</th>
                        <th style="width:100px" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $job->title }}</div>
                            <small class="text-muted">
                                {{ $job->location }} · {{ $job->type }}
                                @if($job->last_date)
                                    · {{ $job->is_expired ? 'Expired on' : 'Expires' }} {{ $job->last_date->format('d M Y') }}
                                @endif
                            </small>
                        </td>
                        <td>{{ $job->admin->company_name ?? '—' }}</td>
                        <td>
                            @if($job->reports_count > 0)
                                <span class="badge badge-warning">{{ $job->reports_count }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td>
                            @if($job->is_hidden)
                                <span class="badge badge-secondary">Hidden</span>
                            @elseif($job->is_expired)
                                <span class="badge badge-warning">Expired</span>
                            @else
                                <span class="badge badge-success">Live</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="sa-row-actions justify-content-end">
                                @if($job->is_hidden)
                                    <form action="{{ route('superadmin.jobs.unhide', $job->id) }}" method="POST" class="d-inline-flex">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-success sa-icon-action" title="Unhide">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-warning sa-icon-action" title="Hide"
                                            data-toggle="collapse" data-target="#hide{{ $job->id }}">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                @endif
                                <form action="{{ route('superadmin.jobs.destroy', $job->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Permanently delete this job and its applications?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger sa-icon-action" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if(!$job->is_hidden)
                    <tr class="collapse" id="hide{{ $job->id }}">
                        <td colspan="5" class="bg-light">
                            <form action="{{ route('superadmin.jobs.hide', $job->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="form-group mb-2">
                                    <label class="font-weight-bold mb-1">Reason for hiding (shown internally, not to the public)</label>
                                    <input type="text" name="hidden_reason" class="form-control form-control-sm" required maxlength="500" placeholder="e.g. Reported as fake/scam job">
                                </div>
                                <button type="submit" class="btn btn-sm btn-warning">Confirm Hide</button>
                            </form>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="5" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-briefcase"></i>
                            <p>No jobs found.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $jobs->links() }}</div>

</div>

@endsection