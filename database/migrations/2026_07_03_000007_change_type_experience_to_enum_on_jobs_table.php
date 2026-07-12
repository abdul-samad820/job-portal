<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * jobs.type was an unconstrained string — the app only ever uses a
     * fixed set of values in forms/filters, but nothing stopped a direct
     * DB insert from breaking filter matching with a typo'd or unexpected
     * value (Phase4 issue #13). (jobs.experience was already a proper
     * ENUM from an earlier migration — no change needed there.)
     *
     * Raw SQL used instead of Blueprint::change() — doctrine/dbal isn't
     * installed in this project (see Phase4 issue #4).
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE jobs MODIFY type ENUM('Full-time', 'Part-time', 'Internship', 'Contract') NOT NULL DEFAULT 'Full-time'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE jobs MODIFY type VARCHAR(255) NOT NULL DEFAULT 'Full-time'");
        }
    }
};
