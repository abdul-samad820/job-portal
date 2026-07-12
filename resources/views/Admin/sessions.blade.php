@extends('layouts.Admin_layout')
@section('title', 'My Devices')

@section('content')

<div class="container-fluid py-4">

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fa fa-laptop"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">My Devices</h1>
                <small class="text-muted">Everywhere you're currently logged in. If you don't recognize a device, log it out.</small>
            </div>
        </div>
        @if ($sessions->count() > 1)
        <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
            <form action="{{ route('admin.sessions.revokeOthers') }}" method="POST"
                  onsubmit="return confirm('Log out all devices except this one?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">
                    <i class="fa fa-sign-out-alt mr-1"></i> Log out all other devices
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Device / Browser</th>
                            <th>IP Address</th>
                            <th>Last Active</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                        <tr>
                            <td>
                                {{ \Illuminate\Support\Str::limit($session->user_agent, 60) }}
                                @if ($session->is_current)
                                <span class="badge badge-success ml-1">This device</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $session->ip_address }}</td>
                            <td class="text-muted">{{ $session->last_activity_human }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.sessions.revoke', $session->id) }}" method="POST"
                                      onsubmit="return confirm('Log out this device?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-sign-out-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No active sessions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection