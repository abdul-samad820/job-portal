<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ResumeBuilderController extends Controller
{
    /**
     * Renders the user's profile data as a clean, print-ready resume.
     * No PDF library dependency — the view is styled for print, and the
     * user hits their browser's "Print > Save as PDF" to download it.
     */
    public function show(\Illuminate\Http\Request $request)
    {
        $user = Auth::guard('user')->user();
        $profile = $user->profile;

        $skills = $profile && $profile->core_skills
            ? array_filter(array_map('trim', explode(',', $profile->core_skills)))
            : [];

        // Use the null-safe operator so a user with no profile row yet
        // doesn't trigger "attempt to read property on null".
        $education = is_array($profile?->education) ? $profile->education : [];
        $experience = is_array($profile?->experience) ? $profile->experience : [];
        $projects = is_array($profile?->projects) ? $profile->projects : [];

        // Let the user pick between a couple of print layouts. "Modern"
        // is a referral-badge perk — fall back to Classic silently if
        // they don't have Bronze tier or higher yet.
        $badgeTier = \App\Services\BadgeLimitService::tierFor($user);
        $modernUnlocked = $badgeTier !== null; // any tier (bronze/silver/gold) unlocks it

        $requestedTemplate = in_array($request->query('template'), ['classic', 'modern'], true)
            ? $request->query('template')
            : 'classic';

        $template = ($requestedTemplate === 'modern' && ! $modernUnlocked) ? 'classic' : $requestedTemplate;

        return view('User.resume_builder', compact(
            'user', 'profile', 'skills', 'education', 'experience', 'projects', 'template', 'modernUnlocked'
        ));
    }
}
