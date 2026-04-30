@extends('layouts.superadmin')

@section('content')
<div class="container-fluid mt-4">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card border-0 shadow">

        {{-- Header --}}
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-users mr-2 text-primary"></i> Admin Management
            </h5>
        </div>

        {{-- Body --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($admins as $index => $admin)
                        <tr>

                            <td class="font-weight-bold text-muted">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>{{ $admin->company_name }}</strong>
                            </td>

                            <td class="text-muted">
                                {{ $admin->email }}
                            </td>

                            <td>
                                <span class="badge badge-light">
                                    {{ $admin->location }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($admin->is_active)
                                    <span class="badge badge-success px-3 py-2">
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">
                                        Suspended
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">

                                {{-- Suspend / Activate --}}
                                @if($admin->is_active)
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.suspend', $admin->id) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning btn-sm px-3"
                                                onclick="return confirm('Suspend this admin?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.unsuspend', $admin->id) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm px-3"
                                                onclick="return confirm('Activate this admin?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route('superadmin.admin.delete', $admin->id) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm px-3"
                                            onclick="return confirm('Delete this admin permanently?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash mr-2"></i>
                                No admins found
                            </td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection