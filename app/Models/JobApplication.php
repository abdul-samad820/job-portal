<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * Single source of truth for status→color, used by the API response
     * and available for Blade views. Matches the Bootstrap badge classes
     * already used in user_dashboard.blade.php and the hex colors fixed
     * in the application-status email (Phase3 issue #8) — Phase9 UX-08.
     */
    public const STATUS_COLORS = [
        'pending' => ['badge_class' => 'badge-warning', 'hex' => '#6c757d'],
        'shortlisted' => ['badge_class' => 'badge-info', 'hex' => 'orange'],
        'hired' => ['badge_class' => 'badge-success', 'hex' => 'green'],
        'rejected' => ['badge_class' => 'badge-danger', 'hex' => 'red'],
    ];

    public function statusColor(): array
    {
        return self::STATUS_COLORS[$this->status] ?? ['badge_class' => 'badge-secondary', 'hex' => '#6c757d'];
    }

    protected $fillable = [
        'job_id',
        'user_id',
        'status',
        'resume',
        'resume_id',
        'cover_letter',
        'expected_salary',
        'notice_period',
        'admin_note',
        'status_updated_at',
        'updated_by_admin_id',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resumeFile()
    {
        return $this->belongsTo(Resume::class, 'resume_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(ApplicationStatusHistory::class)->latest();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($application) {

            if ($application->resume &&
                Storage::disk('public')->exists($application->resume)) {

                Storage::disk('public')->delete($application->resume);
            }
        });
    }

    public function interview()
    {
        return $this->hasOne(Interview::class, 'job_application_id');
    }
}
