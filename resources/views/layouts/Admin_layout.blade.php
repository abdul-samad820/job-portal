<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Job Portal')</title>
    <meta name="description" content="@yield('meta_description', 'Manage job postings, applications, and candidates from the Job Hub admin dashboard.')">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('admins/dist/img/Job_Hub_Logo_Design.png') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admins/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Custom overrides load LAST so they win the cascade over AdminLTE defaults -->
    <link rel="stylesheet" href="{{ asset('css/style-admin-file.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-pro.css') }}">
    @stack('styles')

    <!-- Custom Typography Override -->
</head>
<body class="hold-transition sidebar-mini layout-fixed admin-shell">
    <div class="wrapper flex-grow-1">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top border-0">
            <ul class="navbar-nav align-items-center flex-grow-1">
                <!-- Sidebar toggle -->
                <li class="nav-item">
                    <a class="sa-header-toggle" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>

                <!-- Page title / breadcrumb -->
                <li class="nav-item d-none d-md-block">
                    <div class="sa-breadcrumb">
                        <span class="sa-crumb-trail">Admin Panel</span>
                        <span class="sa-crumb-title">@yield('title', 'Dashboard')</span>
                    </div>
                </li>

                <!-- Search -->
                <li class="nav-item ml-auto ml-md-4 d-none d-sm-block">
                    <form class="sa-header-search" method="GET" action="{{ route('admin.search') }}">
                        <i class="fas fa-search"></i>
                        <input type="search" name="q" aria-label="Search jobs, applicants" placeholder="Search jobs, applicants..." value="{{ request('q') }}">
                    </form>
                </li>
            </ul>

            <!-- Right: icons -->
            <ul class="navbar-nav ml-auto ml-sm-2 align-items-center">

                {{-- $adminUser, $notifications, $unreadCount are injected by
                     App\View\Composers\AdminLayoutComposer via AppServiceProvider --}}

                <li class="nav-item dropdown">
                    <a class="sa-icon-btn" data-toggle="dropdown" href="#" aria-label="Notifications">
                        <i class="far fa-bell"></i>
                        @if ($unreadCount > 0)
                            <span class="badge badge-danger navbar-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow border-0 p-0 u-w-380px-radius-12px-ovf-hidden">

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
                        <div class="dropdown-scroll-350">

                            @forelse($notifications->take(5) as $notification)
                            <a href="{{ route('notifications.open', $notification->id) }}" class="dropdown-item px-4 py-3 {{ is_null($notification->read_at) ? 'bg-light' : '' }}">

                                <div class="d-flex">

                                    {{-- Icon --}}
                                    <div class="mr-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center u-w-38px-h-38px">
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
                    <a class="sa-header-profile" data-toggle="dropdown" href="#">
                        <img src="{{ $adminUser->profile_image
                            ? Storage::url('admins/' . $adminUser->profile_image)
                            : asset('admins/dist/img/default.png') }}" class="sa-avatar-circle" style="object-fit: cover;" alt="Admin profile">
                        <span class="d-none d-lg-flex flex-column">
                            <span class="sa-header-name">{{ \Illuminate\Support\Str::limit($adminUser->company_name, 20) }}</span>
                            <span class="sa-header-role">{{ $adminUser->email }}</span>
                        </span>
                    </a>

                    {{-- DROPDOWN MENU --}}
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 p-0 u-w-260px-radius-14px-ovf-hidden">

                        {{-- HEADER --}}
                        <div class="text-center p-4 u-bg-linear-gradien">

                            <img src="{{ $adminUser->profile_image
                            ? Storage::url('admins/' . $adminUser->profile_image)
                            : asset('admins/dist/img/default.png') }}" class="rounded-circle shadow mb-2 u-fit-cover-border-3px-solid-white" width="70" height="70" alt="Admin profile">

                            <div class="text-white font-weight-bold">
                                {{ $adminUser->company_name }}
                            </div>

                            <small class="text-white-50">
                                {{ $adminUser->email }}
                            </small>
                        </div>

                        {{-- MENU ITEMS --}}
                        <div class="py-2">

                            <a class="dropdown-item d-flex align-items-center py-2 u-trans-0-2s" href="{{ route('admin.profile') }}">

                                <i class="fas fa-user-circle text-primary mr-2"></i>
                                <span>My Profile</span>
                            </a>

                            <a class="dropdown-item d-flex align-items-center py-2 u-trans-0-2s" href="{{ route('admin.dashboard') }}">

                                <i class="fas fa-chart-line text-info mr-2"></i>
                                <span>Dashboard</span>
                            </a>

                            <div class="dropdown-divider my-2"></div>

                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center py-2 text-danger u-trans-0-2s">

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
            <div class="jobi-sidebar-fixed">
                <div class="jobi-brand mb-0">
                    <img src="{{ asset('admins/dist/img/Job_Hub_Logo.png') }}" class="brand-img" alt="Job Hub logo">
                    <div class="sa-brand-text">
                        <span class="sa-brand-title">Job Hub</span>
                        <small class="brand-subtext">Control Panel</small>
                    </div>
                </div>

                <div class="jobi-profile mb-0">
                    <img src="{{ $adminUser->profile_image
                        ? Storage::url('admins/' . $adminUser->profile_image)
                        : asset('admins/dist/img/default.png') }}" class="profile-img" style="object-fit: cover;" alt="Admin profile">
                    <div class="sa-profile-meta">
                        <div class="sa-profile-name">{{ \Illuminate\Support\Str::limit($adminUser->company_name, 18) }}</div>
                        <span class="sa-profile-role-badge"><i class="fas fa-building"></i>Company Admin</span>
                    </div>
                </div>
            </div>

            <div class="sidebar">

                <!-- MENU -->
                <nav class="mt-1 w-100">
                    <ul class="nav flex-column jobi-menu">

                        <p class="sa-nav-heading">Overview</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.dashboard',
                            'icon' => 'fas fa-home',
                            'label' => 'Dashboard',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Hiring</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.job_category',
                            'activeCheck' => 'admin.job_category*',
                            'icon' => 'fas fa-th-large',
                            'label' => 'Job Category',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.job_role',
                            'activeCheck' => 'admin.job_role*',
                            'icon' => 'fas fa-users-cog',
                            'label' => 'Job Role',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.job',
                            'icon' => 'fas fa-briefcase',
                            'label' => 'Job',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'job_application',
                            'activeCheck' => 'job_application*',
                            'icon' => 'fas fa-clipboard-list',
                            'label' => 'Application',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.candidates.index',
                            'activeCheck' => 'admin.candidates*',
                            'icon' => 'fas fa-users',
                            'label' => 'Candidates',
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Content</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.selectedList',
                            'activeCheck' => 'admin.selectedList*',
                            'icon' => 'fas fa-user-tie',
                            'label' => 'Selected Candidate',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'faq',
                            'activeCheck' => 'faq*',
                            'icon' => 'fas fa-comments',
                            'label' => 'Question answer',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.testimonials',
                            'activeCheck' => '*testimonial*',
                            'icon' => 'fas fa-quote-left',
                            'label' => 'Testimonials',
                            'badge' => $pendingTestimonialsCount ?? 0,
                            'linkClass' => 'jobi-link',
                        ])

                        <p class="sa-nav-heading">Account</p>

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.activity_log',
                            'activeCheck' => 'admin.activity_log*',
                            'icon' => 'fas fa-history',
                            'label' => 'Activity Log',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.sessions',
                            'activeCheck' => 'admin.sessions*',
                            'icon' => 'fas fa-laptop',
                            'label' => 'My Devices',
                            'linkClass' => 'jobi-link',
                        ])

                        @include('partials.sidebar-nav-item', [
                            'route' => 'admin.profile',
                            'activeCheck' => 'admin.profile*',
                            'icon' => 'fas fa-user-circle',
                            'label' => 'Company Profile',
                            'linkClass' => 'jobi-link',
                        ])
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- /.sidebar -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content" style="padding-top: 1.25rem;">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} <a href="{{ route('admin.dashboard') }}">JOB HUB</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.3
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

    <script src="{{ asset('admins/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

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