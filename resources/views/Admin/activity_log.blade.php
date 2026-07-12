@extends('layouts.Admin_layout')
@section('title', 'Activity Log')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-history',
        'title' => 'Activity Log',
        'subtitle' => 'A record of actions taken on your account — jobs posted, edited, deleted, and application status changes.',
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>When</th>
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
                                <span class="badge badge-secondary">{{ str_replace('_', ' ', $log->action) }}</span>
                            </td>
                            <td>{{ $log->description }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>

</div>

@endsection