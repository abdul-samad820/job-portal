<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We keep the existing `resume` string column as-is — it stores the
     * file path actually used for this specific application (a snapshot),
     * so an application still shows the right file even if the user later
     * renames/deletes the library entry. `resume_id` just links back to
     * the library resume that produced that snapshot, when one was used.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->foreignId('resume_id')->nullable()
                ->after('resume')
                ->constrained('resumes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['resume_id']);
            $table->dropColumn('resume_id');
        });
    }
};
