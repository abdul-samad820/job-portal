<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
     protected $fillable = [
    'job_id',
    'user_id',
    'status',
    'resume',
    'cover_letter',
    'expected_salary',
    'notice_period',
    'admin_note',
    'status_updated_at',
    'updated_by_admin_id'
];

    public function job()
{ 
    return $this->belongsTo(Job::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

public function updatedBy()
{
    return $this->belongsTo(Admin::class, 'updated_by_admin_id');
} 

protected static function boot()
{
    parent::boot();

    static::deleting(function ($application) {

        if ($application->resume &&
            Storage::disk('public')->exists($application->resume)) {

            Storage::disk('public')->delete($application->resume);
        }
    });
}
}