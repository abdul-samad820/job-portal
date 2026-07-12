@extends('layouts.superadmin')
@section('title', 'Dashboard')

@section('content')

    <div class="sa-page-header">
        <div>
            <h1 class="sa-page-title font-weight-bold text-dark mb-0">Welcome back, Super Admin</h1>
            <small class="text-muted">Here's what's happening across the platform today.</small>
        </div>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAdminModal">
            <i class="fas fa-user-plus mr-1"></i> Add New Admin
        </button>
    </div>

    @if($pendingJobReports > 0 || $pendingTestimonials > 0 || $unreadMessages > 0)
    <!-- NEEDS ATTENTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-uppercase text-muted mb-3" style="letter-spacing: .05em; font-size: .75rem;">
                <i class="fas fa-exclamation-circle text-warning mr-1"></i> Needs Your Attention
            </h6>
            <div class="d-flex flex-wrap" style="gap: .75rem;">
                @if($pendingJobReports > 0)
                    <a href="{{ route('superadmin.job-reports') }}" class="d-flex align-items-center px-3 py-2 rounded-lg" style="background: rgba(220,53,69,.08); border: 1px solid rgba(220,53,69,.15); text-decoration: none;">
                        <span class="badge badge-danger badge-pill mr-2">{{ $pendingJobReports }}</span>
                        <span class="text-dark font-weight-medium">Job report{{ $pendingJobReports > 1 ? 's' : '' }} to review</span>
                    </a>
                @endif
                @if($pendingTestimonials > 0)
                    <a href="{{ route('superadmin.testimonials', ['filter' => 'pending']) }}" class="d-flex align-items-center px-3 py-2 rounded-lg" style="background: rgba(255,193,7,.10); border: 1px solid rgba(255,193,7,.2); text-decoration: none;">
                        <span class="badge badge-warning badge-pill mr-2">{{ $pendingTestimonials }}</span>
                        <span class="text-dark font-weight-medium">Testimonial{{ $pendingTestimonials > 1 ? 's' : '' }} pending approval</span>
                    </a>
                @endif
                @if($unreadMessages > 0)
                    <a href="{{ route('superadmin.contact.index') }}" class="d-flex align-items-center px-3 py-2 rounded-lg" style="background: rgba(37,99,235,.08); border: 1px solid rgba(37,99,235,.15); text-decoration: none;">
                        <span class="badge badge-primary badge-pill mr-2">{{ $unreadMessages }}</span>
                        <span class="text-dark font-weight-medium">Unread contact message{{ $unreadMessages > 1 ? 's' : '' }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- STATISTICS ROW -->
    <div class="row">

        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable">
               <div class="p-3 d-flex justify-content-between align-items-center stat-card-body">
    <div>
      <div class="d-flex align-items-center flex-wrap mb-1">
    <div class="stat-value mr-2 mb-0">{{ number_format($totalAdmins) }}</div>
    <span class="badge badge-success badge-pill stat-mini-badge">{{ $activeAdmins }} active</span>
</div>
<div class="stat-label">Total Admins</div>
    </div>
    <div class="stat-round-icon">
        <i class="fas fa-users-cog"></i>
    </div>
</div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($totalUsers) }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-round-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($totalJobs) }}</div>
                        <div class="stat-label">Total Jobs</div>
                    </div>
                    <div class="stat-round-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="card stat-pill sa-hoverable">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value mb-1">{{ number_format($totalApplications) }}</div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-round-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SECTION: QUICK ACCESS -->
    <div class="card shadow-sm border-0 mt-2">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="font-weight-bold mb-0">Quick Access</h5>
            <small class="text-muted">Jump straight to a section</small>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    $quickLinks = [
                        ['route' => 'superadmin.admins', 'icon' => 'fa-users-cog', 'label' => 'Manage Admins'],
                        ['route' => 'superadmin.users', 'icon' => 'fa-user', 'label' => 'Manage Users'],
                        ['route' => 'superadmin.jobs', 'icon' => 'fa-briefcase', 'label' => 'All Jobs'],
                        ['route' => 'superadmin.reports', 'icon' => 'fa-chart-bar', 'label' => 'Reports & Analytics'],
                        ['route' => 'superadmin.broadcast', 'icon' => 'fa-bullhorn', 'label' => 'Broadcast Announcement'],
                        ['route' => 'superadmin.settings', 'icon' => 'fa-cogs', 'label' => 'Settings'],
                    ];
                @endphp
                @foreach($quickLinks as $link)
                    <div class="col-md-4 col-6 mb-3">
                        <a href="{{ route($link['route']) }}" class="d-flex align-items-center p-3 rounded-lg hover-bg" style="border: 1px solid #edf2f7; text-decoration: none;">
                            <div class="stat-round-icon mr-3" style="width:38px;height:38px;font-size:14px;">
                                <i class="fas {{ $link['icon'] }}"></i>
                            </div>
                            <span class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $link['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('partials.create-admin-modal')

@endsection
