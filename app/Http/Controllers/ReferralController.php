<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Referral milestone badges — reached purely by count, no DB column
     * needed since it's always derived from how many people someone
     * has referred.
     */
    private const MILESTONES = [
        10 => ['label' => 'Bronze Referrer', 'icon' => 'fa-medal', 'color' => '#cd7f32'],
        25 => ['label' => 'Silver Referrer', 'icon' => 'fa-medal', 'color' => '#adadad'],
        50 => ['label' => 'Gold Referrer', 'icon' => 'fa-trophy', 'color' => '#e6b800'],
    ];

    /**
     * Show the user's referral code/link plus everyone they've referred
     * so far, along with a simple "have they applied to anything yet"
     * signal so it's not just a headcount.
     */
    public function index()
    {
        $user = Auth::guard('user')->user();

        $referredUsers = User::where('referred_by', $user->id)
            ->withCount('jobApplications')
            ->latest()
            ->get();

        $referralLink = route('user.register.view', ['ref' => $user->referral_code]);

        $count = $referredUsers->count();

        // Highest milestone reached so far.
        $earnedBadge = null;
        foreach (self::MILESTONES as $threshold => $badge) {
            if ($count >= $threshold) {
                $earnedBadge = $badge + ['threshold' => $threshold];
            }
        }

        // Next milestone to work towards (for a small progress indicator).
        $nextMilestone = null;
        foreach (self::MILESTONES as $threshold => $badge) {
            if ($count < $threshold) {
                $nextMilestone = $badge + ['threshold' => $threshold];
                break;
            }
        }

        return view('User.referrals', compact(
            'user', 'referredUsers', 'referralLink', 'earnedBadge', 'nextMilestone', 'count'
        ));
    }
}
