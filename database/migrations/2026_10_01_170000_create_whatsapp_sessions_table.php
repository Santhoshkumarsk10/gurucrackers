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
        if (!Schema::hasTable('whatsapp_sessions')) {
            Schema::create('whatsapp_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->default('default_session')->unique();
                $table->longText('session_payload');
                $table->unsignedInteger('files_count')->default(0);
                $table->string('connected_phone')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
