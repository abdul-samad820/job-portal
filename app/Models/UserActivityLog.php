<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
    ];

    public const ACTION_LABELS = [
        'login' => 'Logged in',
        'logout' => 'Logged out',
        'password_changed' => 'Changed password',
        'profile_updated' => 'Updated profile',
        'data_exported' => 'Exported account data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity row for a job seeker. Keeps the call site
     * (UserController etc) a one-liner instead of duplicating the
     * request-context boilerplate everywhere.
     */
    public static function log(int $userId, string $action, ?\Illuminate\Http\Request $request = null): void
    {
        $request = $request ?? request();

        static::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }
}
