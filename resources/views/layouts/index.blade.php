<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Job Portal')</title>
      <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

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
                <li class="nav-item">
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search"
                                    placeholder="Search jobs, companies..." aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span
                            class="badge badge-warning navbar-badge">5</span></a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">5 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-check mr-2 text-primary"></i> New application received
                            <span class="float-right text-muted text-sm">10 mins</span>
                        </a>
                    </div>
                </li>

               <!-- Profile Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">

        <img src="{{ Auth::guard('admin')->user()->profile_image 
            ? asset('uploads/admins/' . Auth::guard('admin')->user()->profile_image)
            : asset('admins/dist/img/default.png') }}"
            class="rounded-circle"
            width="34" height="34" style="object-fit:cover;">

    </a>

    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0"
         style="width: 220px; border-radius:10px;">

        <!-- Header -->
        <div class="text-center p-3 border-bottom">
            <img src="{{ Auth::guard('admin')->user()->profile_image 
                ? asset('uploads/admins/' . Auth::guard('admin')->user()->profile_image)
                : asset('admins/dist/img/default.png') }}"
                class="rounded-circle mb-2"
                width="55" height="55" style="object-fit:cover;">

            <h6 class="mb-0 font-weight-bold">{{ Auth::guard('admin')->user()->company_name }}</h6>
            <small class="text-muted">{{ Auth::guard('admin')->user()->email }}</small>
        </div>

        <!-- Menu Items -->
        <a class="dropdown-item py-2" href="{{ route('admin.profile') }}">
            <i class="fas fa-user mr-2 text-primary"></i> Profile
        </a>

        <div class="dropdown-divider"></div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="dropdown-item py-2 text-danger" type="submit">
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
