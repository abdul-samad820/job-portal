<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Faq;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Setting;
use App\Models\Testimonial;
use App\View\Composers\AdminLayoutComposer;
use App\View\Composers\SuperAdminLayoutComposer;
use App\View\Composers\UserLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // ── Layout Composers ────────────────────────────────────────────────
        // These replace the @php DB-query blocks that were previously living
        // inside the layout blade files themselves, violating SRP and making
        // the layouts untestable.

        // Supplies $adminUser, $notifications, $unreadCount to Admin layout
        View::composer('layouts.Admin_layout', AdminLayoutComposer::class);
        View::composer('layouts.superadmin', SuperAdminLayoutComposer::class);

        // Supplies $authUser, $userImg, $notifications, $unreadCount to User layout
        View::composer('layouts.User_layout', UserLayoutComposer::class);

        // ── Existing Composers ───────────────────────────────────────────────
        View::composer('Admin.*', function ($view) {
            if (auth('admin')->check()) {
                $adminId = auth('admin')->id();

                $recentJobs = Job::where('admin_id', $adminId)
                    ->orderBy('id', 'desc')
                    ->take(5)
                    ->get();

                $view->with('recentJobs', $recentJobs);
            } else {
                $view->with('recentJobs', collect());
            }
        });

        View::composer('User.register', function ($view) {
            $view->with('liveJobsCount', Job::whereDate('last_date', '>=', now())->count());
            $view->with('verifiedCompaniesCount', Admin::where('is_active', true)->count());
        });

        // Footer / global site stats — scoped to the shared landing_page
        // LAYOUT (not just the 'home' view), since the footer that displays
        // these lives in layouts.landing_page and is rendered on every page
        // that extends it (apply form, job listings, privacy policy, terms,
        // etc.). Previously these were only bound on the 'home' view, so
        // every other page fell back to the footer's `?? 0` default and
        // showed "0+ Live Jobs" / "0+ Companies" / "0+ Job Seekers".
        View::composer('layouts.landing_page', function ($view) {
            $view->with('totalJobsCount', Job::count());
            $view->with('totalCompaniesCount', Admin::where('is_active', true)->count());
            // "Verified Companies" used to just mean "any active admin" —
            // now it means SuperAdmin has actually confirmed them.
            $view->with('verifiedCompaniesCount', Admin::where('is_active', true)->where('is_verified', true)->count());
            $view->with('totalUsersCount', \App\Models\User::count());

            // Global site settings (contact info, social links) — managed
            // solely by SuperAdmin, replaces hardcoded email/location/links.
            $view->with('settings', Setting::allSettings());
        });

        // Homepage-only sections — recent jobs, categories, FAQs and
        // testimonials are only rendered on the home view itself, so they
        // stay scoped there. The hero section on home.blade.php ALSO reads
        // totalJobsCount/verifiedCompaniesCount directly (no ?? fallback),
        // so those are re-supplied here too — the layout composer above
        // only reaches the layout's own template (the footer), not the
        // child view's @section('content') scope.
        View::composer(['home'], function ($view) {

            $view->with('totalJobsCount', Job::count());
            $view->with('totalCompaniesCount', Admin::where('is_active', true)->count());
            $view->with('verifiedCompaniesCount', Admin::where('is_active', true)->where('is_verified', true)->count());
            $view->with('totalUsersCount', \App\Models\User::count());
            $view->with('settings', Setting::allSettings());

            // recent jobs
            $view->with(
                'recentJobs',
                Job::visible()->latest()->take(5)->get()
            );

            // categories
            $view->with(
                'all_categories',
                JobCategory::withCount('jobs')->get()
            );

            // FAQs (NEW) — only globally-managed FAQs (admin_id null, i.e.
            // curated by SuperAdmin) show on the public homepage. Previously
            // this pulled ANY admin's FAQ regardless of ownership, letting
            // any company admin post content directly to the public site.
            $view->with(
                'faqs',
                Faq::whereNull('admin_id')->where('status', 1)->latest()->get()
            );

            // Testimonials (NEW) — only admin-approved reviews show publicly.
            // Show the latest 4 only (most recently added first).
            $view->with(
                'testimonials',
                Testimonial::approved()->latest()->take(4)->get()
            );
        });
    }
}
