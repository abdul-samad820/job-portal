<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\EditAdminRequest;
use App\Http\Requests\SuperAdminLoginRequest;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Job;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function login(SuperAdminLoginRequest $request)
    {
        $data = $request->validated();

        // BUG FIX: this used to be Auth::guard('superadmin')->attempt($data)
        // with no role filter — since the 'superadmin' guard's provider
        // points at the SAME Admin model/table as the regular 'admin'
        // guard, ANY company admin could log into /superadmin/login with
        // their own normal credentials and get full SuperAdmin access.
        $admin = Admin::where('email', $data['email'])->where('role', 'super_admin')->first();

        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (! $admin->is_active) {
            return back()->withErrors(['email' => 'This account has been suspended.'])->withInput();
        }

        // Don't log in yet — email a one-time code first. SuperAdmin can
        // delete companies and suspend users; a password alone shouldn't
        // be enough to get in.
        $this->issueTwoFactorCode($admin);

        $request->session()->put('2fa_superadmin_id', $admin->id);

        return redirect()->route('superadmin.2fa.form');
    }

    public function twoFactorForm(Request $request)
    {
        if (! $request->session()->has('2fa_superadmin_id')) {
            return redirect()->route('superadmin.login.view');
        }

        return view('SuperAdmin.two_factor');
    }

    public function twoFactorVerify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $adminId = $request->session()->get('2fa_superadmin_id');

        if (! $adminId) {
            return redirect()->route('superadmin.login.view');
        }

        $admin = Admin::find($adminId);

        if (! $admin
            || ! $admin->two_factor_code
            || ! $admin->two_factor_expires_at
            || now()->gt($admin->two_factor_expires_at)
            || ! Hash::check($request->code, $admin->two_factor_code)) {
            return back()->withErrors(['code' => 'That code is invalid or has expired. Request a new one.']);
        }

        $admin->forceFill(['two_factor_code' => null, 'two_factor_expires_at' => null])->save();
        $request->session()->forget('2fa_superadmin_id');

        Auth::guard('superadmin')->login($admin);
        $request->session()->regenerate();

        ActivityLog::create([
            'superadmin_id' => $admin->id,
            'action' => 'superadmin_login',
            'subject_type' => 'Admin',
            'subject_id' => $admin->id,
            'description' => 'SuperAdmin logged in (2FA verified)',
        ]);

        return redirect()->route('superadmin.dashboard');
    }

    public function twoFactorResend(Request $request)
    {
        $adminId = $request->session()->get('2fa_superadmin_id');

        if (! $adminId) {
            return redirect()->route('superadmin.login.view');
        }

        $admin = Admin::find($adminId);

        if (! $admin) {
            return redirect()->route('superadmin.login.view');
        }

        $this->issueTwoFactorCode($admin);

        return back()->with('success', 'A new code has been sent to your email.');
    }

    private function issueTwoFactorCode(Admin $admin): void
    {
        $code = (string) random_int(100000, 999999);

        $admin->forceFill([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        $admin->notify(new \App\Notifications\SuperAdminTwoFactorCodeNotification($code));
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login.view');
    }

    public function dashboard()
    {
        $adminStats = Admin::where('role', 'admin')
            ->selectRaw('count(*) as total, sum(case when is_active = 1 then 1 else 0 end) as active')
            ->first();

        $totalAdmins = $adminStats->total;
        $activeAdmins = $adminStats->active;
        $suspendedAdmins = $totalAdmins - $activeAdmins;

        $totalUsers = User::count();
        $totalJobs = Job::count();
        $totalApplications = \App\Models\JobApplication::count();

        // "Needs attention" counts — surfaced on the dashboard so a
        // SuperAdmin logging in sees exactly what's pending without
        // having to click into every section to check.
        $pendingJobReports = \App\Models\JobReport::pending()->count();
        $pendingTestimonials = \App\Models\Testimonial::pending()->count();
        $unreadMessages = \App\Models\ContactMessage::unread()->count();

        return view('SuperAdmin.dashboard', compact(
            'totalAdmins',
            'activeAdmins',
            'suspendedAdmins',
            'totalUsers',
            'totalJobs',
            'totalApplications',
            'pendingJobReports',
            'pendingTestimonials',
            'unreadMessages'
        ));
    }

    public function adminList(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status'); // active | suspended

        $admins = Admin::where('role', 'admin')
            ->withCount(['jobs', 'jobApplications'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'suspended', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('SuperAdmin.admin_list', compact('admins', 'search', 'status'));
    }

    public function exportAdminsCsv(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $admins = Admin::where('role', 'admin')
            ->withCount(['jobs', 'jobApplications'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'suspended', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->get();

        $filename = 'admins_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($admins) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Company Name', 'Email', 'Contact', 'Location', 'Jobs', 'Applications', 'Status', 'Joined']);

            foreach ($admins as $admin) {
                fputcsv($handle, [
                    $admin->id,
                    $admin->company_name,
                    $admin->email,
                    $admin->contact_number,
                    $admin->location,
                    $admin->jobs_count,
                    $admin->job_applications_count,
                    $admin->is_active ? 'Active' : 'Suspended',
                    $admin->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function createForm()
    {
        if (auth('superadmin')->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('SuperAdmin.create_admin');
    }

    public function createAdmin(CreateAdminRequest $request)
    {
        if (auth('superadmin')->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validated();

        // BUG FIX: contact_number, location, and description are all
        // 'nullable' in CreateAdminRequest — when the SuperAdmin leaves
        // one blank, validated() simply won't include that key at all.
        // Accessing $data['contact_number'] directly then threw an
        // "Undefined array key" error and the whole request 500'd.
        $admin = new Admin([
            'company_name' => $data['company_name'],
            'contact_number' => $data['contact_number'] ?? null,
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        ActivityLog::log('admin_created', "Created admin \"{$admin->company_name}\" ({$admin->email})", $admin);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'New Admin Created Successfully!');

    }

    public function editForm($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        return view('SuperAdmin.edit_admin', compact('admin'));
    }

    public function updateAdmin(EditAdminRequest $request, $id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        $data = $request->validated();

        // Same optional-field guard as createAdmin() above — these are
        // nullable on EditAdminRequest too, so they can be absent from
        // validated() entirely.
        $admin->company_name = $data['company_name'];
        $admin->contact_number = $data['contact_number'] ?? null;
        $admin->location = $data['location'] ?? null;
        $admin->description = $data['description'] ?? null;
        $admin->email = $data['email'];

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
            // SuperAdmin now knows this password until the admin changes
            // it — same reasoning as initial account creation, so re-apply
            // the same forced-rotation requirement on every reset, not
            // just the first one.
            $admin->must_change_password = true;
        }

        $admin->save();

        ActivityLog::log('admin_updated', "Updated details for \"{$admin->company_name}\" ({$admin->email})", $admin);

        return redirect()->route('superadmin.admins')
            ->with('success', 'Admin details updated successfully.');
    }

    /**
     * Platform-wide site settings (contact info, social links, etc).
     * Not scoped to any admin_id — this is global config, editable only
     * by the SuperAdmin, never by individual company Admins.
     */
    public function settingsIndex()
    {
        $settings = Setting::allSettings();

        return view('SuperAdmin.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_location' => ['required', 'string', 'max:255'],
            'working_hours' => ['required', 'string', 'max:100'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Site settings updated successfully.');
    }

    public function suspendAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        $admin->forceFill(['is_active' => false])->save();
        $this->killAdminSessions($admin->id);

        ActivityLog::log('admin_suspended', "Suspended \"{$admin->company_name}\" ({$admin->email})", $admin);

        return back()->with('success', " {$admin->company_name} suspended successfully.");
    }

    /**
     * Force-logout every device a company admin is currently logged in
     * on. Used when suspending an account so access ends immediately,
     * rather than only on that admin's *next* request when Admin_mid
     * would otherwise catch the is_active flag.
     */
    private function killAdminSessions(int $adminId): void
    {
        \Illuminate\Support\Facades\DB::table('sessions')->get()->each(function ($row) use ($adminId) {
            try {
                // DatabaseSessionHandler always base64-encodes the payload
                // column, regardless of SESSION_ENCRYPT — that outer layer
                // must come off first before decrypting what's underneath.
                $raw = base64_decode($row->payload, true);

                if ($raw === false) {
                    return;
                }

                $decoded = config('session.encrypt')
                    ? \Illuminate\Support\Facades\Crypt::decryptString($raw)
                    : $raw;

                $data = @unserialize($decoded);

                if (is_array($data) && ($data['admin_session_owner'] ?? null) == $adminId) {
                    \Illuminate\Support\Facades\DB::table('sessions')->where('id', $row->id)->delete();
                }
            } catch (\Throwable $e) {
                // Unreadable row — skip it rather than fail the suspend action.
            }
        });
    }

    public function unsuspendAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        $admin->forceFill(['is_active' => true])->save();

        ActivityLog::log('admin_unsuspended', "Reactivated \"{$admin->company_name}\" ({$admin->email})", $admin);

        return back()->with('success', " {$admin->company_name} reactivated successfully.");
    }

    /**
     * Real company verification — separate from "active". A company can
     * be active (able to log in and post jobs) without being verified;
     * only SuperAdmin explicitly confirming their legitimacy earns the
     * "Verified" badge shown to job seekers.
     */
    public function verifyAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        $admin->forceFill(['is_verified' => true, 'verified_at' => now()])->save();

        ActivityLog::log('admin_verified', "Verified \"{$admin->company_name}\" ({$admin->email})", $admin);

        return back()->with('success', "{$admin->company_name} marked as verified.");
    }

    public function unverifyAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        $admin->forceFill(['is_verified' => false, 'verified_at' => null])->save();

        ActivityLog::log('admin_unverified', "Removed verification from \"{$admin->company_name}\" ({$admin->email})", $admin);

        return back()->with('success', "Verification removed from {$admin->company_name}.");
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::where('role', 'admin')->withCount(['jobs', 'jobApplications'])->findOrFail($id);

        // Hard-deleting an admin CASCADE-deletes every job they posted,
        // every application to those jobs, saved-jobs, reports, interviews...
        // If there's real data on the line, force the safer path (suspend)
        // instead of silently wiping a company's history and every job
        // seeker's application record for it.
        if ($admin->jobs_count > 0 || $admin->job_applications_count > 0) {
            return back()->with('error',
                "Can't delete \"{$admin->company_name}\" — they have {$admin->jobs_count} job(s) and ".
                "{$admin->job_applications_count} application(s) on file. Suspend them instead, or ".
                'remove their jobs first if you really need to delete the account.'
            );
        }

        $name = $admin->company_name;
        $email = $admin->email;
        $adminId = $admin->id;
        $admin->delete();

        ActivityLog::create([
            'superadmin_id' => Auth::guard('superadmin')->id(),
            'action' => 'admin_deleted',
            'subject_type' => 'Admin',
            'subject_id' => $adminId,
            'description' => "Deleted admin \"{$name}\" ({$email})",
        ]);

        return back()->with('success', ' Admin deleted successfully.');
    }

    /**
     * Chronological audit trail of every sensitive SuperAdmin action.
     */
    public function activityLog()
    {
        $logs = ActivityLog::with(['superadmin', 'admin'])->latest()->paginate(25);

        return view('SuperAdmin.activity_log', compact('logs'));
    }

    /**
     * Bulk suspend/unsuspend/delete — logs one summary entry rather than
     * one per admin so the activity log stays readable for large batches.
     */
    public function bulkAction(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:suspend,unsuspend,delete',
            'admin_ids' => 'required|array|min:1',
            'admin_ids.*' => 'integer|exists:admins,id',
        ]);

        $admins = Admin::where('role', 'admin')->whereIn('id', $data['admin_ids'])->get();
        $names = $admins->pluck('company_name')->implode(', ');
        $count = $admins->count();

        switch ($data['action']) {
            case 'suspend':
                Admin::where('role', 'admin')->whereIn('id', $data['admin_ids'])->update(['is_active' => false]);
                $actionLabel = 'suspended';
                break;
            case 'unsuspend':
                Admin::where('role', 'admin')->whereIn('id', $data['admin_ids'])->update(['is_active' => true]);
                $actionLabel = 'reactivated';
                break;
            case 'delete':
                foreach ($admins as $admin) {
                    $admin->delete(); // triggers boot() cleanup (profile image etc) per admin
                }
                $actionLabel = 'deleted';
                break;
        }

        ActivityLog::log(
            'admin_bulk_'.$data['action'],
            "Bulk {$actionLabel} {$count} admin(s): {$names}"
        );

        return back()->with('success', "{$count} admin(s) {$actionLabel} successfully.");
    }

    public function showAdmin($id)
    {
        $admin = Admin::where('role', 'admin')
            ->withCount(['jobs', 'jobs as active_jobs_count' => function ($q) {
                $q->where('last_date', '>=', now());
            }])
            ->findOrFail($id);

        return response()->json($admin->only([
            'id', 'company_name', 'email', 'contact_number', 'location',
            'description', 'expertise', 'profile_image', 'is_active',
            'jobs_count', 'active_jobs_count', 'created_at',
        ]));
    }

    public function readNotifications()
    {
        auth('superadmin')->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
