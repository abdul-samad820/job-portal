<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
     protected $fillable = [
        'job_id','user_id','status','resume','cover_letter',
    ];

    public function job()
{ 
    return $this->belongsTo(Job::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

protected static function boot()
{
    parent::boot();

    static::deleting(function ($application) {

        if ($application->resume &&
            Storage::disk('public')->exists('resumes/' . $application->resume)) {

            Storage::disk('public')
                ->delete('resumes/' . $application->resume);
        }
    });
}
}
