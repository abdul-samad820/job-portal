<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase4 issue #7: the FK on user_profiles.user_id had no accompanying
     * unique constraint, so nothing at the DB level stopped a second
     * profile row for the same user (only PHP-level updateOrCreate logic
     * prevented it).
     *
     * Phase4 issue #12: education/experience are stored as JSON strings
     * but declared as plain TEXT, losing MySQL's JSON validation/indexing.
     * Converted via raw SQL (not Blueprint::change()) since this project
     * doesn't have doctrine/dbal installed.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unique('user_id');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_profiles MODIFY education JSON NULL');
            DB::statement('ALTER TABLE user_profiles MODIFY experience JSON NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_profiles MODIFY education TEXT NULL');
            DB::statement('ALTER TABLE user_profiles MODIFY experience TEXT NULL');
        }

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
