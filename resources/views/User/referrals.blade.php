@extends('layouts.User_layout')
@section('title', 'Refer a Friend')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-user-plus"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Refer a Friend</h1>
                <small class="text-muted">Share your link — every friend who signs up counts towards a badge, and every badge unlocks real perks.</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded h-100">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-3">Your Referral Link</h6>

                    <div class="input-group mb-3">
                        <input type="text" id="referralLinkInput" class="form-control" value="{{ $referralLink }}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="copyReferralLink()">
                                <i class="fas fa-copy mr-1"></i> Copy
                            </button>
                        </div>
                    </div>

                    <h6 class="font-weight-bold mb-2 mt-4">Your Referral Code</h6>
                    <span class="badge badge-primary px-4 py-2 u-fs-1rem">{{ $user->referral_code }}</span>

                    <hr>

                    <h5 class="font-weight-bold text-primary mb-0">{{ $count }}</h5>
                    <small class="text-muted">Friend(s) referred so far</small>

                    <hr>

                    @if ($earnedBadge)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas {{ $earnedBadge['icon'] }} fa-2x mr-2" style="color: {{ $earnedBadge['color'] }};"></i>
                        <div>
                            <div class="font-weight-bold">{{ $earnedBadge['label'] }}</div>
                            <small class="text-muted">Earned at {{ $earnedBadge['threshold'] }}+ referrals</small>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-2"><i class="fas fa-medal mr-1"></i> No badge yet</p>
                    @endif

                    @if ($nextMilestone)
                    @php
                        $prevThreshold = 0;
                        foreach ([10, 25, 50] as $t) { if ($t < $nextMilestone['threshold']) $prevThreshold = $t; }
                        $span = $nextMilestone['threshold'] - $prevThreshold;
                        $progress = $span > 0 ? min(100, round((($count - $prevThreshold) / $span) * 100)) : 0;
                    @endphp
                    <small class="text-muted d-block mb-1">
                        {{ $nextMilestone['threshold'] - $count }} more to unlock "{{ $nextMilestone['label'] }}"
                    </small>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%;"></div>
                    </div>
                    @else
                    <small class="text-success d-block"><i class="fas fa-check-circle mr-1"></i> You've unlocked every tier!</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded h-100">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-1">What Each Badge Gets You</h6>
                    <p class="text-muted small mb-4">Higher tiers raise your limits across the whole site — saved jobs, company follows, resume tools, and bulk applying.</p>

                    @php
                        $tierCards = [
                            [
                                'key' => 'bronze', 'threshold' => 10, 'label' => 'Bronze Referrer',
                                'icon' => 'fa-medal', 'color' => '#cd7f32',
                                'perks' => [
                                    'Save up to 15 jobs (up from 10)',
                                    'Follow up to 10 companies (up from 5)',
                                    '5 Resume Score checks/day (up from 3)',
                                    '10 Salary Insight checks/day (up from 5)',
                                    'Bulk apply to 10 jobs at once (up from 5)',
                                    'Unlocks the "Modern" Resume Builder template',
                                ],
                            ],
                            [
                                'key' => 'silver', 'threshold' => 25, 'label' => 'Silver Referrer',
                                'icon' => 'fa-medal', 'color' => '#adadad',
                                'perks' => [
                                    'Save up to 25 jobs',
                                    'Follow up to 20 companies',
                                    '10 Resume Score checks/day',
                                    '20 Salary Insight checks/day',
                                    'Bulk apply to 20 jobs at once',
                                ],
                            ],
                            [
                                'key' => 'gold', 'threshold' => 50, 'label' => 'Gold Referrer',
                                'icon' => 'fa-trophy', 'color' => '#e6b800',
                                'perks' => [
                                    'Unlimited saved jobs',
                                    'Unlimited company follows',
                                    'Unlimited Resume Score checks',
                                    'Unlimited Salary Insight checks',
                                    'Unlimited bulk apply',
                                    'A "Top Referrer" badge shown on your profile & navbar',
                                ],
                            ],
                        ];

                        $tierOrder = ['bronze' => 1, 'silver' => 2, 'gold' => 3];
                        $earnedRank = $earnedBadge ? $tierOrder[strtolower(explode(' ', $earnedBadge['label'])[0])] ?? 0 : 0;
                    @endphp

                    @foreach ($tierCards as $tier)
                    @php
                        $isUnlocked = $count >= $tier['threshold'];
                    @endphp
                    <div class="d-flex align-items-start p-3 mb-3 rounded" style="background: {{ $isUnlocked ? 'rgba(0,0,0,0.02)' : '#f8f9fa' }}; border: 1px solid {{ $isUnlocked ? $tier['color'] : '#e9ecef' }};">
                        <div class="mr-3 d-flex align-items-center justify-content-center" style="width:44px; height:44px; border-radius:50%; background: {{ $tier['color'] }}; flex-shrink:0; {{ $isUnlocked ? '' : 'opacity:0.4;' }}">
                            <i class="fas {{ $tier['icon'] }}" style="color:#fff; font-size:18px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="font-weight-bold">{{ $tier['label'] }} <span class="text-muted font-weight-normal small">({{ $tier['threshold'] }}+ referrals)</span></span>
                                @if ($isUnlocked)
                                <span class="badge bg-success"><i class="fas fa-check mr-1"></i>Unlocked</span>
                                @else
                                <span class="badge bg-secondary"><i class="fas fa-lock mr-1"></i>Locked</span>
                                @endif
                            </div>
                            <ul class="mb-0 pl-3 small text-muted">
                                @foreach ($tier['perks'] as $perk)
                                <li>{{ $perk }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function copyReferralLink() {
        const input = document.getElementById('referralLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function () {
            alert('Referral link copied!');
        });
    }
</script>
@endpush