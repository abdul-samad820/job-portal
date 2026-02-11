<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
  use Illuminate\Support\Facades\View;
use App\Models\Job;
use App\Models\JobCategory;

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

    View::composer('*', function ($view) {

        
        if (!$view->offsetExists('recentJobs')) {
            $view->with(
                'recentJobs',
                Job::orderBy('id', 'desc')->take(5)->get()
            );
        }

        $view->with(
            'all_categories',
            JobCategory::withCount('jobs')->get()
        );
    });
}

}
