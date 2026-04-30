<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable implements CanResetPassword
{
    use CanResetPasswordTrait, HasFactory  , Notifiable;

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
        'role',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
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
                Storage::disk('public')->exists('admins/'.$admin->profile_image)) {

                Storage::disk('public')
                    ->delete('admins/'.$admin->profile_image);
            }
        });
    }
}
