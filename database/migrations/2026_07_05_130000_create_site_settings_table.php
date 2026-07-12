<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Global, platform-wide configuration (contact info, social links, etc.)
     * managed exclusively by the SuperAdmin. Unlike jobs/faqs/testimonials,
     * this table has NO admin_id — it is not owned by any individual
     * company/recruiter, it belongs to the platform as a whole.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general'); // contact, social, hero
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
