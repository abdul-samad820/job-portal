<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;
use Carbon\Carbon;

class DeleteExpiredJobs extends Command
{
    protected $signature = 'app:delete-expired-jobs';

    protected $description = 'Delete all expired jobs from database';

    public function handle()
    {
        $deleted = Job::whereDate('last_date', '<', Carbon::today())->delete();

        $this->info($deleted . ' expired jobs deleted successfully.');
    }
}