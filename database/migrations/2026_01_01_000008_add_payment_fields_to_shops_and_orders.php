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
            $table->string('upi_id')->nullable()->after('whatsapp_phone');
            $table->string('upi_name')->nullable()->after('upi_id');
            $table->string('upi_qr_image')->nullable()->after('upi_name');
            $table->text('bank_details')->nullable()->after('upi_qr_image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('total_amount');
            $table->text('payment_notes')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['upi_id', 'upi_name', 'upi_qr_image', 'bank_details']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_notes']);
        });
    }
};
