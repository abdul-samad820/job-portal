<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'email',
        'password',
        'company_name',
        'description',
        'contact_number',
        'location',
        'expertise',
        'profile_image',
    ];
    // 'role' and 'is_active' are intentionally NOT mass-assignable —
    // they control admin privileges and must only be set explicitly
    // via forceFill()/direct assignment (see SuperAdminController).

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'must_change_password' => 'boolean',
            'verified_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // Relationship
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    // All applications submitted to any of this admin's jobs
    public function jobApplications()
    {
        return $this->hasManyThrough(JobApplication::class, Job::class);
    }

    public function jobCategories()
    {
        return $this->hasMany(JobCategory::class);
    }

    public function jobRoles()
    {
        return $this->hasMany(JobRole::class);
    }

    public function jobInvites()
    {
        return $this->hasMany(JobInvite::class);
    }

    public function followers()
    {
        return $this->hasMany(CompanyFollow::class);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'admin_id');
    }

    // Auto delete profile image when admin deleted
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            if (empty($admin->slug) && ! empty($admin->company_name)) {
                $admin->slug = static::generateUniqueSlug($admin->company_name);
            }
        });

        static::deleting(function ($admin) {

            if ($admin->profile_image &&
                Storage::disk('public')->exists('admins/'.$admin->profile_image)) {

                Storage::disk('public')
                    ->delete('admins/'.$admin->profile_image);
            }

            // Jobs, job categories, and job applications under this admin
            // are removed by the DB's ON DELETE CASCADE, which bypasses
            // Eloquent model events — so each model's own file-cleanup
            // hook (job_image, category_image, resume snapshot) never
            // runs for them. Clean up those physical files here first.
            $jobs = $admin->jobs()->get(['id', 'job_image']);

            \App\Models\JobApplication::whereIn('job_id', $jobs->pluck('id'))
                ->whereNotNull('resume')
                ->get(['resume'])
                ->each(function ($application) {
                    if (Storage::disk('public')->exists($application->resume)) {
                        Storage::disk('public')->delete($application->resume);
                    }
                });

            foreach ($jobs as $job) {
                if ($job->job_image && Storage::disk('public')->exists($job->job_image)) {
                    Storage::disk('public')->delete($job->job_image);
                }
            }

            foreach ($admin->jobCategories()->get(['id', 'category_image']) as $category) {
                if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
                    Storage::disk('public')->delete($category->category_image);
                }
            }
        });
    }

    /**
     * Base slug + numeric suffix on collision, same idea as the
     * User model's referral_code generation loop.
     */
    protected static function generateUniqueSlug(string $companyName): string
    {
        $base = \Illuminate\Support\Str::slug($companyName) ?: 'company';
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    public function getRouteKeyForCompanyProfile(): string
    {
        return $this->slug ?? (string) $this->id;
    }
}
