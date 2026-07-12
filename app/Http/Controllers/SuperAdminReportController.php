<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Support\Carbon;

class SuperAdminReportController extends Controller
{
    /**
     * Platform-wide analytics. Trend data is grouped in PHP rather than
     * with DB-specific date functions (DATE_FORMAT vs strftime) so this
     * works identically on SQLite (local/dev) and MySQL (production)
     * without maintaining two query paths.
     */
    public function index()
    {
        $days = 30;
        $since = Carbon::now()->subDays($days - 1)->startOfDay();

        $jobsTrend = $this->dailyCounts(Job::where('created_at', '>=', $since)->pluck('created_at'), $days);
        $usersTrend = $this->dailyCounts(User::where('created_at', '>=', $since)->pluck('created_at'), $days);
        $applicationsTrend = $this->dailyCounts(JobApplication::where('created_at', '>=', $since)->pluck('created_at'), $days);

        $topCategories = JobCategory::withCount('jobs')
            ->orderByDesc('jobs_count')
            ->take(5)
            ->get(['id', 'name']);

        $topCompanies = Admin::where('role', 'admin')
            ->withCount('jobs')
            ->orderByDesc('jobs_count')
            ->take(5)
            ->get(['id', 'company_name']);

        $applicationStatusBreakdown = JobApplication::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total_jobs' => Job::count(),
            'total_users' => User::count(),
            'total_companies' => Admin::where('role', 'admin')->count(),
            'total_applications' => JobApplication::count(),
            'jobs_last_30_days' => $jobsTrend->sum(),
            'users_last_30_days' => $usersTrend->sum(),
        ];

        return view('SuperAdmin.reports', compact(
            'jobsTrend', 'usersTrend', 'applicationsTrend',
            'topCategories', 'topCompanies', 'applicationStatusBreakdown', 'summary'
        ));
    }

    /**
     * Turns a flat collection of timestamps into a date-indexed count
     * series covering every day in the window (including zero-count days
     * so the chart doesn't skip gaps).
     */
    private function dailyCounts($timestamps, int $days)
    {
        $counts = collect();
        $byDate = $timestamps->groupBy(fn ($t) => Carbon::parse($t)->format('Y-m-d'));

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $counts->put($date, $byDate->get($date, collect())->count());
        }

        return $counts;
    }
}
