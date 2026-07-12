<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SuperAdmin sets the initial password when creating a company
     * admin account, meaning SuperAdmin knows that password until the
     * admin changes it. New accounts must be forced to set their own
     * password on first login. Existing accounts are grandfathered in
     * (false) so nobody who's already using the system gets locked out.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(true)->after('is_verified');
        });

        DB::table('admins')->update(['must_change_password' => false]);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
