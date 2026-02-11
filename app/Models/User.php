<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password','phone','address'];
    
  protected function casts(): array {
        return [
            'password' => 'hashed',
        ];
    }

    public function Admin(){
      return  $this->hasMany(Admin::class);
    } 

    public function profile(){
    return $this->hasOne(User_profile::class);
}

}
