<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobReport extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_DISMISSED = 'dismissed';

    public const REASONS = [
        'fake' => 'Fake / Fraudulent job',
        'spam' => 'Spam',
        'scam' => 'Asking for money / Scam',
        'expired' => 'Expired but still listed',
        'offensive' => 'Offensive content',
        'other' => 'Other',
    ];

    protected $fillable = ['job_id', 'user_id', 'reason', 'details', 'status'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
