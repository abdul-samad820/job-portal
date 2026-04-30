<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    protected $fillable = [
        'user_id',
        'keywords',
        'is_active',
        'last_sent_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    // ─────────────────────────────────────
    // Relationship: JobAlert belongs to User
    // ─────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getKeywordsArrayAttribute(): array
    {
        return array_map(
            'trim',
            explode(',', strtolower($this->keywords))
        );
    }
}
