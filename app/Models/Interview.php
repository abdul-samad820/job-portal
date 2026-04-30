<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'job_application_id',
        'admin_id',
        'interview_date',
        'interview_time',
        'mode',
        'location',
        'notes',
        'status',
    ];

    protected $casts = [
        'interview_date' => 'date',
    ];

    // ─────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────
    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // ─────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────

    // "14 Apr 2026, 10:30 AM" format
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->interview_date->format('d M Y').
               ', '.
               Carbon::createFromTimeString($this->interview_time)->format('h:i A');
    }

    // Interview aane wala hai? (aaj ya future)
    public function getIsUpcomingAttribute(): bool
    {
        return $this->interview_date->isFuture() ||
               $this->interview_date->isToday();
    }

    // Status badge color ke liye
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'success',
            'rescheduled' => 'warning',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary',
        };
    }
}
