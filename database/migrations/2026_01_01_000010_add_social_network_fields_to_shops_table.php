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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->text('maps_url')->nullable()->after('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['facebook_url', 'instagram_url', 'youtube_url', 'maps_url']);
        });
    }
};
