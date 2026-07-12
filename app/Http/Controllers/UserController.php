<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\SavedJob;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ProfileCompletionService;
use App\Traits\VerifiesUploadedFileMime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use VerifiesUploadedFileMime;

    /**
     * Show the registration form. If the visitor arrived via a referral
     * link (?ref=CODE), remember it in a cookie for 30 days so the
     * referral still counts even if they browse a few pages before
     * actually registering — userregister() reads this cookie as a
     * fallback when no ?ref= is present on the submitted form.
     */
    public function registerView(Request $request)
    {
        if ($request->filled('ref')) {
            Cookie::queue('referral_code', strtoupper($request->query('ref')), 60 * 24 * 30);
        }

        return view('User.register');
    }

    public function userregister(RegisterRequest $request)
    {
        $credentials = $request->validated();
        unset($credentials['agree_terms']);
        $credentials['password'] = Hash::make($credentials['password']);

        // If the person signed up via a referral link (?ref=CODE) or
        // typed the code manually, link the two accounts.
        $refCode = $request->input('ref') ?? $request->cookie('referral_code');
        if ($refCode) {
            $referrer = User::where('referral_code', strtoupper($refCode))->first();
            if ($referrer) {
                $credentials['referred_by'] = $referrer->id;
            }
        }

        $user = User::create($credentials);

        // Automatic verification email send
        $user->sendEmailVerificationNotification();

        Auth::guard('user')->login($user);

        // Verification page
        return redirect()->route('verification.notice')
            ->with('info', 'Account created! Please verify your email to continue.');
    }

    public function userlogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Suspended accounts are rejected immediately, before attempting
        // auth — mirrors AdminController::login() instead of letting the
        // user log in for a split second and get bounced by User_mid.
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser && ! $existingUser->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ])->withInput($request->only('email'));
        }

        $remember = $request->boolean('remember');

        // Rate limiting is enforced at the route level via
        // ->middleware('throttle:5,1') on POST /user/login.
        if (Auth::guard('user')->attempt($data, $remember)) {
            // Login success — attempts reset
            $request->session()->regenerate();
            \App\Models\UserActivityLog::log(auth('user')->id(), 'login', $request);

            return redirect()->route('user.dashboard')
                ->with('login_success', 'Welcome back, '.auth('user')->user()->name.' 👋');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    public function userlogout(Request $request)
    {
        $userId = Auth::guard('user')->id();
        if ($userId) {
            \App\Models\UserActivityLog::log($userId, 'logout', $request);
        }

        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }

    public function user_dashboard()
    {
        $userId = auth('user')->id();
        $user = Auth::guard('user')->user();
        $totalJobs = Job::count();
        $appliedJobsCount = JobApplication::where('user_id', $userId)->count();

        // Closure-based subquery — 1 SQL round-trip instead of a separate pluck() query
        $newJobsCount = Job::visible()->where('created_at', '>=', now()->subDay())
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('job_id')->from('job_applications')->where('user_id', $userId);
            })
            ->count();

        $recentAppliedJobs = JobApplication::with(['job.admin'])->where('user_id', $userId)->latest()
            ->take(4)->get();

        // Fetched once, reused below — removes the duplicate query
        $profile = UserProfile::where('user_id', $userId)->first();
        $recommendedJobsCount = 0;

        if ($profile && ! empty($profile->core_skills)) {
            $userSkills = array_map('trim', explode(',', $profile->core_skills));

            $recommendedJobsQuery = Job::visible()->whereDate('last_date', '>=', now());
            $this->applySkillMatch($recommendedJobsQuery, $userSkills);

            $recommendedJobsCount = $recommendedJobsQuery
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('job_id')->from('job_applications')->where('user_id', $userId);
                })
                ->count();
        }

        //  USER PROFILE COMPLETION
        $profileCompletion = ProfileCompletionService::calculate($user);

        $savedJobsCount = SavedJob::where('user_id', $userId)->count();
        $savedJobs = SavedJob::with('job')->where('user_id', $userId)->latest()->take(3)->get();

        //  UPCOMING INTERVIEWS (next 3, soonest first)
        $upcomingInterviews = Interview::with(['application.job.admin'])
            ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
            ->where('interview_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('interview_date', 'asc')
            ->orderBy('interview_time', 'asc')
            ->take(3)
            ->get();

        // 1 grouped query instead of 4 separate counts
        $statusCounts = JobApplication::where('user_id', $userId)
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $pendingCount = $statusCounts['pending'] ?? 0;
        $shortlistedCount = $statusCounts['shortlisted'] ?? 0;
        $hiredCount = $statusCounts['hired'] ?? 0;
        $rejectedCount = $statusCounts['rejected'] ?? 0;

        return view('User.user_dashboard', compact(
            'totalJobs',
            'appliedJobsCount',
            'newJobsCount',
            'recentAppliedJobs',
            'recommendedJobsCount',
            'profileCompletion',
            'savedJobsCount',
            'savedJobs',
            'upcomingInterviews',
            'pendingCount',
            'shortlistedCount',
            'hiredCount',
            'rejectedCount'
        ));
    }

    public function saved_jobs()
    {
        $userId = Auth::guard('user')->id();
        $savedJobs = SavedJob::with(['job.admin', 'job.category', 'job.role'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('User.user_saved_jobs', compact('savedJobs'));
    }

    public function user_jobs(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $query = Job::with(['category', 'role', 'admin'])->visible();

        $userJobStatus = Cache::remember("user_{$userId}_job_status", 60, function () use ($userId) {
            return [
                'saved' => SavedJob::where('user_id', $userId)->pluck('job_id')->toArray(),
                'applied' => JobApplication::where('user_id', $userId)->pluck('job_id')->toArray(),
            ];
        });
        $savedJobIds = $userJobStatus['saved'];
        $appliedJobIds = $userJobStatus['applied'];

        //  Filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%');
            });
        }

        // "Recommended for you" — match against the user's saved core
        // skills instead of a text search. Same matching logic used for
        // the dashboard's Recommended Jobs count, so the number there
        // and the jobs shown here always agree.
        $isRecommended = $request->boolean('recommended');
        $noProfileSkills = false;

        if ($isRecommended) {
            $profile = UserProfile::where('user_id', $userId)->first();

            if ($profile && ! empty($profile->core_skills)) {
                $userSkills = array_map('trim', explode(',', $profile->core_skills));
                $this->applySkillMatch($query, $userSkills);
                $query->whereNotIn('id', $appliedJobIds);
            } else {
                // No skills on file to match against — show nothing
                // rather than silently falling back to "all jobs".
                $noProfileSkills = true;
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%'.$request->location.'%');
        }
        if ($request->filled('min_salary')) {
            $query->where('min_salary', '>=', $request->min_salary);
        }
        if ($request->filled('max_salary')) {
            $query->where('max_salary', '<=', $request->max_salary);
        }

        $jobs = $query->orderBy('id', 'desc')->paginate(5);
        $categories = JobCategory::allCached();
        $roles = JobRole::allCached();
        $totalUsers = User::count();

        // Trending Jobs — top viewed live jobs, shown only on the default
        // (unfiltered, first-page) view so it doesn't clutter search results.
        $trendingJobs = collect();
        if (! $isRecommended && ! $request->anyFilled(['search', 'category', 'role', 'location', 'min_salary', 'max_salary']) && $request->input('page', 1) == 1) {
            $trendingJobs = Job::with(['category', 'admin'])
                ->visible()
                ->where(function ($q) {
                    $q->whereNull('last_date')->orWhereDate('last_date', '>=', now());
                })
                ->orderByDesc('views_count')
                ->take(5)
                ->get();
        }

        return view('User.user_job_show', compact(
            'jobs',
            'categories',
            'roles',
            'totalUsers',
            'savedJobIds',
            'appliedJobIds',
            'isRecommended',
            'noProfileSkills',
            'trendingJobs'
        ));
    }

    /**
     * Shared skill-matching filter used by both the dashboard's
     * "Recommended Jobs" count and the full recommended-jobs listing —
     * keeping this in one place means the count and the actual results
     * can never drift out of sync with each other.
     */
    private function applySkillMatch($query, array $userSkills): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'mysql') {
            // FULLTEXT index (added in Phase4) — no full table scan.
            $query->whereRaw('MATCH(required_skills) AGAINST(? IN BOOLEAN MODE)', [
                implode(' ', array_map(fn ($s) => '"'.str_replace('"', '', $s).'"', $userSkills)),
            ]);
        } else {
            // SQLite (local dev) has no FULLTEXT support for plain
            // columns — fall back to the original LIKE-OR chain.
            $query->where(function ($q) use ($userSkills) {
                foreach ($userSkills as $skill) {
                    $q->orWhere('required_skills', 'LIKE', '%'.$skill.'%');
                }
            });
        }
    }

    public function user_job_single($id)
    {
        $userId = Auth::guard('user')->id();
        $singlejob = Job::with(['category', 'role', 'admin'])->findOrFail($id);

        // Job was hidden by SuperAdmin moderation (e.g. reported as fake/spam)
        // — don't let anyone reach it via a direct link either.
        if ($singlejob->is_hidden) {
            abort(404);
        }

        // View tracking for the admin's job-performance analytics
        // (applications / views conversion rate). De-duped per session
        // so refreshing the page doesn't inflate the count.
        $viewedKey = 'viewed_job_'.$singlejob->id;
        if (! session()->has($viewedKey)) {
            $singlejob->increment('views_count');
            session()->put($viewedKey, true);
        }

        $jobs = Job::with(['category', 'role', 'admin'])
            ->visible()
            ->where('category_id', $singlejob->category_id)
            ->where('id', '!=', $singlejob->id)
            ->whereDate('last_date', '>=', now())
            ->take(6)
            ->get();

        return view('User.user_job_single', compact('singlejob', 'jobs'));
    }

    public function job_applied(Request $request)
    {
        $userId = Auth::guard('user')->id();

        $query = JobApplication::with(['job.admin', 'testimonial'])->where('user_id', $userId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('job', function ($q) use ($search) {
                $q->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhereHas('admin', function ($q2) use ($search) {
                        $q2->where('company_name', 'LIKE', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(5)->withQueryString();

        return view('User.user_applied_jobs', compact('applications'));
    }

    public function User_profile()
    {
        $user = Auth::guard('user')->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        return view('User.profile', compact('user', 'profile'));
    }

    public function add_user_profile()
    {
        $userId = Auth::guard('user')->id();
        $profile = UserProfile::firstOrCreate(['user_id' => $userId]);
        $educationData = $profile->education ?? [];

        return view('User.profile_add', compact('profile', 'educationData'));
    }

    public function update_user_profile(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $profile = UserProfile::firstOrCreate(['user_id' => $userId]);
        $data = $request->validate([
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'professional_summary' => 'nullable|string|max:2000',
            'core_skills' => 'nullable|string|max:500',
            'education' => 'nullable|array',
            'education.*.degree' => 'nullable|string|max:255',
            'education.*.institute' => 'nullable|string|max:255',
            'education.*.year' => 'nullable|string|max:20',
            'experience' => 'nullable|array',
            'experience.*.company' => 'nullable|string',
            'experience.*.role' => 'nullable|string',
            'experience.*.duration' => 'nullable|string',
            'experience.*.description' => 'nullable|string',
            'projects' => 'nullable|array',
            'projects.*.title' => 'nullable|string|max:255',
            'projects.*.tech' => 'nullable|string|max:255',
            'projects.*.link' => 'nullable|url|max:500',
            'projects.*.description' => 'nullable|string',
        ]);

        //  Handle Profile Image (Storage System)
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($profile->profile_image) {
                Storage::disk('public')
                    ->delete('user_profile/'.$profile->profile_image);
            }
            // Store new image
            $path = $request->file('profile_image')->store('user_profile', 'public');
            if (! $this->verifyStoredMime('public', $path, ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['profile_image' => 'Invalid file type.']);
            }
            $data['profile_image'] = basename($path);
        }
        // Education JSON
        if ($request->has('education') && is_array($request->education)) {
            $data['education'] = $request->education;
        }
        $profile->update($data);

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function account_setting()
    {
        $userId = Auth::guard('user')->id();
        $user_data = Auth::guard('user')->user();

        return view('User.user_account_setting', compact('user_data'));
    }

    public function account_setting_update(Request $request)
    {
        $user_data = auth('user')->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user_data->id,
            'current_password' => ['required_with:password', function ($attr, $value, $fail) use ($user_data) {
                if ($value && ! Hash::check($value, $user_data->password)) {
                    $fail('Current password is incorrect.');
                }
            }],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => 'required|digits:10',
            'address' => 'nullable|string|max:255',
        ]);

        unset($data['current_password']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            \App\Models\UserActivityLog::log($user_data->id, 'password_changed', $request);
        } else {
            unset($data['password']);
        }

        $user_data->update($data);

        return back()->with('success', 'Account details updated successfully!');
    }

    /**
     * GDPR — "Right to data portability". Bundles everything Job Hub
     * holds about this candidate into a single downloadable JSON file.
     * Raw resume/profile-photo binaries aren't embedded (they're already
     * downloadable individually from the Resume Library / profile page);
     * this covers structured personal data instead.
     */
    public function dataExport(Request $request)
    {
        $user = Auth::guard('user')->user();

        $user->load([
            'profile',
            'jobApplications.job.admin',
            'resumes',
            'savedJobs',
            'jobAlert',
            'jobInvites.job.admin',
            'companyFollows.company',
            'testimonials',
            'activityLogs',
        ]);

        $export = [
            'exported_at' => now()->toIso8601String(),
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'referral_code' => $user->referral_code,
                'account_created_at' => $user->created_at?->toIso8601String(),
            ],
            'profile' => $user->profile ? [
                'professional_summary' => $user->profile->professional_summary,
                'core_skills' => $user->profile->core_skills,
                'education' => $user->profile->education,
                'experience' => $user->profile->experience,
                'projects' => $user->profile->projects,
                'open_to_work' => $user->profile->open_to_work,
            ] : null,
            'job_applications' => $user->jobApplications->map(fn ($app) => [
                'job_title' => $app->job->title ?? null,
                'company' => $app->job->admin->company_name ?? null,
                'status' => $app->status,
                'cover_letter' => $app->cover_letter,
                'expected_salary' => $app->expected_salary,
                'notice_period' => $app->notice_period,
                'applied_at' => $app->created_at?->toIso8601String(),
            ]),
            'resumes' => $user->resumes->map(fn ($resume) => [
                'title' => $resume->title,
                'original_name' => $resume->original_name,
                'file_size_bytes' => $resume->file_size,
                'is_default' => $resume->is_default,
                'uploaded_at' => $resume->created_at?->toIso8601String(),
            ]),
            'saved_jobs' => $user->savedJobs->map(fn ($job) => [
                'job_title' => $job->title,
                'saved_at' => $job->pivot->created_at?->toIso8601String(),
            ]),
            'job_alert' => $user->jobAlert ? [
                'keywords' => $user->jobAlert->keywords,
                'is_active' => $user->jobAlert->is_active,
                'last_sent_at' => $user->jobAlert->last_sent_at?->toIso8601String(),
            ] : null,
            'job_invites' => $user->jobInvites->map(fn ($invite) => [
                'job_title' => $invite->job->title ?? null,
                'company' => $invite->admin->company_name ?? null,
                'status' => $invite->status,
                'invited_at' => $invite->created_at?->toIso8601String(),
            ]),
            'companies_followed' => $user->companyFollows->map(fn ($follow) => [
                'company' => $follow->company->company_name ?? null,
                'followed_at' => $follow->created_at?->toIso8601String(),
            ]),
            'testimonials_submitted' => $user->testimonials->map(fn ($t) => [
                'company' => $t->company,
                'review' => $t->review,
                'rating' => $t->rating,
                'status' => $t->status,
                'submitted_at' => $t->created_at?->toIso8601String(),
            ]),
            'activity_log' => $user->activityLogs->map(fn ($log) => [
                'action' => $log->action_label,
                'at' => $log->created_at?->toIso8601String(),
            ]),
        ];

        \App\Models\UserActivityLog::log($user->id, 'data_exported', $request);

        $filename = 'job-hub-data-export-'.now()->format('Y-m-d').'.json';

        return response()->json($export, 200, [
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * GDPR — "Right to erasure". Permanently deletes the candidate's
     * account after confirming their password. Related rows (profile,
     * applications, resumes, saved jobs, alerts, invites, activity log,
     * company follows) cascade-delete at the database level; resume/
     * profile-photo files are cleaned up in User::booted(). Testimonials
     * and job reports the user submitted are kept but anonymised
     * (user_id set null) rather than deleted outright, since they're
     * part of the historical record other users/employers rely on.
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::guard('user')->user();

        $request->validate([
            'delete_password' => ['required', function ($attr, $value, $fail) use ($user) {
                if (! Hash::check($value, $user->password)) {
                    $fail('Incorrect password. Account was not deleted.');
                }
            }],
        ]);

        $userId = $user->id;

        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        User::findOrFail($userId)->delete();

        \Illuminate\Support\Facades\Log::info("GDPR account deletion completed for user id {$userId}.");

        return redirect()->route('user.home')
            ->with('success', 'Your account and personal data have been permanently deleted.');
    }

    public function invites()
    {
        $userId = Auth::guard('user')->id();
        $invites = \App\Models\JobInvite::with(['job', 'admin'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        // Mark pending invites as viewed once the user opens this page.
        \App\Models\JobInvite::where('user_id', $userId)
            ->where('status', 'pending')
            ->update(['status' => 'viewed']);

        return view('User.invites', compact('invites'));
    }

    public function activityLog()
    {
        $userId = Auth::guard('user')->id();
        $logs = \App\Models\UserActivityLog::where('user_id', $userId)
            ->latest()
            ->paginate(15);

        return view('User.activity_log', compact('logs'));
    }

    public function toggleOpenToWork(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $profile = UserProfile::firstOrCreate(['user_id' => $userId]);
        $profile->open_to_work = ! $profile->open_to_work;
        $profile->save();

        return back()->with('success', $profile->open_to_work
            ? 'You are now marked as Open to Work.'
            : 'You are no longer marked as Open to Work.');
    }

    public function readNotifications()
    {
        Auth::guard('user')->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    public function saveJob(Job $job)
    {
        $user = auth('user')->user();

        $alreadySaved = $user->savedJobs()->where('jobs.id', $job->id)->exists();

        if (! $alreadySaved) {
            $limit = \App\Services\BadgeLimitService::limitFor($user, 'saved_jobs');
            $currentCount = $user->savedJobs()->count();

            if ($limit !== null && $currentCount >= $limit) {
                return back()->with('error',
                    "You can save up to {$limit} jobs. Remove one, or refer more friends to raise this limit.");
            }
        }

        $user->savedJobs()->syncWithoutDetaching([$job->id]);

        return back()->with('success', 'Job saved successfully.');
    }

    public function unsaveJob(Job $job)
    {
        auth('user')->user()->savedJobs()->detach($job->id);

        return back()->with('success', 'Job removed from saved.');
    }
}
