<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
      use Notifiable;
       public $timestamps = false;

       protected $guard = 'admin';

    protected $fillable = ['email', 'password','company_name','description','contact_number','location', 'expertise','profile_image','role'];

    protected function casts(): array {
        return [
            'password' => 'hashed',
        ];
    }
    public function jobs(){
        return $this->hasMany(job::class);
    }
}
