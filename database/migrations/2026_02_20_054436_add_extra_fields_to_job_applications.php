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
       Schema::table('job_applications', function (Blueprint $table) {

    $table->string('expected_salary')->nullable();
    $table->string('notice_period')->nullable();
    $table->text('admin_note')->nullable();
    $table->timestamp('status_updated_at')->nullable();
    $table->foreignId('updated_by_admin_id')->nullable()
          ->constrained('admins')->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            //
        });
    }
};
