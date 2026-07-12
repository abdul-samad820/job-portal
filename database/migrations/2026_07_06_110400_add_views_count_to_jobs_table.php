<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a company admin see conversion (applications / views) per job,
     * not just raw application counts — a job with lots of views but
     * few applications usually means the description/pay/requirements
     * need work, which a raw application count alone can't tell you.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->after('is_hidden');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('views_count');
        });
    }
};
