<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "job_queue" collided visually with the "jobs" (job-listings) table
     * and only worked because config/queue.php hardcoded the table name
     * to match (Phase4 issue #6). Renaming to "queue_jobs" removes the
     * confusion; config/queue.php is updated in the same change.
     *
     * Also adds the queue/available_at indexes the queue worker needs
     * for its polling query — missing from the original migration
     * (Phase4 issue #16). php artisan queue:table generates these by
     * default; this migration brings the table in line with that.
     */
    public function up(): void
    {
        Schema::rename('job_queue', 'queue_jobs');

        Schema::table('queue_jobs', function (Blueprint $table) {
            $table->index('queue');
            $table->index('available_at');
        });
    }

    public function down(): void
    {
        Schema::table('queue_jobs', function (Blueprint $table) {
            $table->dropIndex(['queue']);
            $table->dropIndex(['available_at']);
        });

        Schema::rename('queue_jobs', 'job_queue');
    }
};
