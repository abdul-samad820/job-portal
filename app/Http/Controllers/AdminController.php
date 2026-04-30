<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\LoginSecurityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $data['email'])->first();

        if ($admin && ! $admin->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact SuperAdmin.',
            ]);
        }

        if (Auth::guard('admin')->attempt($data)) {

            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

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

    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        $adminId = $admin->id;
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');
        $totalJobs = Job::where('admin_id', $adminId)->count();
        $activeJobs = Job::where('admin_id', $adminId)->where('last_date', '>=', now())->count();
        $expiredJobs = Job::where('admin_id', $adminId)->where('last_date', '<', now())->count();
        $totalApplications = JobApplication::whereIn('job_id', $jobIds)->count();
        $pendingCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'pending')->count();
        $shortlistedCount = JobApplication::whereIn('job_id', $jobIds)
            ->where('status', 'shortlisted')->count();
        $hiredCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count();
        $rejectedCount = JobApplication::whereIn('job_id', $jobIds)->where('status', 'rejected')
            ->count();
        $topJob = Job::withCount('applications')->where('admin_id', $adminId)->orderBy('applications_count', 'desc')->first();
        $expiringJobs = Job::where('admin_id', $adminId)->whereBetween('last_date', [now(), now()->addDays(2)])->count();

        return view('Admin.admin_dashboard', compact(
            'totalJobs', 'activeJobs', 'expiredJobs', 'totalApplications', 'pendingCount', 'shortlistedCount', 'hiredCount', 'rejectedCount', 'topJob', 'expiringJobs'
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

    public function admin_profile(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $profile = Auth::guard('admin')->user();
        $totalJobs = Job::where('admin_id', $adminId)->count();
        $jobIds = Job::where('admin_id', $adminId)->pluck('id');
        $totalApplications = JobApplication::whereIn('job_id', $jobIds)->count();

        return view('Admin.profile', compact('profile', 'totalJobs', 'totalApplications'));
    }

    public function update_profile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'location' => 'nullable|string|max:255',
            'expertise' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                Storage::disk('public')->delete('admins/'.$admin->profile_image);
            }
            $path = $request->file('profile_image')->store('admins', 'public');
            $data['profile_image'] = basename($path);
        }

        $admin->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function readNotifications()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return back();
    }
}
