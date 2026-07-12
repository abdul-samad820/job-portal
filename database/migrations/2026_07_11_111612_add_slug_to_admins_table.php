<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Public company profile pages (/company/{slug}) need a stable,
     * URL-safe identifier that isn't the raw company name (spaces,
     * special characters, duplicates) or the internal numeric id
     * (not user-friendly / leaks row counts).
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('company_name');
        });

        // Backfill existing rows. company_name has no unique constraint,
        // so two admins could collide on the base slug — append the id
        // whenever that happens to guarantee uniqueness.
        DB::table('admins')->orderBy('id')->select('id', 'company_name')->chunkById(100, function ($admins) {
            foreach ($admins as $admin) {
                $base = Str::slug($admin->company_name ?: 'company-'.$admin->id);
                $slug = $base;

                if (DB::table('admins')->where('slug', $slug)->where('id', '!=', $admin->id)->exists()) {
                    $slug = $base.'-'.$admin->id;
                }

                DB::table('admins')->where('id', $admin->id)->update(['slug' => $slug]);
            }
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
