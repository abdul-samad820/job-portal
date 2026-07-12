@extends('layouts.superadmin')
@section('title', 'Users')

@section('content')

<div class="container-fluid py-4">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-users',
        'title' => 'Users',
        'subtitle' => 'Directory of registered job seekers. Suspend an account to block login for fake or abusive users.',
    ])

    <div class="card border-0 shadow-sm">

        {{-- Header --}}
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <div class="sa-filter-pills">
                    <a href="{{ route('superadmin.users', array_filter(['search' => $search])) }}" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                    <a href="{{ route('superadmin.users', array_filter(['search' => $search, 'status' => 'active'])) }}" class="btn btn-sm {{ $status == 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">Active</a>
                    <a href="{{ route('superadmin.users', array_filter(['search' => $search, 'status' => 'suspended'])) }}" class="btn btn-sm {{ $status == 'suspended' ? 'btn-primary' : 'btn-outline-secondary' }}">Suspended</a>
                </div>
                <div class="d-flex align-items-center" style="gap:.5rem;">
                    <form method="GET" class="form-inline">
                        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                        <input type="text" name="search" class="form-control form-control-sm mr-2"
                               placeholder="Search name or email..." value="{{ $search }}">
                        <button class="btn btn-sm btn-outline-primary">Search</button>
                    </form>
                    <a href="{{ route('superadmin.users.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-csv mr-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th style="width:110px">Applications</th>
                            <th style="width:100px">Saved Jobs</th>
                            <th style="width:100px">Status</th>
                            <th style="width:110px" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="font-weight-bold">{{ $user->name }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td><span class="badge badge-light border">{{ $user->job_applications_count }}</span></td>
                            <td><span class="badge badge-light border">{{ $user->saved_jobs_count }}</span></td>
                            <td>
                                @if ($user->is_active)
                                <span class="badge badge-success px-3 py-2">Active</span>
                                @else
                                <span class="badge badge-danger px-3 py-2">Suspended</span>
                                @endif
                            </td>
                            <td class="text-center">
                              <div class="sa-row-actions justify-content-center">
                                @if ($user->is_active)
                                <form action="{{ route('superadmin.users.suspend', $user->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Suspend {{ addslashes($user->name) }}? They will be logged out and unable to log back in.')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-danger sa-icon-action" title="Suspend">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('superadmin.users.unsuspend', $user->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Reactivate {{ addslashes($user->name) }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-success sa-icon-action" title="Reactivate">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                </form>
                                @endif
                              </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="p-0">
                            <div class="sa-empty-state">
                                <i class="fas fa-user-slash"></i>
                                <p>No users found.</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 px-3">{{ $users->appends(request()->query())->links() }}</div>

        </div>
    </div>

</div>

@endsection