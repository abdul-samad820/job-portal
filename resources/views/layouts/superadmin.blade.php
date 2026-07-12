<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ' Super Admin Portal')</title>
    <meta name="description" content="Super Admin control panel for managing Job Hub administrators and platform settings.">
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/Job_Hub_Logo_Design.png') }}">
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

    <!-- Custom overrides load LAST so they win the cascade over AdminLTE defaults -->
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin-pro.css') }}">
    @stack('styles')


    <!-- Custom Typography Override -->
</head>

<body class="hold-transition sidebar-mini layout-fixed sa-shell">

    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top border-0">

            <ul class="navbar-nav align-items-center flex-grow-1">
                <!-- Sidebar toggle -->
                <li class="nav-item">
                    <a class="sa-header-toggle" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>

                <!-- Page title / breadcrumb (fed by @@section('title')) -->
                <!-- NOTE: the @@ above is intentional — a literal @section(...)
                     here (even inside an HTML comment) gets compiled by Blade
                     into a real, unclosed startSection() call, since Blade
                     scans raw text for directives and doesn't know about
                     HTML comments. @@ escapes it to a literal '@'. -->
                <li class="nav-item d-none d-md-block">
                    <div class="sa-breadcrumb">
                        <span class="sa-crumb-trail">Super Admin</span>
                        <span class="sa-crumb-title">@yield('title', 'Dashboard')</span>
                    </div>
                </li>

                <!-- Search -->
                <li class="nav-item ml-auto ml-md-4 d-none d-sm-block">
                    <div class="sa-header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Quick search…" aria-label="Quick search">
                    </div>
                </li>
            </ul>

            <!-- Right navbar -->
            <ul class="navbar-nav ml-auto ml-sm-2 align-items-center">

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="sa-icon-btn" data-toggle="dropdown" href="#" aria-label="Notifications">
                        <i class="far fa-bell"></i>
                        @if($saUnreadCount > 0)
                            <span class="badge badge-danger navbar-badge">{{ $saUnreadCount }}</span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow border-0 p-0 sa-notif-menu">

                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-center sa-notif-header">
                            <div>
                                <h6 class="mb-0">Notifications</h6>
                                <small class="text-muted">{{ $saUnreadCount }} unread</small>
                            </div>
                            <i class="fas fa-bell text-primary"></i>
                        </div>

                        <div class="dropdown-divider m-0"></div>

                        {{-- List --}}
                        <div class="sa-notif-list">
                            @forelse($saNotifications as $notification)
                                <a href="{{ route('notifications.open', $notification->id) }}" class="dropdown-item sa-notif-item">
                                    <span class="sa-notif-icon"><i class="fas fa-bell"></i></span>
                                    <span class="sa-notif-body">
                                        <span class="sa-notif-title">
                                            {{ \Illuminate\Support\Str::limit($notification->data['title'] ?? 'Notification', 40) }}
                                        </span>
                                        @if(!empty($notification->data['message']))
                                            <span class="sa-notif-message">
                                                {{ \Illuminate\Support\Str::limit($notification->data['message'], 70) }}
                                            </span>
                                        @endif
                                        <span class="sa-notif-time">
                                            <i class="far fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                            @empty
                                <div class="sa-notif-empty">
                                    <i class="far fa-check-circle"></i>
                                    <div>You're all caught up</div>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        @if($saUnreadCount > 0)
                            <div class="dropdown-divider m-0"></div>
                            <div class="text-center py-2">
                                <form method="POST" action="{{ route('superadmin.notifications.read') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary px-4">
                                        Mark all as read
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="sa-header-profile" data-toggle="dropdown" href="#">
                        <span class="sa-avatar-circle">{{ strtoupper(substr($superAdminUser->email ?? 'S', 0, 1)) }}</span>
                        <span class="d-none d-lg-flex flex-column">
                            <span class="sa-header-name">Super Admin</span>
                            <span class="sa-header-role">{{ \Illuminate\Support\Str::limit($superAdminUser->email ?? '', 22) }}</span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-2">
                        <span class="dropdown-item-text text-muted small">{{ $superAdminUser->email ?? '' }}</span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('superadmin.profile') }}">
                            <i class="fas fa-user mr-2 text-muted"></i> Profile
                        </a>
                        <a class="dropdown-item" href="{{ route('superadmin.settings') }}">
                            <i class="fas fa-cogs mr-2 text-muted"></i> Settings
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

            <!-- Fixed top block (logo + profile) — stays pinned while the
                 menu below scrolls independently. Kept OUTSIDE the .sidebar
                 div because AdminLTE's OverlayScrollbars plugin attaches
                 its custom scrollbar to the .sidebar element specifically;
                 if brand/profile lived inside it, they'd scroll away too. -->
            <div class="jobi-sidebar-fixed">
                <div class="jobi-brand mb-0">
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" class="brand-img" alt="Job Hub logo">
                    <div class="sa-brand-text">
                        <span class="sa-brand-title">Job Hub</span>
                        <small class="brand-subtext">Control Panel</small>
                    </div>
                </div>

                <div class="jobi-profile mb-0">
                    <img src="{{ asset('admins/dist/img/user2-160x160.jpg') }}" class="profile-img" alt="Admin avatar">
                    <div class="sa-profile-meta">
                        <div class="sa-profile-name">{{ \Illuminate\Support\Str::before($superAdminUser->email ?? 'Super Admin', '@') }}</div>
                        <span class="sa-profile-role-badge"><i class="fas fa-shield-alt"></i>Super Admin</span>
                    </div>
                </div>
            </div>

            <div class="sidebar">

                <!-- SUPER ADMIN MENU (scrollable region) -->
                <nav class="mt-1 w-100">
                    <ul class="nav flex-column jobi-menu">

                        <p class="sa-nav-heading">Overview</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.dashboard',
                            'icon' => 'fas fa-chart-line',
                            'label' => 'Dashboard',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Platform</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.admins',
                            'icon' => 'fas fa-users-cog',
                            'label' => 'Manage Admins',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.users',
                            'icon' => 'fas fa-user',
                            'label' => 'Manage Users',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.jobs',
                            'icon' => 'fas fa-briefcase',
                            'label' => 'All Jobs',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Moderation</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.job-reports',
                            'icon' => 'fas fa-flag',
                            'label' => 'Job Reports',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.testimonials',
                            'icon' => 'fas fa-quote-left',
                            'label' => 'Testimonials',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.contact.index',
                            'icon' => 'fas fa-envelope',
                            'label' => 'Contact Messages',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Content</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.faqs',
                            'icon' => 'fas fa-question-circle',
                            'label' => 'Global FAQs',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.broadcast',
                            'icon' => 'fas fa-bullhorn',
                            'label' => 'Broadcast Announcement',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Insights</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.reports',
                            'icon' => 'fas fa-chart-bar',
                            'label' => 'Reports & Analytics',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.activity-log',
                            'icon' => 'fas fa-history',
                            'label' => 'Activity Log',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Account</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.settings',
                            'icon' => 'fas fa-cogs',
                            'label' => 'Settings',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'superadmin.profile',
                            'icon' => 'fas fa-user-circle',
                            'label' => 'My Profile',
                            'linkClass' => 'jobi-link',
                        ])

                    </ul>
                </nav>

            </div>
        </aside>
        <!-- /.sidebar -->

        <!-- Content Wrapper -->
        <div class="content-wrapper">

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
                <b>Version</b> 1.3
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
    <script src="{{ asset('js/global-loading.js') }}"></script>
</body>

</html>