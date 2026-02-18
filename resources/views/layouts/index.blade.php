<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Job Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('admins/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/summernote/summernote-bs4.min.css') }}">


    <!-- Custom Typography Override -->
<body class="hold-transition sidebar-mini layout-fixed">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-0">
            <!-- Left: pushmenu -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.job') }}" class="nav-link">Jobs</a>
                </li>
            </ul>

            <!-- Right: search + icons -->
            <ul class="navbar-nav ml-auto align-items-center">

                <li class="nav-item mr-3">
                    <form class="form-inline" method="GET" action="#">

                        <div class="input-group input-group-sm">
                            <input class="form-control border-0 shadow-sm" type="search" name="q"
                                placeholder="Search jobs, applicants..." value="{{ request('q') }}"
                                style="border-radius:20px 0 0 20px;">

                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit" style="border-radius:0 20px 20px 0;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>

                @php
                    $admin = auth('admin')->user();
                    $notifications = $admin->unreadNotifications;
                    $unreadCount = $notifications->count();
                @endphp

               <li class="nav-item dropdown mr-3">
    <a class="nav-link position-relative" data-toggle="dropdown" href="#">
        <i class="far fa-bell fa-lg"></i>

        @if ($unreadCount > 0)
            <span class="badge badge-danger badge-pill position-absolute"
                  style="top:-5px; right:1px; font-size:10px;">
                {{ $unreadCount }}
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-right shadow border-0 p-0"
         style="width:380px; border-radius:12px; overflow:hidden;">

        {{-- Header --}}
        <div class="px-4 py-3 bg-light d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold">Notifications</h6>
                <small class="text-muted">
                    {{ $unreadCount }} Unread
                </small>
            </div>
            <i class="fas fa-bell text-primary"></i>
        </div>

        <div class="dropdown-divider m-0"></div>

        {{-- Notification List --}}
        <div style="max-height:350px; overflow-y:auto;">

            @forelse($notifications->take(5) as $notification)

                <a href="#"
                   class="dropdown-item px-4 py-3 {{ is_null($notification->read_at) ? 'bg-light' : '' }}">

                    <div class="d-flex">

                        {{-- Icon --}}
                        <div class="mr-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width:38px; height:38px;">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-fill">

                            <div class="font-weight-bold small">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </div>

                            <div class="text-muted small mt-1">
                                {{ Str::limit($notification->data['message'] ?? '', 80) }}
                            </div>

                            <small class="text-muted d-block mt-1">
                                <i class="far fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>

                        </div>

                    </div>
                </a>

                <div class="dropdown-divider m-0"></div>

            @empty

                <div class="text-center py-5 text-muted">
                    <i class="far fa-check-circle fa-2x mb-2 text-success"></i>
                    <div class="small font-weight-bold">
                        You're all caught up
                    </div>
                </div>

            @endforelse

        </div>

        {{-- Footer --}}
        @if ($unreadCount > 0)
            <div class="text-center py-2 bg-light">

                <form method="POST" action="{{ route('admin.notifications.read') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary px-4">
                        Mark all as read
                    </button>
                </form>

            </div>
        @endif

    </div>
</li>

                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center p-0" data-toggle="dropdown" href="#"
                        style="cursor:pointer;">

                        <div class="position-relative">
                            <img src="{{ $admin->profile_image
                                ? Storage::url('admins/' . $admin->profile_image)
                                : asset('admins/dist/img/default.png') }}"
                                class="rounded-circle shadow-sm" width="38" height="38"
                                style="object-fit:cover; border:2px solid #fff;">

                            {{-- Online Dot --}}
                            <span
                                style="
                position:absolute;
                bottom:2px;
                right:2px;
                width:10px;
                height:10px;
                background:#28a745;
                border-radius:50%;
                border:2px solid #fff;">
                            </span>
                        </div>
                    </a>

                    {{-- DROPDOWN MENU --}}
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 p-0"
                        style="width:260px; border-radius:14px; overflow:hidden;">

                        {{-- HEADER --}}
                        <div class="text-center p-4" style="background: linear-gradient(135deg, #007bff, #0056b3);">

                            <img src="{{ $admin->profile_image
                                ? Storage::url('admins/' . $admin->profile_image)
                                : asset('admins/dist/img/default.png') }}"
                                class="rounded-circle shadow mb-2" width="70" height="70"
                                style="object-fit:cover; border:3px solid #fff;">

                            <div class="text-white font-weight-bold">
                                {{ $admin->company_name }}
                            </div>

                            <small class="text-white-50">
                                {{ $admin->email }}
                            </small>
                        </div>

                        {{-- MENU ITEMS --}}
                        <div class="py-2">

                            <a class="dropdown-item d-flex align-items-center py-2"
                                href="{{ route('admin.profile') }}" style="transition:0.2s;">

                                <i class="fas fa-user-circle text-primary mr-2"></i>
                                <span>My Profile</span>
                            </a>

                            <a class="dropdown-item d-flex align-items-center py-2"
                                href="{{ route('admin.dashboard') }}" style="transition:0.2s;">

                                <i class="fas fa-chart-line text-info mr-2"></i>
                                <span>Dashboard</span>
                            </a>

                            <div class="dropdown-divider my-2"></div>

                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center py-2 text-danger"
                                    style="transition:0.2s;">

                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    <span>Logout</span>
                                </button>
                            </form>

                        </div>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-light-primary jobi-sidebar">
            <div class="sidebar">

                <!-- BRAND -->
                <div class="jobi-brand text-center mb-3">
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}" class="brand-img mb-2">
                    <small class="brand-subtext">JOB HUB</small>
                </div>

                <!-- PROFILE -->

                <!-- MENU -->
                <nav class="mt-4 w-100">
                    <ul class="nav flex-column jobi-menu">

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link jobi-link active">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.job_category') }}" class="nav-link jobi-link">
                                <i class="fas fa-th-large"></i>
                                <p>Job Category</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.job_role') }}" class="nav-link jobi-link">
                                <i class="fas fa-users-cog"></i>
                                <p>Job Role</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.job') }}" class="nav-link jobi-link">
                                <i class="fas fa-briefcase"></i>
                                <p>Job</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('job_application') }}" class="nav-link jobi-link">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Application</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.selectedList') }}" class="nav-link jobi-link">
                                <i class="fas fa-user-tie"></i>
                                <p>Selected Candidate</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>
        <!-- /.sidebar -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center justify-content-end">

                        <!-- Right Side: Page Title + Subtitle -->
                        <div class="col-sm-6 text-right">
                            <h3 class="m-0">
                                <span
                                    class="badge badge-primary badge-pill px-4 py-2 d-inline-flex align-items-center font-weight-bold">
                                    @yield('title')
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content ">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong> &copy;{{ date('Y') }} <a href="">JOB HUB</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- Bootstrap -->
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- AdminLTE -->
    <script src="{{ asset('admins/dist/js/adminlte.js') }}"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <!-- Other plugins -->
    <script src="{{ asset('admins/plugins/sparklines/sparkline.js') }}"></script>
    <script src="{{ asset('admins/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('admins/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('admins/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    @stack('scripts')
</body>

</html>
