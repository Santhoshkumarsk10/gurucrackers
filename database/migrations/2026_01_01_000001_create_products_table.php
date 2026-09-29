<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->string('unit')->default('1 Box'); // 1 Pkt / 1 Box / 1 Pcs
            $table->decimal('actual_rate', 10, 2);     // MRP
            $table->decimal('net_rate', 10, 2);        // final selling price
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
