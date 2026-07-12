<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase4 issue #11: faqs had no admin_id, so every FAQ was global and
     * unattributed — any admin could edit/delete any other admin's FAQs.
     * Nullable: null = global FAQ (managed by SuperAdmin), set = owned by
     * that specific admin/company.
     */
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')
                ->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
