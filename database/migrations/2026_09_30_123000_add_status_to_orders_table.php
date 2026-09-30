<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->after('total_amount')->index();
            });
        }

        // Migrate and normalize existing records to strict specification:
        // status: pending | confirmed | packed | dispatched | cancelled
        // payment_status: pending | paid

        // 1. Dispatched orders
        DB::table('orders')
            ->where(function ($q) {
                $q->where('payment_status', 'dispatched')
                  ->orWhereNotNull('lr_number')
                  ->orWhereNotNull('dispatched_at');
            })
            ->update([
                'status' => 'dispatched',
                'payment_status' => 'paid',
            ]);

        // 2. Confirmed or Packed orders
        DB::table('orders')
            ->where('payment_status', 'confirmed')
            ->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

        DB::table('orders')
            ->where('payment_status', 'packed')
            ->update([
                'status' => 'packed',
                'payment_status' => 'paid',
            ]);

        // 3. Paid orders
        DB::table('orders')
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

        // 4. Any leftover payment_status that is not 'paid' must be 'pending'
        DB::table('orders')
            ->whereNotIn('payment_status', ['pending', 'paid'])
            ->update([
                'payment_status' => 'pending',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
