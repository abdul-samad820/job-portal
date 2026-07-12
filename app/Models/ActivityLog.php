<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $table = 'admin_activity_logs';

    protected $fillable = ['superadmin_id', 'admin_id', 'action', 'subject_type', 'subject_id', 'description'];

    /**
     * Record an action performed by the currently-logged-in SuperAdmin.
     * Called from SuperAdminController/SuperAdminJobController etc so
     * every sensitive action has a traceable "who did what, when".
     */
    public static function log(string $action, string $description, $subject = null): void
    {
        static::create([
            'superadmin_id' => Auth::guard('superadmin')->id(),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject->id ?? null,
            'description' => $description,
        ]);
    }

    /**
     * Same idea, but for a company admin acting within their own
     * account (creating/editing/deleting their jobs, categories,
     * applications). Previously these actions left no trail at all.
     */
    public static function logAdmin(string $action, string $description, $subject = null): void
    {
        static::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject->id ?? null,
            'description' => $description,
        ]);
    }

    public function superadmin()
    {
        return $this->belongsTo(Admin::class, 'superadmin_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Whoever actually performed the action, regardless of which
     * column it's stored in — lets views render one unified "Actor"
     * field without an if/else in every blade.
     */
    public function getActorAttribute()
    {
        return $this->admin ?? $this->superadmin;
    }

    public function getActorRoleAttribute(): string
    {
        return $this->admin_id ? 'Company Admin' : 'SuperAdmin';
    }
}
