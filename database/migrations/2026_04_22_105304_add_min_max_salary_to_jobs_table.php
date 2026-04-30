<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('salary');
            $table->unsignedInteger('min_salary')->nullable()->after('location');
            $table->unsignedInteger('max_salary')->nullable()->after('min_salary');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['min_salary', 'max_salary']);
            $table->integer('salary')->nullable()->after('location');
        });
    }
};
