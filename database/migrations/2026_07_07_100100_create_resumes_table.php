<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A dedicated resume library lets a job seeker upload several PDFs
     * (e.g. "Frontend Resume", "Backend Resume") once and re-use any of
     * them across multiple job applications, instead of re-uploading a
     * file every single time they apply.
     */
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title'); // e.g. "Frontend Developer Resume"
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
