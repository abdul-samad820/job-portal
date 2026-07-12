<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobInvite;
use App\Models\JobRole;
use App\Models\User;
use App\Notifications\LoginSecurityNotification;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    use VerifiesUploadedFileMime;

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $data['email'])->where('role', 'admin')->first();

        if ($admin && ! $admin->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact SuperAdmin.',
            ]);
        }

        $remember = $request->boolean('remember');

        if ($admin && Auth::guard('admin')->attempt(array_merge($data, ['role' => 'admin']), $remember)) {

            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            // Tags this session so "My Devices" can list/revoke it later.
            $request->session()->put('admin_session_owner', $admin->id);

            $exists = $admin->notifications()
                ->where('type', LoginSecurityNotification::class)
                ->whereJsonContains('data->ip_address', $request->ip())
                ->whereDate('created_at', today())
                ->exists();

            if (! $exists) {
                $admin->notify(new LoginSecurityNotification(
                    $request->ip(),
                    $request->userAgent()
                ));
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.view');
    }

    /**
     * Shown to a company admin on their very first login (or any time
     * SuperAdmin resets their password) — they must set their own
     * password before they can use the rest of the panel.
     */
    public function forcePasswordChangeForm()
    {
        return view('Admin.force_password_change');
    }

    public function forcePasswordChangeUpdate(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $admin = Auth::guard('admin')->user();
        $admin->forceFill([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ])->save();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password updated. Welcome aboard!');
    }

    /**
     * A company admin's own audit trail — every job/category/application
     * action they've taken on their own account. Scoped strictly to
     * their admin_id; they never see other companies' activity.
     */
    public function activityLog()
    {
        $logs = \App\Models\ActivityLog::where('admin_id', Auth::guard('admin')->id())
            ->latest()
            ->paginate(25);

        return view('Admin.activity_log', compact('logs'));
    }

    /**
     * A company admin's own logged-in devices — pulled from the
     * `sessions` table (SESSION_DRIVER=database), filtered by the
     * `admin_session_owner` marker stored at login time. Multi-guard
     * apps can't rely on the session table's built-in user_id column
     * since it tracks whichever guard Laravel considers "default".
     */
    public function mySessions(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $currentSessionId = $request->session()->getId();

        $sessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->orderByDesc('last_activity')
            ->get()
            ->filter(function ($row) use ($adminId) {
                return $this->sessionBelongsTo($row->payload, 'admin_session_owner', $adminId);
            })
            ->map(function ($row) use ($currentSessionId) {
                $row->is_current = $row->id === $currentSessionId;
                $row->last_activity_human = \Carbon\Carbon::createFromTimestamp($row->last_activity)->diffForHumans();

                return $row;
            });

        return view('Admin.sessions', compact('sessions'));
    }

    public function revokeSession(Request $request, $sessionId)
    {
        $adminId = Auth::guard('admin')->id();

        $row = \Illuminate\Support\Facades\DB::table('sessions')->where('id', $sessionId)->first();

        // Only allow deleting a session that actually belongs to this
        // admin — prevents guessing another session's ID to kill it.
        if ($row && $this->sessionBelongsTo($row->payload, 'admin_session_owner', $adminId)) {
            \Illuminate\Support\Facades\DB::table('sessions')->where('id', $sessionId)->delete();
        }

        if ($sessionId === $request->session()->getId()) {
            return redirect()->route('admin.login.view');
        }

        return back()->with('success', 'Device logged out.');
    }

    public function revokeOtherSessions(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $currentSessionId = $request->session()->getId();

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('id', '!=', $currentSessionId)
            ->get()
            ->each(function ($row) use ($adminId) {
                if ($this->sessionBelongsTo($row->payload, 'admin_session_owner', $adminId)) {
                    \Illuminate\Support\Facades\DB::table('sessions')->where('id', $row->id)->delete();
                }
            });

        return back()->with('success', 'All other devices have been logged out.');
    }

    /**
     * Decode a `sessions.payload` value and check whether it carries the
     * given owner marker/value. Handles both encrypted and plain
     * session payloads depending on SESSION_ENCRYPT, and fails closed
     * (returns false) on anything unreadable rather than throwing.
     */
    private function sessionBelongsTo(string $payload, string $key, $expectedId): bool
    {
        try {
            // DatabaseSessionHandler always base64-encodes the payload column,
            // regardless of SESSION_ENCRYPT — that outer layer must come off
            // first before we can decrypt (or plain-decode) what's underneath.
            $raw = base64_decode($payload, true);

            if ($raw === false) {
                return false;
            }

            if (config('session.encrypt')) {
                $decoded = \Illuminate\Support\Facades\Crypt::decryptString($raw);
            } else {
                $decoded = $raw;
            }

            $data = @unserialize($decoded);

            return is_array($data) && ($data[$key] ?? null) == $expectedId;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function search(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $q = trim($request->get('q', ''));

        $jobs = collect();
        $applicants = collect();

        if ($q !== '') {
            $jobs = Job::where('admin_id', $adminId)
                ->where('title', 'LIKE', '%'.$q.'%')
                ->latest()
                ->take(10)
                ->get();

            $applicants = JobApplication::with(['user', 'job'])
                ->whereHas('job', fn ($query) => $query->where('admin_id', $adminId))
                ->whereHas('user', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%'.$q.'%')
                        ->orWhere('email', 'LIKE', '%'.$q.'%');
                })
                ->latest()
                ->take(10)
                ->get();
        }

        return view('Admin.admin_search', compact('jobs', 'applicants', 'q'));
    }

    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        $adminId = $admin->id;
        $now = now();

        // 1 query — fetch all admin jobs once, compute breakdowns in memory
        $adminJobs = Job::where('admin_id', $adminId)->get(['id', 'last_date', 'title', 'views_count', 'role_id', 'min_salary', 'max_salary', 'created_at']);
        $jobIds = $adminJobs->pluck('id');
        $totalJobs = $adminJobs->count();
        $activeJobs = $adminJobs->where('last_date', '>=', $now)->count();
        $expiredJobs = $adminJobs->where('last_date', '<', $now)->count();
        $expiringJobs = $adminJobs->whereBetween('last_date', [$now, $now->copy()->addDays(7)])->count();

        // 1 query — application status counts, grouped
        $statusCounts = JobApplication::whereIn('job_id', $jobIds)
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $totalApplications = $statusCounts->sum();
        $pendingCount = $statusCounts['pending'] ?? 0;
        $shortlistedCount = $statusCounts['shortlisted'] ?? 0;
        $hiredCount = $statusCounts['hired'] ?? 0;
        $rejectedCount = $statusCounts['rejected'] ?? 0;

        // 1 query — top job
        $topJob = Job::withCount('applications')->where('admin_id', $adminId)->orderBy('applications_count', 'desc')->first();

        // Recent posted jobs
        $recentJobs = Job::where('admin_id', $adminId)->latest()->take(5)->get();

        // ───────────────────────────────────────────────────────────────
        // 1) HIRING ANALYTICS
        // ───────────────────────────────────────────────────────────────

        // Applications trend — last 30 days, one point per day
        $trendStart = $now->copy()->subDays(29)->startOfDay();
        $dailyCounts = JobApplication::whereIn('job_id', $jobIds)
            ->where('created_at', '>=', $trendStart)
            ->selectRaw('DATE(created_at) as d, count(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $trendLabels = [];
        $trendData = [];
        for ($i = 0; $i < 30; $i++) {
            $day = $trendStart->copy()->addDays($i);
            $key = $day->format('Y-m-d');
            $trendLabels[] = $day->format('d M');
            $trendData[] = (int) ($dailyCounts[$key] ?? 0);
        }

        // Hiring funnel: Applied -> Shortlisted -> Interviewed -> Hired
        $interviewedCount = Interview::whereHas('application', function ($q) use ($jobIds) {
            $q->whereIn('job_id', $jobIds);
        })->distinct('job_application_id')->count('job_application_id');

        $funnel = [
            'applied' => $totalApplications,
            'shortlisted' => $shortlistedCount,
            'interviewed' => $interviewedCount,
            'hired' => $hiredCount,
        ];

        // Job-wise applications comparison (top 6 by application count) + views ratio
        $jobPerformance = Job::withCount('applications')
            ->where('admin_id', $adminId)
            ->orderByDesc('applications_count')
            ->take(6)
            ->get()
            ->map(function ($job) {
                $job->conversion_rate = $job->views_count > 0
                    ? round(($job->applications_count / $job->views_count) * 100, 1)
                    : 0;

                return $job;
            });

        // ───────────────────────────────────────────────────────────────
        // 2) CANDIDATE PIPELINE
        // ───────────────────────────────────────────────────────────────

        $openToWorkCount = User::whereHas('profile', function ($q) {
            $q->where('open_to_work', true);
        })->count();

        $invitesSent = JobInvite::where('admin_id', $adminId)->count();
        $invitesResponded = JobInvite::where('admin_id', $adminId)->where('status', 'applied')->count();

        $upcomingInterviews = Interview::where('admin_id', $adminId)
            ->whereIn('status', ['scheduled', 'rescheduled'])
            ->where('interview_date', '>=', $now->toDateString())
            ->orderBy('interview_date')
            ->orderBy('interview_time')
            ->with(['application.user', 'application.job'])
            ->take(5)
            ->get();

        // ───────────────────────────────────────────────────────────────
        // 3) JOB HEALTH
        // ───────────────────────────────────────────────────────────────

        $expiringJobsList = Job::where('admin_id', $adminId)
            ->whereBetween('last_date', [$now, $now->copy()->addDays(7)])
            ->orderBy('last_date')
            ->get();

        $zeroApplicationJobs = Job::withCount('applications')
            ->where('admin_id', $adminId)
            ->where('last_date', '>=', $now)
            ->having('applications_count', '=', 0)
            ->get();

        $avgTimeToHireDays = \Illuminate\Support\Facades\DB::table('application_status_histories as h')
            ->join('job_applications as ja', 'ja.id', '=', 'h.job_application_id')
            ->whereIn('ja.job_id', $jobIds)
            ->where('h.to_status', 'hired')
            ->selectRaw('AVG(DATEDIFF(h.created_at, ja.created_at)) as avg_days')
            ->value('avg_days');
        $avgTimeToHireDays = $avgTimeToHireDays !== null ? round($avgTimeToHireDays, 1) : null;

        // ───────────────────────────────────────────────────────────────
        // 4) COMPANY PROFILE
        // ───────────────────────────────────────────────────────────────

        $profileFields = ['company_name', 'description', 'contact_number', 'location', 'expertise', 'profile_image'];
        $filledCount = collect($profileFields)->filter(fn ($f) => ! empty($admin->{$f}))->count();
        $companyProfileCompletion = (int) round(($filledCount / count($profileFields)) * 100);

        // ───────────────────────────────────────────────────────────────
        // 5) MARKET INSIGHT — your avg posted salary vs platform-wide avg, by role
        // ───────────────────────────────────────────────────────────────

        $salaryComparison = $adminJobs
            ->filter(fn ($j) => $j->role_id && $j->min_salary && $j->max_salary)
            ->groupBy('role_id')
            ->map(function ($jobsForRole, $roleId) {
                $roleName = optional(JobRole::find($roleId))->name ?? 'Unknown';
                $yourAvg = round(($jobsForRole->avg('min_salary') + $jobsForRole->avg('max_salary')) / 2);

                $marketAvg = Job::visible()->where('role_id', $roleId)
                    ->whereNotNull('min_salary')->whereNotNull('max_salary')
                    ->selectRaw('AVG((min_salary + max_salary) / 2) as avg_salary')
                    ->value('avg_salary');

                return [
                    'role' => $roleName,
                    'your_avg' => $yourAvg,
                    'market_avg' => $marketAvg ? round($marketAvg) : null,
                ];
            })
            ->values()
            ->take(4);

        // ───────────────────────────────────────────────────────────────
        // 6) ACTIVITY & SECURITY
        // ───────────────────────────────────────────────────────────────

        $recentActivity = ActivityLog::where('admin_id', $adminId)->latest()->take(5)->get();

        $currentSessionId = request()->session()->getId();
        $activeSessionsCount = \Illuminate\Support\Facades\DB::table('sessions')
            ->orderByDesc('last_activity')
            ->get()
            ->filter(fn ($row) => $this->sessionBelongsTo($row->payload, 'admin_session_owner', $adminId))
            ->count();

        // ───────────────────────────────────────────────────────────────
        // 7) GROWTH METRICS — this month vs last month
        // ───────────────────────────────────────────────────────────────

        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $applicationsThisMonth = JobApplication::whereIn('job_id', $jobIds)->where('created_at', '>=', $thisMonthStart)->count();
        $applicationsLastMonth = JobApplication::whereIn('job_id', $jobIds)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $applicationsGrowth = $applicationsLastMonth > 0
            ? round((($applicationsThisMonth - $applicationsLastMonth) / $applicationsLastMonth) * 100)
            : ($applicationsThisMonth > 0 ? 100 : 0);

        $jobsThisMonth = $adminJobs->where('created_at', '>=', $thisMonthStart)->count();
        $jobsLastMonth = $adminJobs->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $jobsGrowth = $jobsLastMonth > 0
            ? round((($jobsThisMonth - $jobsLastMonth) / $jobsLastMonth) * 100)
            : ($jobsThisMonth > 0 ? 100 : 0);

        return view('Admin.admin_dashboard', compact(
            'totalJobs', 'activeJobs', 'expiredJobs', 'totalApplications', 'pendingCount', 'shortlistedCount', 'hiredCount', 'rejectedCount', 'topJob', 'expiringJobs', 'recentJobs',
            'trendLabels', 'trendData', 'funnel', 'jobPerformance',
            'openToWorkCount', 'invitesSent', 'invitesResponded', 'upcomingInterviews',
            'expiringJobsList', 'zeroApplicationJobs', 'avgTimeToHireDays',
            'companyProfileCompletion', 'admin',
            'salaryComparison',
            'recentActivity', 'activeSessionsCount',
            'applicationsThisMonth', 'applicationsLastMonth', 'applicationsGrowth',
            'jobsThisMonth', 'jobsLastMonth', 'jobsGrowth'
        ));
    }

    public function selectedList()
    {
        $adminId = Auth::guard('admin')->id();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');
        $selectedApplicants = JobApplication::with(['user.profile', 'job', 'interview'])
            ->whereIn('job_id', $jobIds)
            ->whereIn('status', ['shortlisted', 'hired'])
            ->latest()
            ->get();

        return view('Admin.selectedList', compact('selectedApplicants'));
    }

    public function exportSelectedList()
    {
        $adminId = Auth::guard('admin')->id();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');
        $applicants = JobApplication::with(['user', 'job'])
            ->whereIn('job_id', $jobIds)
            ->whereIn('status', ['shortlisted', 'hired'])
            ->latest()
            ->get();

        $filename = 'selected-candidates-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($applicants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Candidate Name', 'Email', 'Job Position', 'Status', 'Applied On']);

            foreach ($applicants as $app) {
                fputcsv($handle, [
                    $app->user->name ?? 'N/A',
                    $app->user->email ?? 'N/A',
                    $app->job->title ?? 'N/A',
                    ucfirst($app->status),
                    $app->created_at->format('d M Y'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Shared by dashboard() and admin_profile() so a future optimization
     * only needs to happen in one place (Phase6 PERF-12).
     */
    private function getAdminJobStats(int $adminId): array
    {
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');

        return [
            'totalJobs' => $jobIds->count(),
            'totalApplications' => JobApplication::whereIn('job_id', $jobIds)->count(),
        ];
    }

    public function admin_profile(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $profile = Auth::guard('admin')->user();
        $stats = $this->getAdminJobStats($adminId);
        $totalJobs = $stats['totalJobs'];
        $totalApplications = $stats['totalApplications'];

        return view('Admin.profile', compact('profile', 'totalJobs', 'totalApplications'));
    }

    public function update_profile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,'.$admin->id,
            'location' => 'nullable|string|max:255',
            'expertise' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                Storage::disk('public')->delete('admins/'.$admin->profile_image);
            }
            $path = $request->file('profile_image')->store('admins', 'public');
            if (! $this->verifyStoredMime('public', $path, ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['profile_image' => 'Invalid file type.']);
            }
            $data['profile_image'] = basename($path);
        }

        $admin->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * GDPR — "Right to data portability" for company/employer accounts.
     * Bundles the admin's company profile, job postings, categories,
     * roles, and received applications summary into one JSON file.
     * Candidate personal data inside applications is limited to what
     * the employer already legitimately sees (name/email/status), not
     * the candidate's full profile.
     */
    public function dataExport(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $admin->load([
            'jobs.category',
            'jobs.applications.user',
            'jobCategories',
            'jobRoles',
            'jobInvites.job',
            'jobInvites.user',
            'followers.user',
            'faqs',
            'testimonials',
            'activityLogs',
        ]);

        $export = [
            'exported_at' => now()->toIso8601String(),
            'company_account' => [
                'company_name' => $admin->company_name,
                'email' => $admin->email,
                'contact_number' => $admin->contact_number,
                'location' => $admin->location,
                'description' => $admin->description,
                'expertise' => $admin->expertise,
                'account_created_at' => $admin->created_at?->toIso8601String(),
            ],
            'job_categories' => $admin->jobCategories->map(fn ($c) => [
                'name' => $c->name,
                'description' => $c->description,
            ]),
            'job_roles' => $admin->jobRoles->map(fn ($r) => [
                'name' => $r->name ?? null,
            ]),
            'jobs_posted' => $admin->jobs->map(fn ($job) => [
                'title' => $job->title,
                'category' => $job->category->name ?? null,
                'location' => $job->location,
                'type' => $job->type,
                'last_date' => $job->last_date?->toDateString(),
                'posted_at' => $job->created_at?->toIso8601String(),
                'applications_received' => $job->applications->map(fn ($app) => [
                    'candidate_name' => $app->user->name ?? null,
                    'candidate_email' => $app->user->email ?? null,
                    'status' => $app->status,
                    'applied_at' => $app->created_at?->toIso8601String(),
                ]),
            ]),
            'job_invites_sent' => $admin->jobInvites->map(fn ($invite) => [
                'job_title' => $invite->job->title ?? null,
                'candidate_email' => $invite->user->email ?? null,
                'status' => $invite->status,
                'sent_at' => $invite->created_at?->toIso8601String(),
            ]),
            'followers_count' => $admin->followers->count(),
            'faqs' => $admin->faqs->map(fn ($faq) => [
                'question' => $faq->question,
                'answer' => $faq->answer,
                'status' => $faq->status,
            ]),
            'testimonials' => $admin->testimonials->map(fn ($t) => [
                'name' => $t->name,
                'review' => $t->review,
                'rating' => $t->rating,
                'status' => $t->status,
            ]),
            'activity_log' => $admin->activityLogs->map(fn ($log) => [
                'action' => $log->action,
                'description' => $log->description,
                'at' => $log->created_at?->toIso8601String(),
            ]),
        ];

        \App\Models\ActivityLog::logAdmin('data_exported', 'Exported company account data.');

        $filename = 'job-hub-company-data-export-'.now()->format('Y-m-d').'.json';

        return response()->json($export, 200, [
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * GDPR — "Right to erasure" for company/employer accounts.
     * Deletes the company profile along with every job it posted
     * (and those jobs' applications, invites, follows — all cascade
     * at the DB level). FAQs, testimonials, and the activity log are
     * anonymised (admin_id set null) rather than deleted, since they
     * remain part of the historical/public record. Physical files
     * (job images, category images, resume snapshots) are cleaned up
     * in Admin::booted() before the row is removed.
     */
    public function deleteAccount(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'delete_password' => ['required', function ($attr, $value, $fail) use ($admin) {
                if (! Hash::check($value, $admin->password)) {
                    $fail('Incorrect password. Account was not deleted.');
                }
            }],
        ]);

        $adminId = $admin->id;

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Admin::findOrFail($adminId)->delete();

        \Illuminate\Support\Facades\Log::info("GDPR account deletion completed for admin id {$adminId}.");

        return redirect()->route('user.home')
            ->with('success', 'Your company account and its data have been permanently deleted.');
    }

    public function readNotifications()
    {
        auth('admin')->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
