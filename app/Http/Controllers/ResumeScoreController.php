<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class ResumeScoreController extends Controller
{
    // A small set of "strong" resume action verbs — their presence in the
    // experience/summary text is a good proxy for achievement-oriented
    // writing, which is what ATS systems and recruiters both reward.
    private const ACTION_VERBS = [
        'led', 'built', 'created', 'designed', 'developed', 'improved', 'increased',
        'reduced', 'launched', 'managed', 'implemented', 'optimized', 'automated',
        'delivered', 'achieved', 'drove', 'architected', 'mentored', 'scaled',
    ];

    /**
     * Score the user's profile the way a basic ATS/recruiter skim would:
     * completeness, presence of quantifiable/action language, and —
     * if a job is specified — keyword overlap against that job's
     * required skills.
     */
    public function index($jobId = null)
    {
        $user = Auth::guard('user')->user();
        $profile = $user->profile;

        // Daily usage cap — resets automatically at midnight since the
        // cache key itself is dated, no cron/reset job needed.
        $limit = \App\Services\BadgeLimitService::limitFor($user, 'resume_score_daily');
        if ($limit !== null) {
            $cacheKey = 'resume_score_checks:'.$user->id.':'.now()->toDateString();
            $usedToday = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);

            if ($usedToday >= $limit) {
                return back()->with('error',
                    "You've used your {$limit} resume score checks for today. Refer more friends to raise this limit, or try again tomorrow.");
            }

            \Illuminate\Support\Facades\Cache::put($cacheKey, $usedToday + 1, now()->endOfDay());
        }

        $breakdown = [];
        $score = 0;

        // 1) Professional summary (15 pts)
        $summaryLen = strlen((string) ($profile->professional_summary ?? ''));
        $summaryPoints = $summaryLen >= 80 ? 15 : ($summaryLen > 0 ? 7 : 0);
        $score += $summaryPoints;
        $breakdown[] = [
            'label' => 'Professional Summary',
            'points' => $summaryPoints,
            'max' => 15,
            'tip' => $summaryLen >= 80 ? 'Good length.' : 'Write at least 80 characters describing your profile.',
        ];

        // 2) Skills (20 pts)
        $skills = array_filter(array_map('trim', explode(',', (string) ($profile->core_skills ?? ''))));
        $skillsPoints = min(20, count($skills) * 3);
        $score += $skillsPoints;
        $breakdown[] = [
            'label' => 'Core Skills',
            'points' => $skillsPoints,
            'max' => 20,
            'tip' => count($skills) < 5 ? 'List at least 5-6 relevant skills, comma separated.' : 'Good skill coverage.',
        ];

        // 3) Education (15 pts)
        $education = is_array($profile->education ?? null) ? $profile->education : [];
        $eduPoints = count($education) > 0 ? 15 : 0;
        $score += $eduPoints;
        $breakdown[] = [
            'label' => 'Education',
            'points' => $eduPoints,
            'max' => 15,
            'tip' => $eduPoints ? 'Education listed.' : 'Add at least one education entry.',
        ];

        // 4) Experience with quantifiable / action language (25 pts)
        $experience = is_array($profile->experience ?? null) ? $profile->experience : [];
        $expText = strtolower(json_encode($experience));
        $hasNumbers = (bool) preg_match('/\d/', $expText);
        $actionVerbHits = 0;
        foreach (self::ACTION_VERBS as $verb) {
            if (str_contains($expText, $verb)) {
                $actionVerbHits++;
            }
        }
        $expPoints = 0;
        $expPoints += count($experience) > 0 ? 10 : 0;
        $expPoints += $hasNumbers ? 8 : 0;
        $expPoints += min(7, $actionVerbHits * 2);
        $score += $expPoints;
        $breakdown[] = [
            'label' => 'Experience Quality',
            'points' => $expPoints,
            'max' => 25,
            'tip' => ! $hasNumbers
                ? 'Add measurable results (e.g. "increased performance by 30%").'
                : ($actionVerbHits < 2 ? 'Use more action verbs like "led", "built", "improved".' : 'Strong, quantified experience.'),
        ];

        // 5) Projects (15 pts)
        $projects = is_array($profile->projects ?? null) ? $profile->projects : [];
        $projPoints = min(15, count($projects) * 8);
        $score += $projPoints;
        $breakdown[] = [
            'label' => 'Projects',
            'points' => $projPoints,
            'max' => 15,
            'tip' => count($projects) === 0 ? 'Add at least one project to strengthen your profile.' : 'Good project coverage.',
        ];

        // 6) Resume file on record (10 pts)
        $hasResume = $user->resumes()->exists();
        $resumePoints = $hasResume ? 10 : 0;
        $score += $resumePoints;
        $breakdown[] = [
            'label' => 'Resume File Uploaded',
            'points' => $resumePoints,
            'max' => 10,
            'tip' => $hasResume ? 'Resume on file.' : 'Upload a PDF resume to your library.',
        ];

        $score = min(100, $score);

        // Optional: job-specific keyword match
        $job = null;
        $matchPercent = null;
        $matchedSkills = [];
        $missingSkills = [];

        if ($jobId) {
            $job = Job::visible()->find($jobId);
            if ($job && $job->required_skills) {
                $jobSkills = array_filter(array_map('trim', explode(',', strtolower($job->required_skills))));
                $userSkillsLower = array_map('strtolower', $skills);

                foreach ($jobSkills as $js) {
                    $found = false;
                    foreach ($userSkillsLower as $us) {
                        if ($us === $js || str_contains($us, $js) || str_contains($js, $us)) {
                            $found = true;

                            break;
                        }
                    }
                    if ($found) {
                        $matchedSkills[] = $js;
                    } else {
                        $missingSkills[] = $js;
                    }
                }

                $matchPercent = count($jobSkills) > 0
                    ? round((count($matchedSkills) / count($jobSkills)) * 100)
                    : null;
            }
        }

        return view('User.resume_score', compact(
            'score', 'breakdown', 'job', 'matchPercent', 'matchedSkills', 'missingSkills'
        ));
    }
}
