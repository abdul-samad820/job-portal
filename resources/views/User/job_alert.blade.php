@extends('layouts.User_layout')
@section('content')

<div class="ja-page">

    {{-- ════════════ PAGE HEADER ════════════ --}}
    <div class="card border-0 shadow-sm mb-4 ja-header-card">
        <div class="card-body px-4 py-3">

            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="ja-icon-box mr-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <div class="ja-eyebrow">Notifications</div>

                        <h5 class="mb-1 font-weight-bold">
                            Job Alerts
                        </h5>

                        <small class="text-muted">
                            Get notified instantly when a matching job is posted.
                        </small>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="d-flex align-items-center mt-3 mt-md-0">

                    <!-- MINI INFO -->
                    <div class="ja-mini-hero text-right mr-3">
                        <div class="font-weight-bold small"><i class="fas fa-bullseye"></i> Smart Matching</div>
                        <div class="text-muted small">AI powered</div>
                    </div>

                    <!-- STATUS -->
                    @if($alert)
                    <div class="ja-status-pill {{ $alert->is_active ? 'active' : 'paused' }}">
                        <span class="ja-dot"></span>
                        {{ $alert->is_active ? 'Active' : 'Paused' }}
                    </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
    <div class="row">

        {{-- ════════════ LEFT — MAIN CARD ════════════ --}}
        <div class="col-lg-7 col-md-8 mb-4">

            <div class="ja-main-card">
                <div class="ja-card-body">

                    {{-- Success message --}}
                    @if(session('success'))
                    <div class="ja-alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($alert)

                    {{-- Status control row --}}
                    <div class="ja-status-row">
                        <div>
                            <div class="ja-status-row-label">Alert Status</div>
                            <div class="ja-status-row-value {{ $alert->is_active ? 'is-active' : '' }}">
                                @if($alert->is_active)
                                <i class="fas fa-circle u-fs-0-533rem-va-2px"></i>
                                Receiving alerts
                                @else
                                <i class="fas fa-pause-circle u-fs-var-fs-sm-color-94a3b8-va-1px"></i>
                                Alerts paused
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('job.alert.toggle') }}" class="mb-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="ja-toggle-btn {{ $alert->is_active ? 'pause' : 'activate' }}">
                                @if($alert->is_active)
                                <i class="fas fa-pause"></i> Pause
                                @else
                                <i class="fas fa-play"></i> Activate
                                @endif
                            </button>
                        </form>
                    </div>

                    {{-- Keywords --}}
                    <div class="mb-4">
                        <div class="ja-section-label">Your Keywords</div>

                        <div class="ja-keywords-cloud">
                            @forelse(explode(',', $alert->keywords) as $keyword)
                            @if(trim($keyword))
                            <span class="ja-keyword-tag">
                                <i class="fas fa-tag"></i>
                                {{ trim($keyword) }}
                            </span>
                            @endif
                            @empty
                            <span class="u-fs-8rem-color-94a3b8">
                                No keywords set yet
                            </span>
                            @endforelse
                        </div>

                        @if($alert->last_sent_at)
                        <div class="ja-last-sent">
                            <i class="fas fa-clock"></i>
                            Last alert sent {{ $alert->last_sent_at->diffForHumans() }}
                        </div>
                        @endif
                    </div>

                    @endif

                    {{-- ── Form ── --}}
                    <div class="ja-section-label">
                        {{ $alert ? 'Update Keywords' : 'Set Your Keywords' }}
                    </div>

                    <form method="POST" action="{{ route('job.alert.save') }}">
                        @csrf

                        <div class="ja-input-group">
                            <span class="ja-input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="keywords" aria-label="Keywords" class="ja-input"
                                placeholder="e.g. Laravel, PHP, React, Remote"
                                value="{{ old('keywords', $alert->keywords ?? '') }}" autocomplete="off">
                        </div>

                        @error('keywords')
                        <div class="u-fs-8rem-color-var-danger-d-flex-gap-5px">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                        @enderror

                        <div class="ja-input-hint">
                            <i class="fas fa-info-circle"></i>
                           Separate with commas — you can add as many keywords as you want.
                        </div>

                        <button type="submit" class="ja-save-btn mt-4">
                            <i class="fas fa-bell"></i>
                            {{ $alert ? 'Update Alert' : 'Create Alert' }}
                        </button>
                    </form>

                    {{-- Delete --}}
                    @if($alert)
                    <form method="POST" action="{{ route('job.alert.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ja-delete-btn"
                            onclick="return confirm('Delete this alert permanently?')">
                            <i class="fas fa-trash-alt u-fs-var-fs-xs"></i>
                            Delete alert permanently
                        </button>
                    </form>
                    @endif

                </div>{{-- /card-body --}}
            </div>{{-- /ja-main-card --}}

        </div>{{-- /col-left --}}

        {{-- ════════════ RIGHT SIDEBAR ════════════ --}}
        <div class="col-lg-5 col-md-4">

            {{-- ── Stats Card ── --}}
            @if($alert)
            <div class="ja-stats-card">
                <div class="ja-stats-header">Alert Overview</div>

                <div class="ja-stat-item">
                    <div class="ja-stat-item-left">
                        <div class="ja-stat-icon ja-stat-icon--blue">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <div class="ja-stat-name">Keywords</div>
                            <div class="ja-stat-sub">Active filters</div>
                        </div>
                    </div>
                    <div class="ja-stat-value">
                        {{ count(array_filter(array_map('trim', explode(',', $alert->keywords)))) }}
                    </div>
                </div>

                <div class="ja-stat-item">
                    <div class="ja-stat-item-left">
                        <div class="ja-stat-icon ja-stat-icon--green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="ja-stat-name">Status</div>
                            <div class="ja-stat-sub">Current state</div>
                        </div>
                    </div>
                    <div class="ja-stat-value"
                        style="font-size:.8rem; color:{{ $alert->is_active ? '#059669' : '#94a3b8' }}">
                        {{ $alert->is_active ? 'Active' : 'Paused' }}
                    </div>
                </div>

                <div class="ja-stat-item">
                    <div class="ja-stat-item-left">
                        <div class="ja-stat-icon ja-stat-icon--orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="ja-stat-name">Last Alert</div>
                            <div class="ja-stat-sub">Most recent email</div>
                        </div>
                    </div>
                    <div class="ja-stat-value u-fs-78rem-ta-right-maxw-90px-lh-1-3">
                        @if($alert->last_sent_at)
                        {{ $alert->last_sent_at->diffForHumans() }}
                        @else
                        <span class="u-color-94a3b8">Never</span>
                        @endif
                    </div>
                </div>

                <div class="ja-stat-item">
                    <div class="ja-stat-item-left">
                        <div class="ja-stat-icon ja-stat-icon--purple">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="ja-stat-name">Created</div>
                            <div class="ja-stat-sub">Alert set on</div>
                        </div>
                    </div>
                    <div class="ja-stat-value u-fs-78rem">
                        {{ $alert->created_at->format('d M Y') }}
                    </div>
                </div>

            </div>
            @endif

            {{-- ── How It Works Card ── --}}
            <div class="ja-how-card mb-4">
                <div class="ja-how-header">How It Works</div>

                <div class="ja-step">
                    <div class="ja-step-num">1</div>
                    <div>
                        <div class="ja-step-title">Set Your Keywords</div>
                        <p class="ja-step-desc">
                            Enter job roles, skills, or technologies you're targeting.
                        </p>
                    </div>
                </div>

                <div class="ja-step">
                    <div class="ja-step-num">2</div>
                    <div>
                        <div class="ja-step-title">Smart Matching</div>
                        <p class="ja-step-desc">
                            Our system scans new jobs daily and matches them to your keywords.
                        </p>
                    </div>
                </div>

                <div class="ja-step">
                    <div class="ja-step-num">3</div>
                    <div>
                        <div class="ja-step-title">Get Notified</div>
                        <p class="ja-step-desc">
                            Receive a daily email with all matching jobs — every morning at 9 AM.
                        </p>
                    </div>
                </div>
            </div>
        </div>{{-- /col-right --}}

    </div>{{-- /row --}}
</div>{{-- /ja-page --}}

@endsection