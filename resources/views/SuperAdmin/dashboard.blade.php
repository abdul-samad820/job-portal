@extends('layouts.superadmin')
@section('title', 'Super Admin Dashboard')

@section('content')

    <div class="container-fluid mt-3">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark">Super Admin Dashboard</h2>

            <a href="{{ route('superadmin.create.form') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Admin
            </a>
        </div>

        <!-- STATISTICS ROW -->
        <div class="row">

            <!-- Total Admins -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-pill">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 font-weight-bold">{{ $totalAdmins }}</h4>
                            <span class="text-muted">Total Admins</span>
                        </div>
                        <div class="stat-round-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-pill">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 font-weight-bold">{{ $totalUsers }}</h4>
                            <span class="text-muted">Total Users</span>
                        </div>
                        <div class="stat-round-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Jobs -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-pill">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 font-weight-bold">{{ $totalJobs }}</h4>
                            <span class="text-muted">Total Jobs</span>
                        </div>
                        <div class="stat-round-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Applications -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-pill">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <div>
                            {{-- <h4 class="mb-0 font-weight-bold">{{ $totalApplications }}</h4> --}}
                            <h4 class="mb-0 font-weight-bold"></h4>
                            <span class="text-muted">Total Applications</span>
                        </div>
                        <div class="stat-round-icon">
                            <i class="fas fa-pen"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- SECTION: MANAGE PANELS -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">

                <h5 class="font-weight-bold mb-3">Management Tools</h5>

                <div class="d-flex flex-wrap">

                    <a href="{{ route('superadmin.admins') }}" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                        <i class="fas fa-users"></i> Manage Admins
                    </a>

                    <a href="#" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                        <i class="fas fa-building"></i> Manage Employers
                    </a>

                    <a href="#" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                        <i class="fas fa-user"></i> Manage Users
                    </a>

                    <a href="#" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                        <i class="fas fa-briefcase"></i> All Jobs
                    </a>

                    <a href="#" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                        <i class="fas fa-chart-bar"></i> Reports & Analytics
                    </a>

                </div>

            </div>
        </div>

    </div>

@endsection
