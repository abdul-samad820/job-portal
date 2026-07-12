@extends('layouts.User_layout')
@section('title', 'Companies I Follow')

@section('content')

<div class="container py-4">

    @if (session('success'))
    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-building"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Companies I Follow</h1>
                <small class="text-muted">You'll be notified whenever these companies post a new job.</small>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($follows as $follow)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded h-100">
                <div class="card-body p-4 text-center">
                    <img src="{{ $follow->company && $follow->company->profile_image
                            ? Storage::url('admins/' . $follow->company->profile_image)
                            : asset('admins/dist/img/default.png') }}"
                        class="rounded-circle mb-3 u-w-70px-h-70px-fit-cover" alt="Company logo">

                    <h6 class="font-weight-bold mb-1">{{ $follow->company->company_name ?? 'Company' }}</h6>
                    <p class="text-muted small mb-3">Following since {{ $follow->created_at->format('d M Y') }}</p>

                    <form action="{{ route('user.following.toggle', $follow->admin_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-4">
                            <i class="fas fa-times mr-1"></i> Unfollow
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="p-5 text-center text-muted bg-white shadow-sm rounded">
                <i class="fas fa-building fa-3x mb-3"></i>
                <p class="fs-5 mb-0">You're not following any companies yet.</p>
                <p class="text-muted">Visit a job listing and hit "Follow Company" to get notified about their new jobs.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>

@endsection