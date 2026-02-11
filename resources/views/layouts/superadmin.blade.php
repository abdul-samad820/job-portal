<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ' Super Admin Portal')</title>
      <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
 <style>
        .super-badge {
            padding: 8px 18px;
            font-size: 15px;
            border-radius: 20px;
        }
    </style>
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

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">

    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top border-0">

            <ul class="navbar-nav">
                <!-- Sidebar toggle -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>

                <!-- Top Nav Links -->
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('superadmin.dashboard') }}" class="nav-link">Dashboard</a>
                </li>

                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('superadmin.admins') }}" class="nav-link">Admins</a>
                </li>
            </ul>

            <!-- Right navbar -->
            <ul class="navbar-nav ml-auto">

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">3</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">3 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user-plus mr-2 text-primary"></i> New Admin Added
                            <span class="float-right text-muted text-sm">5 mins</span>
                        </a>
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-2">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('superadmin.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>

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
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}" class="brand-img mb-2" style="height:55px;">
                    <small class="brand-subtext">JOB HUB — Super Admin</small>
                </div>

                <!-- PROFILE -->
                <div class="jobi-profile text-center mb-3">
                    <img src="{{ asset('admins/dist/img/user2-160x160.jpg') }}" class="profile-img mb-2" style="border-radius:50%; height:80px;">
                    {{-- <h6 class="font-weight-bold mb-1">{{ auth('admin')->user()->company_name }}</h6>
                    <small>{{ auth('admin')->user()->email }}</small> --}}
                </div>

                <!-- SUPER ADMIN MENU -->
                <nav class="mt-4 w-100">
                    <ul class="nav flex-column jobi-menu">

                        <li class="nav-item mb-2">
                            <a href="{{ route('superadmin.dashboard') }}" class="nav-link jobi-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('superadmin.admins') }}" class="nav-link jobi-link {{ request()->routeIs('superadmin.admins') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                <p>Manage Admins</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link jobi-link">
                                <i class="fas fa-building"></i>
                                <p>Manage Employers</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link jobi-link">
                                <i class="fas fa-user"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link jobi-link">
                                <i class="fas fa-briefcase"></i>
                                <p>All Jobs</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link jobi-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>Reports & Analytics</p>
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link jobi-link">
                                <i class="fas fa-cogs"></i>
                                <p>Settings</p>
                            </a>
                        </li>

                    </ul>
                </nav>

            </div>
        </aside>
        <!-- /.sidebar -->

        <!-- Content Wrapper -->
        <div class="content-wrapper">

            <div class="content-header">
                <div class="container-fluid">

                    <div class="row mb-2">
                        <div class="col-sm-12 text-right">
                            <h3 class="m-0">
                                <span class="badge badge-primary super-badge">
                                    @yield('title')
                                </span>
                            </h3>
                        </div>
                    </div>

                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>

        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} JOB HUB.</strong> All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

    </div>
 <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

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
