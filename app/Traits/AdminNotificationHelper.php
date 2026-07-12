<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait AdminNotificationHelper
{
    public function hasNotification($adminId, $type, $jobId = null)
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', $adminId)
            ->where('type', $type)
            ->whereNull('read_at');

        if ($jobId !== null) {
            $query->where('data->job_id', $jobId);
        }

        return $query->exists();
    }
}
