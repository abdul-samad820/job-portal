<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Job;
use App\Policies\JobPolicy;
use App\Models\JobCategory;
use App\Policies\JobCategoryPolicy;
use App\Models\JobRole;
use App\Policies\JobRolePolicy;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Job::class => JobPolicy::class,
        JobCategory::class =>JobCategoryPolicy::class,
        JobRole::class =>JobRolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
  public function boot(): void
{
    $this->registerPolicies();

    $this->app->booted(function () {
        \Illuminate\Support\Facades\Auth::shouldUse('admin');
    });
}
}
