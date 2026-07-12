<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase4 issue #9: several columns used in WHERE/JOIN conditions on
     * every page load had no index — full table scans on jobs, job
     * applications, notifications, and admin-list queries.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->index('last_date');
            $table->index('type');
            $table->index('experience');
            $table->index('category_id');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Speeds up "unread notifications for this notifiable" — the
            // query run on every page load for the notification bell.
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_unread_index');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['last_date']);
            $table->dropIndex(['type']);
            $table->dropIndex(['experience']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_unread_index');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
        });
    }
};
