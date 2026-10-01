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
        Schema::table('shops', function (Blueprint $table) {
            $table->decimal('min_order_amount', 10, 2)->default(2500.00)->after('offer_percentage');
        });

        // Set default minimum order amount for existing shop records
        DB::table('shops')->update(['min_order_amount' => 2500.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('min_order_amount');
        });
    }
};
