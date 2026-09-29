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
            $table->string('state', 100)->nullable()->default('Tamil Nadu')->after('city');
        });

        // Set existing records to 'Tamil Nadu'
        \Illuminate\Support\Facades\DB::table('orders')
            ->whereNull('state')
            ->orWhere('state', '')
            ->update(['state' => 'Tamil Nadu']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
};
