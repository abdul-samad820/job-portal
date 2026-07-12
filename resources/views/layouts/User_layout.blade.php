<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Portal')</title>
    <meta name="description" content="@yield('meta_description', 'Find and apply to jobs, track your applications, and manage your profile on Job Hub.')">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <!-- Shared jobi- design system (sidebar/header positioning + generic components) -->
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-user-file.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-pro.css') }}">
</head>


<body class="hold-transition sidebar-mini layout-fixed user-shell">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-light-primary jobi-sidebar">

            <div class="jobi-sidebar-fixed">
                <div class="jobi-brand mb-0">
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" class="brand-img" alt="Job Hub logo">
                    <div class="sa-brand-text">
                        <span class="sa-brand-title">Job Hub</span>
                        <small class="brand-subtext">Candidate Portal</small>
                    </div>
                </div>

                <div class="jobi-profile mb-0">
                    {{-- $userImg injected by App\View\Composers\UserLayoutComposer --}}
                    <img src="{{ $userImg }}" class="profile-img" style="object-fit: cover;" alt="User avatar">
                    <div class="sa-profile-meta">
                        <div class="sa-profile-name">{{ \Illuminate\Support\Str::limit($authUser->name ?? 'Candidate', 18) }}</div>
                        <span class="sa-profile-role-badge"><i class="fas fa-user"></i>Job Seeker</span>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-1 w-100">
                    <ul class="nav flex-column jobi-menu" data-widget="treeview" role="menu" data-accordion="false">

                        <p class="sa-nav-heading">Overview</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.dashboard',
                            'icon' => 'fas fa-tachometer-alt',
                            'label' => 'Dashboard',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.profile',
                            'icon' => 'fas fa-user',
                            'label' => 'My Profile',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Job Search</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.job_applied',
                            'icon' => 'fas fa-briefcase',
                            'label' => 'Applied Jobs',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.saved.jobs',
                            'icon' => 'fas fa-bookmark',
                            'label' => 'Saved Jobs',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'job.alert.index',
                            'icon' => 'fas fa-bell',
                            'label' => 'Job Alerts',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.following.index',
                            'icon' => 'fas fa-building',
                            'label' => 'Following',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Interviews</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.interviews',
                            'icon' => 'fas fa-calendar-alt',
                            'label' => 'My Interviews',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.invites',
                            'icon' => 'fas fa-envelope-open-text',
                            'label' => 'My Invites',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Resume Tools</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.resumes.index',
                            'icon' => 'fas fa-file-pdf',
                            'label' => 'My Resumes',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.resume_builder',
                            'icon' => 'fas fa-file-alt',
                            'label' => 'Resume Builder',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.resume_score',
                            'icon' => 'fas fa-clipboard-check',
                            'label' => 'Resume Score',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Insights</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.salary_insights',
                            'icon' => 'fas fa-chart-line',
                            'label' => 'Salary Insights',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.referrals',
                            'icon' => 'fas fa-user-plus',
                            'label' => 'Refer a Friend',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Account</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.account_setting',
                            'icon' => 'fas fa-key',
                            'label' => 'Account Settings',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'user.activity_log',
                            'icon' => 'fas fa-history',
                            'label' => 'Login Activity',
                            'linkClass' => 'jobi-link',
                        ])

                        <!-- Logout -->
                        <li class="nav-item">
                            <form action="{{ route('user.logout') }}" method="POST">
                                @csrf
                                <button class="nav-link jobi-link text-left w-100 border-0 bg-transparent" type="submit">
                                    <i class="fas fa-sign-out-alt text-danger"></i>
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

        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top border-0">
            <ul class="navbar-nav align-items-center flex-grow-1">
                <li class="nav-item">
                    <a class="sa-header-toggle" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>

                <li class="nav-item d-none d-md-block">
                    <div class="sa-breadcrumb">
                        <span class="sa-crumb-trail">Candidate Portal</span>
                        <span class="sa-crumb-title">@yield('title', 'Dashboard')</span>
                    </div>
                </li>

                <li class="nav-item ml-auto ml-md-4 d-none d-sm-block">
                    <form action="{{ route('user.jobs') }}" method="GET" class="sa-header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Find your next job..." autocomplete="off">
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto ml-sm-2 align-items-center">

                {{-- $authUser, $notifications, $unreadCount injected by App\View\Composers\UserLayoutComposer --}}

                <li class="nav-item dropdown">

                    <a class="sa-icon-btn" data-toggle="dropdown" href="#" aria-label="Notifications">
                        <i class="far fa-bell notification-bell {{ $unreadCount > 0 ? 'shake' : '' }}"></i>

                        @if ($unreadCount > 0)
                        <span class="badge badge-danger navbar-badge pulse-badge bell">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow u-w-320px-ovf-auto">

                        <span class="dropdown-header font-weight-bold">
                            {{ $unreadCount }} New Notifications
                        </span>

                        <div class="dropdown-divider"></div>

                        @forelse($notifications as $notification)
                        <a href="{{ route('notifications.open', $notification->id) }}" class="dropdown-item small {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            {{ $notification->data['message'] }}
                            <br>
                            <span class="text-muted text-xs">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                        @empty
                        <span class="dropdown-item text-muted small">
                            No new notifications
                        </span>
                        @endforelse

                        @if ($unreadCount > 0)
                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('user.notifications.read') }}">
                            @csrf
                            <button class="dropdown-item text-center text-primary small">
                                Mark all as read
                            </button>
                        </form>
                        @endif

                    </div>

                </li>

                <li class="nav-item dropdown">
                    <a class="sa-header-profile" data-toggle="dropdown" href="#">
                        <span class="sa-avatar-wrap position-relative d-inline-block">
                            <img src="{{ $userImg }}" class="sa-avatar-circle" style="object-fit: cover;" alt="User avatar">
                            @if ($navBadgeTier)
                            @php
                                $navBadgeMeta = [
                                    'bronze' => ['icon' => 'fa-medal', 'color' => '#cd7f32'],
                                    'silver' => ['icon' => 'fa-medal', 'color' => '#adadad'],
                                    'gold' => ['icon' => 'fa-trophy', 'color' => '#e6b800'],
                                ][$navBadgeTier];
                            @endphp
                            <span class="sa-nav-badge" style="background: {{ $navBadgeMeta['color'] }};"
                                title="{{ ucfirst($navBadgeTier) }} Referrer">
                                <i class="fas {{ $navBadgeMeta['icon'] }}"></i>
                            </span>
                            @endif
                        </span>
                        <span class="d-none d-lg-flex flex-column">
                            <span class="sa-header-name">{{ \Illuminate\Support\Str::limit($authUser->name ?? 'Candidate', 18) }}</span>
                            <span class="sa-header-role">Job Seeker</span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-2">
                        <a class="dropdown-item" href="{{ route('user.profile') }}">
                            <i class="fas fa-user mr-2 text-muted"></i> My Profile
                        </a>
                        <a class="dropdown-item" href="{{ route('user.account_setting') }}">
                            <i class="fas fa-key mr-2 text-muted"></i> Account Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('user.logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>

            </ul>
        </nav>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Main content -->
            <section class="content" style="padding-top: 1.25rem; padding-bottom: 1.25rem;">
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
        <footer class="main-footer bg-white border-top u-pad-6px-12px">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted small">
                    © {{ date('Y') }} <strong>Job Hub</strong>. All rights reserved.
                </div>
                <div class="text-muted small">
                    <b>Version</b> 1.3
                </div>
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery (must load first — later plugins depend on it) -->
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('admins/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admins/dist/js/adminlte.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('admins/plugins/chart.js/Chart.min.js') }}"></script>

    @stack('scripts')
    <script src="{{ asset('js/global-loading.js') }}"></script>

    @if (session('success') || session('error') || session('warning'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: @json(session('success') ? 'success' : (session('warning') ? 'warning' : 'error')),
                title: @json(session('success') ?? session('warning') ?? session('error')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        });
    </script>
    @endif
</body>

</html>