<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_profile extends Model
{
    protected $fillable = [ 'user_id','professional_summary', 'core_skills', 'education','experience','profile_image'];

    protected $casts = [
        'education' => 'array'
    ];
}
