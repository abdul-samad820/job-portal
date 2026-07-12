@extends('layouts.User_layout')
@section('title', 'Login Activity')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-history"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Login Activity</h1>
                <small class="text-muted">A record of logins, logouts, and account changes for your account — if something here looks unfamiliar, change your password immediately.</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow rounded">
        @if ($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="font-weight-bold">Activity</th>
                        <th class="font-weight-bold">IP Address</th>
                        <th class="font-weight-bold">Device / Browser</th>
                        <th class="font-weight-bold">When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                    <tr>
                        <td>
                            @if ($log->action === 'login')
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                <i class="fas fa-sign-in-alt mr-1"></i> {{ $log->action_label }}
                            </span>
                            @elseif ($log->action === 'logout')
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                <i class="fas fa-sign-out-alt mr-1"></i> {{ $log->action_label }}
                            </span>
                            @elseif ($log->action === 'password_changed')
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="fas fa-key mr-1"></i> {{ $log->action_label }}
                            </span>
                            @else
                            <span class="badge bg-info px-3 py-2 rounded-pill">
                                <i class="fas fa-info-circle mr-1"></i> {{ $log->action_label }}
                            </span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-muted text-truncate d-inline-block u-maxw-280px">
                            {{ $log->user_agent ?? '—' }}
                        </td>
                        <td class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $logs->links('pagination::bootstrap-4') }}
        </div>
        @else
        <div class="p-5 text-center text-muted">
            <i class="fas fa-history fa-3x mb-3"></i>
            <p class="fs-5 mb-0">No activity recorded yet.</p>
        </div>
        @endif
    </div>

</div>

@endsection