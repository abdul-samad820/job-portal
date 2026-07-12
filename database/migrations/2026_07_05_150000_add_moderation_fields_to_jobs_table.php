<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SuperAdmin needs to be able to hide a suspicious/reported job from
     * public view WITHOUT deleting it (deleting destroys evidence and any
     * applications tied to it). is_hidden is independent of the Admin's
     * own last_date/active-listing logic.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('admin_id');
            $table->text('hidden_reason')->nullable()->after('is_hidden');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['is_hidden', 'hidden_reason']);
        });
    }
};
