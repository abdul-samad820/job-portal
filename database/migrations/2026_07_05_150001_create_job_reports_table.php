<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason'); // e.g. spam, fake, scam, expired, offensive, other
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending'); // pending, reviewed, dismissed
            $table->timestamps();

            // A user shouldn't be able to spam-report the same job repeatedly
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_reports');
    }
};
