<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail of every sensitive SuperAdmin action (create/suspend/
     * unsuspend/delete/edit an Admin, hide/delete a Job, etc). This is
     * append-only from the app's perspective — logs are never edited.
     */
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('superadmin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('action');            // e.g. admin_created, admin_suspended
            $table->string('subject_type')->nullable(); // e.g. Admin, Job
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
