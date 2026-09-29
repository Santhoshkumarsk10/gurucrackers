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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('parcel_service_name')->nullable()->after('payment_notes');
            $table->string('lr_number')->nullable()->after('parcel_service_name');
            $table->string('parcel_count')->nullable()->after('lr_number');
            $table->date('dispatch_date')->nullable()->after('parcel_count');
            $table->string('transport_phone')->nullable()->after('dispatch_date');
            $table->string('destination_hub')->nullable()->after('transport_phone');
            $table->string('lr_receipt_image')->nullable()->after('destination_hub');
            $table->text('dispatch_notes')->nullable()->after('lr_receipt_image');
            $table->timestamp('dispatched_at')->nullable()->after('dispatch_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'parcel_service_name',
                'lr_number',
                'parcel_count',
                'dispatch_date',
                'transport_phone',
                'destination_hub',
                'lr_receipt_image',
                'dispatch_notes',
                'dispatched_at',
            ]);
        });
    }
};
