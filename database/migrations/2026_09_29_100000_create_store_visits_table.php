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
        Schema::create('store_visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->date('visited_date')->index();
            $table->unsignedInteger('page_views')->default(1);
            $table->string('device', 20)->default('mobile')->index(); // mobile, desktop, tablet
            $table->string('platform', 50)->nullable(); // Android, iOS, Windows, Mac, Linux
            $table->string('browser', 50)->nullable(); // Chrome, Safari, Firefox, Edge
            $table->string('first_page', 255)->nullable();
            $table->string('last_page', 255)->nullable();
            $table->string('referrer_source', 100)->nullable()->index(); // Direct / QR Code, WhatsApp, Google, etc.
            $table->timestamps();

            $table->unique(['visited_date', 'session_id'], 'unique_daily_session_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_visits');
    }
};
