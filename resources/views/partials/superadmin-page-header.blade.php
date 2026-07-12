{{--
    Consistent page header for every SuperAdmin section.
    Usage: @include('partials.superadmin-page-header', [
        'icon' => 'fa-briefcase',
        'title' => 'All Jobs',
        'subtitle' => 'Every job across every company.',
        'badge' => ['text' => '3 pending', 'class' => 'badge-warning'], // optional
    ])
    Slot: wrap action buttons in @section('page-actions') ... @show if a page
    needs a primary button in the header (see dashboard.blade.php for a
    pattern using @include with an 'actions' view-string instead).
--}}
<div class="sa-page-header">
    <div class="d-flex align-items-center">
        <div class="sa-page-icon mr-3">
            <i class="fa {{ $icon }}"></i>
        </div>
        <div>
            <h1 class="sa-page-title font-weight-bold text-dark mb-0">
                {{ $title }}
                @isset($badge)
                    <span class="badge badge-pill {{ $badge['class'] ?? 'badge-warning' }} ml-2 align-middle">
                        {{ $badge['text'] }}
                    </span>
                @endisset
            </h1>
            @if(!empty($subtitle))
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
            {!! $actions !!}
        </div>
    @endisset
</div>
