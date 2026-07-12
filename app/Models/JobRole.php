<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class JobRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_id', 'admin_id'];

    /**
     * Cached list of all roles — same rationale as JobCategory::allCached()
     * (Phase6 PERF-08). Auto-busted on save/delete.
     */
    public static function allCached()
    {
        return Cache::remember('job_roles_all', 3600, fn () => static::all());
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(fn () => Cache::forget('job_roles_all'));
        static::deleted(fn () => Cache::forget('job_roles_all'));
    }

    public function jobcategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'role_id');
    }
}
