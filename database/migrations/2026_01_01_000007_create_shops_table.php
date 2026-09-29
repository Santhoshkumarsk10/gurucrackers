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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Guru Crackers');
            $table->string('tagline')->nullable()->default('Sivakasi Direct Wholesale & Retail Crackers');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable()->default('Sivakasi');
            $table->string('pincode')->nullable()->default('626123');
            $table->string('offer')->nullable()->default('💥 DIWALI 2026 SPECIAL OFFER | தீபாவளி மெகா தள்ளுபடி');
            $table->unsignedSmallInteger('offer_percentage')->nullable()->default(90);
            $table->string('banner_notice')->nullable()->default('✨ Sivakasi Direct Factory Prices | 100% Genuine Green Crackers | Mega Festival Discount');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
