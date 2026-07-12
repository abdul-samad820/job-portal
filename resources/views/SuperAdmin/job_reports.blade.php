@extends('layouts.superadmin')
@section('title', 'Job Reports')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-flag',
        'title' => 'Reported Jobs',
        'subtitle' => 'Jobs flagged by users as fake, spam, or otherwise problematic.',
        'badge' => $pendingCount > 0 ? ['text' => $pendingCount.' pending', 'class' => 'badge-danger'] : null,
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <div class="sa-filter-pills">
                <a href="{{ route('superadmin.job-reports', ['filter' => 'pending']) }}" class="btn btn-sm {{ $filter == 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('superadmin.job-reports', ['filter' => 'reviewed']) }}" class="btn btn-sm {{ $filter == 'reviewed' ? 'btn-primary' : 'btn-outline-secondary' }}">Reviewed</a>
                <a href="{{ route('superadmin.job-reports', ['filter' => 'dismissed']) }}" class="btn btn-sm {{ $filter == 'dismissed' ? 'btn-primary' : 'btn-outline-secondary' }}">Dismissed</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Job</th>
                        <th>Reported By</th>
                        <th>Reason</th>
                        <th style="width:110px">Status</th>
                        <th style="width:100px" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $r->job->title ?? 'Job deleted' }}</div>
                            <small class="text-muted">{{ $r->job->admin->company_name ?? '' }}</small>
                        </td>
                        <td>{{ $r->user->name ?? 'Unknown' }}</td>
                        <td>
                            <div>{{ \App\Models\JobReport::REASONS[$r->reason] ?? $r->reason }}</div>
                            @if($r->details)
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($r->details, 60) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($r->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($r->status === 'reviewed')
                                <span class="badge badge-success">Reviewed</span>
                            @else
                                <span class="badge badge-secondary">Dismissed</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($r->status === 'pending' && $r->job)
                                <div class="sa-row-actions justify-content-end">
                                    <button type="button" class="btn btn-danger sa-icon-action" title="Hide Job"
                                            data-toggle="collapse" data-target="#act{{ $r->id }}">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                    <form action="{{ route('superadmin.job-reports.dismiss', $r->id) }}" method="POST" class="d-inline-flex">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-secondary sa-icon-action" title="Dismiss">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @if($r->status === 'pending' && $r->job)
                    <tr class="collapse" id="act{{ $r->id }}">
                        <td colspan="5" class="bg-light">
                            <form action="{{ route('superadmin.job-reports.action', $r->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="form-group mb-2">
                                    <label class="font-weight-bold mb-1">Reason for hiding</label>
                                    <input type="text" name="hidden_reason" class="form-control form-control-sm"
                                           value="{{ \App\Models\JobReport::REASONS[$r->reason] ?? '' }}" required maxlength="500">
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger">Confirm & Hide Job</button>
                            </form>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="5" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-flag"></i>
                            <p>No reports found.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $reports->links() }}</div>

</div>

@endsection