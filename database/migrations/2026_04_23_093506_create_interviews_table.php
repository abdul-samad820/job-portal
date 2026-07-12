<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_application_id')
                ->constrained('job_applications')
                ->onDelete('cascade');

            $table->foreignId('admin_id')
                ->constrained('admins')
                ->onDelete('cascade');

            $table->date('interview_date');
            $table->time('interview_time');

            $table->enum('mode', ['online', 'offline'])
                ->default('online');

            // Online → meeting link, Offline → address
            $table->string('location')->nullable();

            // Additional info admin
            $table->text('notes')->nullable();

            // Status tracking
            $table->enum('status', [
                'scheduled',
                'rescheduled',
                'cancelled',
                'completed',
            ])->default('scheduled');

            $table->timestamps();
            $table->unique('job_application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
