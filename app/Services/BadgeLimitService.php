<?php

namespace App\Services;

use App\Models\User;

/**
 * Single source of truth for what a referral badge tier unlocks.
 * Keeping thresholds/limits here (instead of scattered across
 * controllers) means changing a number later is a one-file edit.
 */
class BadgeLimitService
{
    // Referral count needed to reach each tier.
    public const TIERS = [
        'gold' => 50,
        'silver' => 25,
        'bronze' => 10,
    ];

    // Per-feature caps. `null` = unlimited.
    public const LIMITS = [
        'saved_jobs' => ['base' => 10, 'bronze' => 15, 'silver' => 25, 'gold' => null],
        'company_follow' => ['base' => 5, 'bronze' => 10, 'silver' => 20, 'gold' => null],
        'resume_score_daily' => ['base' => 3, 'bronze' => 5, 'silver' => 10, 'gold' => null],
        'salary_insight_daily' => ['base' => 5, 'bronze' => 10, 'silver' => 20, 'gold' => null],
        'bulk_apply_jobs' => ['base' => 5, 'bronze' => 10, 'silver' => 20, 'gold' => null],
    ];

    /**
     * The highest badge tier this user has earned, or null if they
     * haven't hit the first milestone (10 referrals) yet.
     */
    public static function tierFor(User $user): ?string
    {
        $count = User::where('referred_by', $user->id)->count();

        foreach (self::TIERS as $tier => $threshold) {
            if ($count >= $threshold) {
                return $tier;
            }
        }

        return null;
    }

    /**
     * The numeric limit for a given feature at this user's current tier.
     * Returns null when the tier grants unlimited use.
     */
    public static function limitFor(User $user, string $feature): ?int
    {
        $tiers = self::LIMITS[$feature] ?? null;
        if (! $tiers) {
            return null;
        }

        $tier = self::tierFor($user) ?? 'base';

        return $tiers[$tier];
    }
}
