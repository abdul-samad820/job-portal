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
protected static function boot()
{
    parent::boot();

    static::deleting(function ($job) {

        if ($job->job_image &&
            Storage::disk('public')->exists('jobs/' . $job->job_image)) {

            Storage::disk('public')
                ->delete('jobs/' . $job->job_image);
        }
    });
}
}


