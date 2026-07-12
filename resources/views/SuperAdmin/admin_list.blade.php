@extends('layouts.superadmin')
@section('title', 'Manage Admins')

@section('content')
<div class="container-fluid mt-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-users-cog',
        'title' => 'Admin Management',
        'subtitle' => 'Every company/recruiter account on the platform.',
    ])

    {{-- Error Alert --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong>Can't do that.</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

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
    <div class="card border-0 shadow-sm">

        {{-- Header --}}
        <div class="card-header bg-white">
            <div class="sa-toolbar mb-0">
                <div class="sa-filter-pills">
                    <a href="{{ route('superadmin.admins') }}" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                    <a href="{{ route('superadmin.admins', ['status' => 'active']) }}" class="btn btn-sm {{ $status == 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">Active</a>
                    <a href="{{ route('superadmin.admins', ['status' => 'suspended']) }}" class="btn btn-sm {{ $status == 'suspended' ? 'btn-primary' : 'btn-outline-secondary' }}">Suspended</a>
                </div>
                <div class="d-flex align-items-center" style="gap:.5rem;">
                    <form method="GET" class="form-inline">
                        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                        <input type="text" name="search" class="form-control form-control-sm mr-2"
                               placeholder="Search company or email..." value="{{ $search }}">
                        <button class="btn btn-sm btn-outline-primary">Search</button>
                    </form>
                    <a href="{{ route('superadmin.admins.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-csv mr-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Bulk actions bar (enabled once a row is checked) --}}
        <div class="sa-bulk-bar d-none align-items-center" id="bulkBar">
            <span class="sa-bulk-count" id="bulkCount"></span>
            <button type="button" class="btn btn-sm btn-warning" onclick="submitBulk('suspend')">Suspend Selected</button>
            <button type="button" class="btn btn-sm btn-success" onclick="submitBulk('unsuspend')">Unsuspend Selected</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="submitBulk('delete')">Delete Selected</button>
        </div>

        {{-- Body --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead class="thead-light">
                        <tr>
                            <th style="width:30px"><input type="checkbox" id="selectAll"></th>
                            <th>#</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($admins as $index => $admin)
                        <tr>

                            <td>
                                <input type="checkbox" class="row-check" value="{{ $admin->id }}">
                            </td>

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

                            {{-- Verified --}}
                            <td>
                                @if($admin->is_verified)
                                    <span class="badge badge-primary px-3 py-2"><i class="fas fa-check-circle mr-1"></i>Verified</span>
                                @else
                                    <span class="badge badge-light border px-3 py-2">Unverified</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                              <div class="sa-row-actions">

                                {{-- Edit --}}
                                <a href="{{ route('superadmin.admin.edit', $admin->id) }}" class="btn btn-primary sa-icon-action" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Suspend / Activate --}}
                                @if($admin->is_active)
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.suspend', $admin->id) }}"
                                          class="d-inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning sa-icon-action" title="Suspend"
                                                onclick="return confirm('Suspend this admin?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.unsuspend', $admin->id) }}"
                                          class="d-inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success sa-icon-action" title="Activate"
                                                onclick="return confirm('Activate this admin?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Verify / Unverify --}}
                                @if($admin->is_verified)
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.unverify', $admin->id) }}"
                                          class="d-inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-outline-primary sa-icon-action" title="Remove verification"
                                                onclick="return confirm('Remove verified status from {{ addslashes($admin->company_name) }}?')">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('superadmin.admin.verify', $admin->id) }}"
                                          class="d-inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-primary sa-icon-action" title="Mark as verified"
                                                onclick="return confirm('Mark {{ addslashes($admin->company_name) }} as a verified company?')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route('superadmin.admin.delete', $admin->id) }}"
                                      class="d-inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger sa-icon-action" title="Delete"
                                            onclick="return confirm('Delete {{ addslashes($admin->company_name) }}? This is only possible when they have 0 jobs and 0 applications. They currently have {{ $admin->jobs_count }} job(s) and {{ $admin->job_applications_count }} application(s).')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                              </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="sa-empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <p>No admins found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>

            <div class="mt-3 px-3">
                {{ $admins->links() }}
            </div>

        </div>
    </div>

    {{-- Standalone bulk-action form (kept OUTSIDE the table so it never
         nests inside the per-row suspend/unsuspend/delete forms above —
         nested <form> tags are invalid HTML and break unpredictably).
         JS injects the selected admin_ids as hidden inputs before submit. --}}
    <form method="POST" action="{{ route('superadmin.admins.bulk') }}" id="bulkForm" class="d-none">
        @csrf
        <input type="hidden" name="action" id="bulkActionInput">
    </form>

</div>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});

document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBulkBar));

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    const bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = checked + ' selected';
    bar.classList.toggle('d-none', checked === 0);
    bar.classList.toggle('d-flex', checked > 0);
}

function submitBulk(action) {
    const checkedBoxes = document.querySelectorAll('.row-check:checked');
    if (checkedBoxes.length === 0) return;

    const label = { suspend: 'suspend', unsuspend: 'unsuspend', delete: 'permanently delete' }[action];
    if (!confirm(`Are you sure you want to ${label} ${checkedBoxes.length} admin(s)?`)) return;

    const form = document.getElementById('bulkForm');

    // Remove any hidden inputs from a previous submit attempt, then add
    // one fresh admin_ids[] input per currently-checked row.
    form.querySelectorAll('input[name="admin_ids[]"]').forEach(el => el.remove());
    checkedBoxes.forEach(cb => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'admin_ids[]';
        hidden.value = cb.value;
        form.appendChild(hidden);
    });

    document.getElementById('bulkActionInput').value = action;
    form.submit();
}
</script>
@endpush
@endsection
