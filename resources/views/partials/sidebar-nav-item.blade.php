{{--
    Shared sidebar navigation item.

    Used by layouts/Admin_layout.blade.php, layouts/superadmin.blade.php and
    layouts/User_layout.blade.php so a menu change (new item, new icon, new
    badge) only has to be made in one place instead of three.

    Pass via @include('partials.sidebar-nav-item', [...]):

        route       string|null  Named route for the link.
        href        string       Raw URL, used when no route is given (default '#').
        icon        string       Font Awesome icon class, e.g. 'fas fa-home'.
        label       string       Visible menu text.
        activeCheck string|null  routeIs() pattern, e.g. 'admin.job_category*'.
                                 Defaults to `route` when a route is given.
        badge       int|null     Optional numeric badge shown next to the label.
        linkClass   string       Extra class(es) on the <a>, e.g. 'jobi-link'.
        iconClass   string       Extra class(es) on the <i>, e.g. 'nav-icon'.
        liClass     string       Class(es) on the <li>. Defaults to 'nav-item mb-2'.
--}}
@php
    $__href = isset($route) ? route($route) : ($href ?? '#');
    $__pattern = $activeCheck ?? ($route ?? null);
    $__isActive = $__pattern ? request()->routeIs($__pattern) : false;
@endphp
<li class="{{ $liClass ?? 'nav-item mb-2' }}">
    <a href="{{ $__href }}" class="nav-link {{ $linkClass ?? '' }} {{ $__isActive ? 'active' : '' }}">
        <i class="{{ $iconClass ?? '' }} {{ $icon }}"></i>
        <p>{{ $label }}
            @if (! empty($badge))
            <span class="badge badge-warning badge-pill ml-1">{{ $badge }}</span>
            @endif
        </p>
    </a>
</li>
