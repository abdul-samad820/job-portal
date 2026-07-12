<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobInvite extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'admin_id',
        'message',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
