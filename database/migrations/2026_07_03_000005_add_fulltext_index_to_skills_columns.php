<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase4 issue #10 (short-term fix): core_skills/required_skills are
     * comma-separated TEXT with no way to search them at the DB level.
     * A FULLTEXT index lets `WHERE MATCH(required_skills) AGAINST(...)`
     * run efficiently instead of loading every row into PHP.
     *
     * The audit's long-term recommendation — normalizing into a proper
     * `skills` table with pivot tables — is a larger schema redesign
     * that touches the matching logic in JobApplicationController and is
     * left as a separate follow-up task.
     *
     * FULLTEXT requires MySQL/MariaDB (InnoDB 5.6+); skipped on SQLite,
     * which has no FULLTEXT support for plain TEXT columns.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_profiles ADD FULLTEXT core_skills_fulltext (core_skills)');
            DB::statement('ALTER TABLE jobs ADD FULLTEXT required_skills_fulltext (required_skills)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE user_profiles DROP INDEX core_skills_fulltext');
            DB::statement('ALTER TABLE jobs DROP INDEX required_skills_fulltext');
        }
    }
};
