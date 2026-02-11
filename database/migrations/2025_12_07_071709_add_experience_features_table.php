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
    Schema::table('jobs', function (Blueprint $table) {
        $table->enum('experience', [
            'Fresher',
            '1 Year',
            '2 Years',
            '3 Years',
            '3+ Years'
        ])->nullable()->after('salary');
    });
}



    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('jobs', function (Blueprint $table) {
        $table->dropColumn('experience');
    });
}

};
