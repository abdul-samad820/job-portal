<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OldNotifications extends Command
{
    protected $signature = 'app:delete-old-notifications';

    protected $description = 'Delete read notifications older than 30 days to keep the notifications table small';

    /**
     * At ~72 rows/admin/day (hourly CheckAdminNotifications run), the
     * notifications table would otherwise hit millions of rows within a
     * year with no way to shrink back down (Phase6 PERF-18). Only READ
     * notifications are removed — unread ones are kept regardless of age
     * so nothing is silently deleted before the admin/user has seen it.
     */
    public function handle(): void
    {
        $cutoff = now()->subDays(30);

        $deleted = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} read notifications older than 30 days.");
    }
}
