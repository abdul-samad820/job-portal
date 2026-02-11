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
        Schema::create('jobs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
    $table->foreignId('category_id')->constrained('job_categories')->onDelete('cascade');
    $table->string('title');
    $table->text('description');
    $table->string('location');
    $table->decimal('salary', 10, 2)->nullable();
    $table->string('type')->default('Full-time'); // e.g. Full-time, Part-time
    $table->date('last_date')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('jobs');
    }
};
