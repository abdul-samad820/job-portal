<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CompanyFollow;
use App\Models\Job;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;

class CompanyProfileController extends Controller
{
    /**
     * Public company profile page — /company/{slug}.
     *
     * Only ever shows real, active employer accounts: role must be
     * 'admin' (never expose a super_admin row here) and the account
     * must be active. A company with zero jobs still gets a page
     * (their profile/testimonials are still legitimate to show) —
     * only the jobs list will be empty.
     */
    public function show(string $slug)
    {
        $company = Admin::where('role', 'admin')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $activeJobs = Job::with(['category', 'role'])
            ->where('admin_id', $company->id)
            ->live()
            ->latest()
            ->paginate(9);

        $testimonials = Testimonial::where('admin_id', $company->id)
            ->where('status', Testimonial::STATUS_APPROVED)
            ->latest()
            ->take(9)
            ->get();

        $followerCount = CompanyFollow::where('admin_id', $company->id)->count();

        $isFollowing = false;
        if (Auth::guard('user')->check()) {
            $isFollowing = CompanyFollow::where('admin_id', $company->id)
                ->where('user_id', Auth::guard('user')->id())
                ->exists();
        }

        $totalActiveJobs = Job::where('admin_id', $company->id)->live()->count();

        return view('company_profile', compact(
            'company',
            'activeJobs',
            'testimonials',
            'followerCount',
            'isFollowing',
            'totalActiveJobs'
        ));
    }
}
