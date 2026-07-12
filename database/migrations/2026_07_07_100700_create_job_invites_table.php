<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a company (Admin) proactively invite a job seeker who is
     * marked "Open to Work" to apply for one of their open roles,
     * instead of only waiting for inbound applications.
     */
    public function up(): void
    {
        Schema::create('job_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'viewed', 'applied', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_invites');
    }
};
