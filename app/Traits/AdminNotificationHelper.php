<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait AdminNotificationHelper
{
    public function hasNotification($adminId, $type)
    {
        return DB::table('notifications')
            ->where('notifiable_id', $adminId)
            ->where('type', $type)
            ->whereNull('read_at')
            ->exists();
    }
}