<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;   // ✅ IMPORTANT

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
}