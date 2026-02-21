<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Job extends Model
{
    protected $fillable = ['title','description','location',
     'overview','responsibilities','required_skills', 'experience', 'salary','type','last_date','category_id','role_id','admin_id','job_image'];

    
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
    if (!auth('user')->check()) {
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


