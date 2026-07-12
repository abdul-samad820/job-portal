<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    // Moderation states. Admin-authored testimonials are created as
    // APPROVED directly (the admin is trusted content, no queue needed).
    // User-submitted reviews always start as PENDING and need an admin
    // to approve them before they show on the homepage.
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'admin_id',
        'user_id',
        'job_application_id',
        'name',
        'designation',
        'company',
        'image',
        'review',
        'rating',
        'status',
        'sort_order',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * True when this testimonial came from a job seeker's own submission
     * (as opposed to being typed in directly by an admin).
     */
    public function isUserSubmitted(): bool
    {
        return ! is_null($this->user_id);
    }
}
