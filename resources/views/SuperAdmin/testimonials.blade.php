@extends('layouts.superadmin')
@section('title', 'Testimonials')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-quote-left',
        'title' => 'Testimonials — Platform Oversight',
        'subtitle' => 'Every testimonial across all companies. Approve, reject, or remove any of them.',
        'badge' => $pendingCount > 0 ? ['text' => $pendingCount.' pending', 'class' => 'badge-warning'] : null,
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <div class="sa-filter-pills">
                <a href="{{ route('superadmin.testimonials') }}" class="btn btn-sm {{ !$filter ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('superadmin.testimonials', ['filter' => 'pending']) }}" class="btn btn-sm {{ $filter == 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('superadmin.testimonials', ['filter' => 'approved']) }}" class="btn btn-sm {{ $filter == 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">Approved</a>
                <a href="{{ route('superadmin.testimonials', ['filter' => 'rejected']) }}" class="btn btn-sm {{ $filter == 'rejected' ? 'btn-primary' : 'btn-outline-secondary' }}">Rejected</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Review</th>
                        <th>Source</th>
                        <th style="width:110px">Status</th>
                        <th style="width:110px" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $t)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $t->name }}</div>
                            <small class="text-muted">{{ $t->designation }} {{ $t->company ? '· '.$t->company : '' }}</small>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($t->review, 80) }}</td>
                        <td>
                            @if($t->isUserSubmitted())
                                <span class="badge badge-info">User submitted</span>
                            @elseif($t->admin)
                                <span class="badge badge-light border">{{ $t->admin->company_name }}</span>
                            @else
                                <span class="badge badge-light border">Global</span>
                            @endif
                        </td>
                        <td>
                            @if($t->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($t->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-secondary">Rejected</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="sa-row-actions justify-content-end">
                                @if($t->status !== 'approved')
                                <form action="{{ route('superadmin.testimonials.approve', $t->id) }}" method="POST" class="d-inline-flex">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-success sa-icon-action" title="Approve"><i class="fa fa-check"></i></button>
                                </form>
                                @endif
                                @if($t->status !== 'rejected')
                                <form action="{{ route('superadmin.testimonials.reject', $t->id) }}" method="POST" class="d-inline-flex">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-warning sa-icon-action" title="Reject"><i class="fa fa-times"></i></button>
                                </form>
                                @endif
                                <form action="{{ route('superadmin.testimonials.destroy', $t->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Permanently delete this testimonial?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger sa-icon-action" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-quote-left"></i>
                            <p>No testimonials found.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $testimonials->links() }}</div>

</div>

@endsection