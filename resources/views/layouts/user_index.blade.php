<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <!-- Google Fonts: Montserrat + Inter -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- AdminLTE Theme -->
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">

    @stack('styles')

    <!-- Typography Override -->
    <style>
        /* Default Body Font */
        body,
        p,
        li,
        a,
        input,
        textarea,
        select,
        label {
            font-family: 'Inter', sans-serif !important;
            font-weight: 400;
            font-size: 14px !important;
        }

        /* Headings */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 600 !important;
            font-size: 16px !important;
        }

        /* Large Page Titles */
        .page-title,
        .main-title,
        .header-title {
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 700 !important;
        }

        /* Buttons + Badges + Sidebar Links */
        .btn,
        button,
        .badge,
        .nav-link,
        .sidebar a {
            font-family: 'Inter', sans-serif !important;
            font-weight: 500 !important;
        }

        /* Active link background */
        .nav-sidebar .nav-link.active {
            background-color: #e8f1ff !important;
            /* light blue */
            color: #007bff !important;
            /* blue text */
        }

        /* Active icon color */
        .nav-sidebar .nav-link.active i {
            color: #007bff !important;
        }

        /* Hover color */
        .nav-sidebar .nav-link:hover {
            background-color: #f0f6ff !important;
            color: #006ee6 !important;
        }



        /* Force dropdown below icon on LEFT side */
        .nav-item.dropdown .dropdown-menu {
            left: auto !important;
            right: 0 !important;
            /* EXACT under the icon */
            transform: translateX(-10px) translateY(12px) !important;
            min-width: 150px;
        }

        /* Prevent inside-container overflow */
        .navbar .dropdown-menu {
            position: absolute !important;
        }

        .nav-sidebar .nav-link {
            padding: 10px 14px !important;
            border-radius: 6px;
            color: #2e3a49 !important;
            font-size: 14px;
        }

        /* Smaller logo */
        .brand-link .logo-sm {
            height: 40px;
            /* pehle 58 tha */
            width: auto;
        }

        /* Thoda compact brand height */
        .brand-link {
            padding: 8px 10px !important;
        }

        .nav-sidebar .nav-icon {
            font-size: 16px;
            width: 20px;
        }

        /* 🔔 Bell Shake */
        @keyframes bellShake {
            0% {
                transform: rotate(0);
            }

            15% {
                transform: rotate(15deg);
            }

            30% {
                transform: rotate(-10deg);
            }

            45% {
                transform: rotate(8deg);
            }

            60% {
                transform: rotate(-6deg);
            }

            75% {
                transform: rotate(4deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .notification-bell.shake {
            animation: bellShake 0.8s ease-in-out;
            transform-origin: top center;
        }

        /* 🔴 Badge Pulse */
        @keyframes pulseBadge {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        .pulse-badge {
            animation: pulseBadge 1.5s infinite;
        }

        .user-panel {
            display: flex !important;
            align-items: center !important;
        }

        /* Profile image base styling */
        .user-panel .image img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
        }

        /* When sidebar collapsed */
        .sidebar-mini.sidebar-collapse .user-panel {
            justify-content: center !important;
            text-align: center;
        }

        .sidebar-mini.sidebar-collapse .user-panel .info {
            display: none !important;
        }

        .sidebar-mini.sidebar-collapse .user-panel .image {
            margin: 0 auto !important;
        }

        .sidebar-mini.sidebar-collapse .user-panel .image img {
            width: 40px;
            height: 40px;
        }
    </style>
</head>


<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-light-primary elevation-2" style="background:#ffffff;">

            <!-- Brand Logo -->
            <a href="{{ route('user.dashboard') }}" class="brand-link d-flex align-items-center justify-content-start">

                <img src="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}" alt="Job Hub Logo"
                    class="img-fluid logo-sm">
                <span class="brand-text font-weight-bold ml-2 h5 mb-0">
                    JOB HUB
                </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- User Panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                    <div class="image">
                        @php
                            $profile = Auth::guard('user')->user()->profile ?? null;

                            $userImg =
                                $profile && $profile->profile_image
                                    ? asset('uploads/user_profile/' . $profile->profile_image)
                                    : asset('admins/dist/img/default.png');
                        @endphp

                        <img src="{{ $userImg }}" class="rounded-circle"
                            style="width:50px; height:50px; object-fit:cover;">



                    </div>
                    <div class="info ml-2">
                        <a href="{{ route('user.profile') }}" class="d-block text-capitalize font-weight-bold">
                            {{ Auth::guard('user')->user()->name ?? 'Candidate' }}
                        </a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('user.dashboard') }}"
                                class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Profile -->
                        <li class="nav-item">
                            <a href="{{ route('user.profile') }}"
                                class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>My Profile</p>
                            </a>
                        </li>

                        <!-- Applied Jobs -->
                        <li class="nav-item">
                            <a href="{{ route('user.job_applied') }}"
                                class="nav-link {{ request()->routeIs('user.job_applied') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Applied Jobs</p>
                            </a>
                        </li>

                        <!-- Saved Jobs -->
                        <li class="nav-item">
                            <a href="{{ route('user.saved.jobs') }}" class="nav-link">
                                <i class="nav-icon fas fa-bookmark"></i>
                                <p>Saved Jobs</p>
                            </a>
                        </li>

                        <!-- account_setting  -->
                        <li class="nav-item">
                            <a href="{{ route('user.account_setting') }}" class="nav-link">
                                <i class="nav-icon fas fa-key"></i>
                                <p>Account Settings</p>
                            </a>
                        </li>

                        <!-- Logout -->
                        <li class="nav-item">
                            <form action="{{ route('user.logout') }}" method="POST">
                                @csrf
                                <button class="nav-link text-left w-100 border-0 bg-transparent" type="submit">
                                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                    <p class="text-danger">Logout</p>
                                </button>
                            </form>
                        </li>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">

                @php
                    $user = Auth::guard('user')->user();
                    $unreadCount = $user->unreadNotifications->count();
                @endphp

                <li class="nav-item dropdown">

                    <a class="nav-link position-relative" data-toggle="dropdown" href="#">
                        <i class="far fa-bell notification-bell {{ $unreadCount > 0 ? 'shake' : '' }}"></i>

                        @if ($unreadCount > 0)
                            <span class="badge badge-danger navbar-badge pulse-badge">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow"
                        style="width:320px; max-height:400px; overflow:auto;">

                        <span class="dropdown-header font-weight-bold">
                            {{ $unreadCount }} New Notifications
                        </span>

                        <div class="dropdown-divider"></div>

                        @forelse($user->unreadNotifications->take(5) as $notification)
                            <div class="dropdown-item small bg-light">
                                {{ $notification->data['message'] }}
                                <br>
                                <span class="text-muted text-xs">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="dropdown-divider"></div>
                        @empty
                            <span class="dropdown-item text-muted small">
                                No new notifications
                            </span>
                        @endforelse

                        @if ($unreadCount > 0)
                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('notifications.read') }}">
                                @csrf
                                <button class="dropdown-item text-center text-primary small">
                                    Mark all as read
                                </button>
                            </form>
                        @endif

                    </div>

                </li>

            </ul>
        </nav>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark font-weight-bold">
                                @yield('title', 'Dashboard')
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('user.home') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @yield('title', 'Dashboard')
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </section>

        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-white">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer bg-white border-top" style="padding: 6px 12px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted small">
                    © {{ date('Y') }} <strong>Job Hub</strong>. All rights reserved.
                </div>
                <div class="text-muted small">
                    <b>Version</b> 1.0.0
                </div>
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('admins/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admins/dist/js/adminlte.js') }}"></script>

    <!-- PAGE ('admins/PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{ asset('admins/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('admins/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>

    @stack('scripts')
</body>

</html>
