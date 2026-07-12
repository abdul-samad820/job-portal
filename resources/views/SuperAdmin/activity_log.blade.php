@extends('layouts.superadmin')
@section('title', 'Activity Log')

@section('content')

<div class="container-fluid">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-history',
        'title' => 'Activity Log',
        'subtitle' => 'Unified audit trail — actions taken by SuperAdmin, and by company admins on their own accounts.',
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>When</th>
                        <th>Actor</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">
                            {{ $log->created_at->format('d M Y, h:i A') }}
                            <br><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $log->admin_id ? 'badge-info' : 'badge-dark' }} mb-1">
                                {{ $log->actor_role }}
                            </span>
                            <br>
                            <small class="text-muted">
                                {{ $log->actor->company_name ?? $log->actor->email ?? 'Unknown' }}
                            </small>
                        </td>
                        <td>
                            @php
                                $badgeMap = [
                                    'admin_created' => 'success', 'admin_updated' => 'info',
                                    'admin_suspended' => 'warning', 'admin_unsuspended' => 'success',
                                    'admin_deleted' => 'danger', 'job_hidden' => 'warning',
                                    'job_unhidden' => 'success', 'job_deleted' => 'danger',
                                    'admin_verified' => 'primary', 'admin_unverified' => 'secondary',
                                    'user_suspended' => 'warning', 'user_unsuspended' => 'success',
                                    'job_created' => 'success', 'job_updated' => 'info',
                                    'application_status_updated' => 'info', 'superadmin_login' => 'dark',
                                ];
                                $badge = $badgeMap[$log->action] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ str_replace('_', ' ', $log->action) }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-history"></i>
                            <p>No activity recorded yet.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>

</div>

@endsection