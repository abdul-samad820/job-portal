<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}


