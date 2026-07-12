<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Email-based OTP for SuperAdmin login. SuperAdmin is the
     * highest-privilege account on the platform (can delete companies,
     * hide jobs, suspend users) — a leaked password alone should not be
     * enough to get in.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('two_factor_code')->nullable()->after('remember_token');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at']);
        });
    }
};
