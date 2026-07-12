<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'location',
        'overview', 'responsibilities', 'required_skills', 'experience',
        'min_salary', 'max_salary',   'type', 'last_date', 'category_id',
        'role_id', 'admin_id', 'job_image', 'is_hidden', 'hidden_reason'];

    protected $casts = [
        'last_date' => 'date',
    ];

    public function reports()
    {
        return $this->hasMany(JobReport::class);
    }

    /**
     * Scope used by every PUBLIC-facing job query (homepage, listings,
     * search) so a job hidden by SuperAdmin moderation never appears,
     * without needing to touch every individual query site.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * A job is only "live" through the end of its last_date — the day
     * AFTER last_date it flips to expired automatically, with no manual
     * action or scheduled job needed. This is computed on read, so the
     * row is never deleted or mutated just because time passed; the
     * record (and its applications) stay intact for history/analytics.
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('last_date', '<', now()->toDateString());
    }

    public function scopeLive($query)
    {
        return $query->where('is_hidden', false)
            ->where(function ($q) {
                $q->whereNull('last_date')
                    ->orWhereDate('last_date', '>=', now()->toDateString());
            });
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->last_date !== null && $this->last_date->lt(now()->startOfDay());
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function role()
    {
        return $this->belongsTo(JobRole::class, 'role_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs')
            ->withTimestamps();
    }

    public function isSavedByUser()
    {
        if (! auth('user')->check()) {
            return false;
        }

        return $this->savedByUsers()
            ->where('user_id', auth('user')->id())
            ->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($job) {

            if ($job->job_image &&
                Storage::disk('public')->exists($job->job_image)) {

                Storage::disk('public')->delete($job->job_image);
            }
        });
    }
}
