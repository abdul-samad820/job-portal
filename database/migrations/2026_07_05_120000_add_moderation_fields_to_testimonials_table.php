<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Turns testimonials from "admin types it, it's instantly live" into a
     * proper submission + moderation workflow:
     *   - user_id / job_application_id trace who actually wrote the review
     *     and which (hired) application it came from.
     *   - status changes from a boolean (show/hide) into a 3-state
     *     moderation flag: pending / approved / rejected. Existing rows
     *     were all admin-authored and already "live", so they map to
     *     approved (status=1) or rejected (status=0).
     */
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('admin_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('job_application_id')->nullable()->after('user_id')
                ->constrained('job_applications')->nullOnDelete();

            // Temporary column — populated from the old boolean `status`,
            // then swapped in below. Keeping this as a two-step avoids any
            // ambiguity in how existing 1/0 values should be interpreted.
            $table->string('moderation_status', 20)->default('pending')->after('status');
        });

        DB::table('testimonials')->where('status', 1)->update(['moderation_status' => 'approved']);
        DB::table('testimonials')->where('status', 0)->update(['moderation_status' => 'rejected']);

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->renameColumn('moderation_status', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->boolean('status_bool')->default(1)->after('status');
        });

        DB::table('testimonials')->where('status', 'approved')->update(['status_bool' => 1]);
        DB::table('testimonials')->whereIn('status', ['pending', 'rejected'])->update(['status_bool' => 0]);

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->renameColumn('status_bool', 'status');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
