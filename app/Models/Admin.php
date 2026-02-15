<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $guard = 'admin';

    protected $fillable = [
        'email',
        'password',
        'company_name',
        'description',
        'contact_number',
        'location',
        'expertise',
        'profile_image',
        'role'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relationship
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    // Auto delete profile image when admin deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($admin) {

            if ($admin->profile_image &&
                Storage::disk('public')->exists('admins/' . $admin->profile_image)) {

                Storage::disk('public')
                    ->delete('admins/' . $admin->profile_image);
            }
        });
    }
}