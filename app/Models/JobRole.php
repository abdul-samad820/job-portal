<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_id', 'admin_id'];

    public function jobcategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'role_id');
    }
}
