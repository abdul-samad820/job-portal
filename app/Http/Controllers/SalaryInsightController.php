<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobRole;
use Illuminate\Http\Request;

class SalaryInsightController extends Controller
{
    /**
     * Aggregated salary stats computed straight from live job postings —
     * average/min/max pay and job count for a chosen role (optionally
     * narrowed further by location), so a job seeker can gauge what a
     * "fair" range looks like before they negotiate.
     */
    public function index(Request $request)
    {
        $roles = JobRole::orderBy('name')->get();

        $stats = null;
        $selectedRoleId = $request->input('role_id');
        $selectedLocation = $request->input('location');

        if ($selectedRoleId) {
            $user = \Illuminate\Support\Facades\Auth::guard('user')->user();

            // Daily usage cap — only counts an actual lookup (a role was
            // chosen), not the empty page load. Resets at midnight since
            // the cache key itself carries the date.
            $limit = \App\Services\BadgeLimitService::limitFor($user, 'salary_insight_daily');
            if ($limit !== null) {
                $cacheKey = 'salary_insight_checks:'.$user->id.':'.now()->toDateString();
                $usedToday = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);

                if ($usedToday >= $limit) {
                    return view('User.salary_insights', compact('roles', 'stats', 'selectedRoleId', 'selectedLocation'))
                        ->with('error', "You've used your {$limit} salary insight checks for today. Refer more friends to raise this limit, or try again tomorrow.");
                }

                \Illuminate\Support\Facades\Cache::put($cacheKey, $usedToday + 1, now()->endOfDay());
            }

            $query = Job::live()->where('role_id', $selectedRoleId)
                ->whereNotNull('min_salary')
                ->whereNotNull('max_salary');

            if ($selectedLocation) {
                $query->where('location', 'like', '%'.$selectedLocation.'%');
            }

            $jobCount = (clone $query)->count();

            if ($jobCount > 0) {
                $stats = [
                    'job_count' => $jobCount,
                    'avg_min' => round((clone $query)->avg('min_salary')),
                    'avg_max' => round((clone $query)->avg('max_salary')),
                    'overall_min' => (clone $query)->min('min_salary'),
                    'overall_max' => (clone $query)->max('max_salary'),
                ];
            }
        }

        return view('User.salary_insights', compact('roles', 'stats', 'selectedRoleId', 'selectedLocation'));
    }
}
