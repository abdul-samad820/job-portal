<?php

namespace App\Console\Commands;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredJobs extends Command
{
    protected $signature = 'app:delete-expired-jobs';

    protected $description = 'Delete all expired jobs from database';

    public function handle()
    {
        $deleted = Job::whereDate('last_date', '<', Carbon::today())->delete();

        $this->info($deleted.' expired jobs deleted successfully.');
    }
}
