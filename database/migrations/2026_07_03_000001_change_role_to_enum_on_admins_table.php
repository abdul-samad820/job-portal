<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * admins.role was a plain string with no DB-level enforcement — any
     * value could be inserted, silently breaking role checks throughout
     * the app (Phase4 issue #5). This locks it down to the two values
     * actually used in the codebase: 'admin' and 'super_admin'.
     *
     * Uses a raw ALTER TABLE instead of Blueprint::change() because
     * ->change() requires doctrine/dbal, which is not installed in this
     * project (see Phase4 issue #4). SQLite has no real column-type
     * enforcement, so this is effectively a no-op there — the constraint
     * matters on the production MySQL/MariaDB target.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY role ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY role VARCHAR(255) NOT NULL DEFAULT 'admin'");
        }
    }
};
