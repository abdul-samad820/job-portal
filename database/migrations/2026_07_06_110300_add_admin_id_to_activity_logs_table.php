<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * admin_activity_logs previously only recorded SuperAdmin actions.
     * Company admins creating/editing/deleting their own jobs and
     * categories, or changing an applicant's status, had zero audit
     * trail — a real gap if there's ever a dispute about who did what.
     * This adds a second, independent actor column so a log row is
     * either "done by this superadmin" OR "done by this company admin",
     * never both.
     */
    public function up(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('superadmin_id')
                ->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
        });
    }
};
