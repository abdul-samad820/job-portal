@extends('layouts.User_layout')
@section('title', 'My Invites')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-envelope-open-text"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Job Invites</h1>
                <small class="text-muted">Companies you're a fit for can invite you to apply directly — turn on
            <a href="{{ route('user.profile') }}">"Open to Work"</a> on your profile so recruiters can find you.</small>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($invites as $invite)
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="font-weight-bold text-primary mb-0">{{ $invite->job->title ?? 'Job removed' }}</h6>
                        @if ($invite->status === 'applied')
                        <span class="badge bg-success">Applied</span>
                        @elseif ($invite->status === 'dismissed')
                        <span class="badge bg-secondary">Dismissed</span>
                        @else
                        <span class="badge bg-primary">New</span>
                        @endif
                    </div>
                    <p class="text-muted small mb-2">
                        Invited by {{ $invite->admin->company_name ?? 'A company' }}
                        on {{ $invite->created_at->format('d M Y') }}
                    </p>

                    @if ($invite->message)
                    <p class="mb-3 font-italic">"{{ $invite->message }}"</p>
                    @endif

                    @if ($invite->job && $invite->status !== 'applied')
                    <a href="{{ route('apply_form_job_application', ['id' => $invite->job_id]) }}"
                        class="btn btn-primary btn-sm rounded-pill">
                        <i class="fas fa-paper-plane mr-1"></i> View & Apply
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="p-5 text-center text-muted bg-white shadow-sm rounded">
                <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
                <p class="fs-5 mb-0">No invites yet.</p>
                <p class="text-muted">Complete your profile and turn on "Open to Work" to get noticed by recruiters.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>

@endsection
