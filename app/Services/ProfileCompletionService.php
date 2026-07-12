<?php

namespace App\Services;

use App\Models\User;

class ProfileCompletionService
{
    /**
     * Weights sum to 100. Previously the web and API calculated this
     * independently with different weights and different fields counted
     * (web included phone/address but not projects; API included
     * projects but not phone/address) — the same user could see 90% on
     * web and 100% on the API for an identically-filled-in profile
     * (Phase7 CQ-06, BUG-10). This is now the only place this is computed.
     */
    private const WEIGHTS = [
        'profile_image' => 15,
        'professional_summary' => 20,
        'core_skills' => 20,
        'education' => 15,
        'experience' => 15,
        'projects' => 5,
        'contact_info' => 10, // phone + address, both required
    ];

    public static function calculate(User $user): int
    {
        $profile = $user->profile;

        if (! $profile) {
            return 0;
        }

        $completion = 0;

        if (! empty($profile->profile_image)) {
            $completion += self::WEIGHTS['profile_image'];
        }
        if (! empty($profile->professional_summary)) {
            $completion += self::WEIGHTS['professional_summary'];
        }
        if (! empty($profile->core_skills)) {
            $completion += self::WEIGHTS['core_skills'];
        }
        if (! empty($profile->education)) {
            $completion += self::WEIGHTS['education'];
        }
        if (! empty($profile->experience)) {
            $completion += self::WEIGHTS['experience'];
        }
        if (! empty($profile->projects)) {
            $completion += self::WEIGHTS['projects'];
        }
        if (! empty($user->phone) && ! empty($user->address)) {
            $completion += self::WEIGHTS['contact_info'];
        }

        return $completion;
    }
}
