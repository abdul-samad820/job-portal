<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'admin_id',
        'category_image',
    ];

    /**
     * Cached list of all categories — used to populate filter/select
     * dropdowns. Categories change rarely (an admin adds one occasionally)
     * but were being re-queried from the DB on every job listing and job
     * add/edit page load. Cache is auto-busted via the boot() hooks below
     * whenever a category is created, updated, or deleted.
     */
    public static function allCached()
    {
        return Cache::remember('job_categories_all', 3600, fn () => static::all());
    }

    public function jobroles()
    {
        return $this->hasMany(JobRole::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'category_id');
    }

    protected static function boot()
    {

        parent::boot();

        static::saved(fn () => Cache::forget('job_categories_all'));
        static::deleted(fn () => Cache::forget('job_categories_all'));

        static::deleting(function ($category) {

            if ($category->category_image &&
                Storage::disk('public')->exists($category->category_image)) {

                Storage::disk('public')->delete($category->category_image);
            }

        });
    }
}
