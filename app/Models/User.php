<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
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
        return $this->hasOne(UserProfile::class);
    }

    protected static function booted()
    {
        static::deleting(function ($user) {

            if ($user->profile && $user->profile->profile_image) {

                Storage::disk('public')->delete(
                    'user_profile/'.$user->profile->profile_image
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
