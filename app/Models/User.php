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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'referral_code',
        'referred_by',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(8));
                } while (self::where('referral_code', $code)->exists());
                $user->referral_code = $code;
            }
        });

        static::deleting(function ($user) {

            if ($user->profile && $user->profile->profile_image) {

                Storage::disk('public')->delete(
                    'user_profile/'.$user->profile->profile_image
                );
            }

            $user->profile()?->delete();

            // Resume rows are removed by the DB's ON DELETE CASCADE, which
            // bypasses Eloquent events entirely — so Resume's own file
            // cleanup hook never runs for them. Delete the physical files
            // here before the cascade removes the rows underneath us.
            foreach ($user->resumes as $resume) {
                if ($resume->file_path && Storage::disk('public')->exists($resume->file_path)) {
                    Storage::disk('public')->delete($resume->file_path);
                }
            }
        });
    }

    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs')
            ->withTimestamps();
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class)->latest();
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function jobAlert()
    {
        return $this->hasOne(JobAlert::class);
    }

    public function jobInvites()
    {
        return $this->hasMany(JobInvite::class);
    }

    public function companyFollows()
    {
        return $this->hasMany(CompanyFollow::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }
}
