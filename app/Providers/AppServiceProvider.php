<?php

namespace App\Providers;

use App\Models\Faq;
use App\Models\Job;
use App\Models\JobCategory;
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

        // View::composer('*', function ($view) {
        View::composer(['home'], function ($view) {

            // recent jobs
            $view->with(
                'recentJobs',
                Job::latest()->take(5)->get()
            );

            // categories
            $view->with(
                'all_categories',
                JobCategory::withCount('jobs')->get()
            );

            // FAQs (NEW)
            $view->with(
                'faqs',
                Faq::where('status', 1)->latest()->get()
            );
        });
    }
}
