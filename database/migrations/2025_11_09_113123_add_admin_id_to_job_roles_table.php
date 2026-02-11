<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_roles', function (Blueprint $table) {
            // admin_id add karo (foreign key ke saath)
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_roles', function (Blueprint $table) {
            // rollback ke liye column delete karo
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
