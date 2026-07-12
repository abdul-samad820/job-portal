<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';  // ← explicitly set so Laravel doesn't guess wrong

    protected $fillable = [
        'user_id',
        'professional_summary',
        'core_skills',
        'education',
        'experience',
        'projects',
        'profile_image',
        'open_to_work',
    ];

    protected $casts = [
        'education' => 'array',
        'experience' => 'array',
        'projects' => 'array',
        'open_to_work' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
