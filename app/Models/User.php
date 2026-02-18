<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function Admin()
    {
        return $this->hasMany(Admin::class);
    }

    public function profile()
    {
        return $this->hasOne(User_profile::class);
    }
    protected static function booted()
{
    static::deleting(function ($user) {

        if ($user->profile && $user->profile->profile_image) {

            Storage::disk('public')->delete(
                'user_profile/' . $user->profile->profile_image
            );
        }

        $user->profile()?->delete();
    });
}
public function savedJobs()
{
    return $this->belongsToMany(Job::class, 'saved_jobs')
        ->withTimestamps();
}
}