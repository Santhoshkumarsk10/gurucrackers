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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('message_id', 150)->nullable()->index();
            $table->string('remote_jid', 100)->nullable()->index();
            $table->string('phone', 25)->index();
            $table->boolean('from_me')->default(false)->index();
            $table->string('sender_name')->nullable();
            $table->string('message_type', 30)->default('text');
            $table->longText('message_text')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_filename')->nullable();
            $table->string('status', 30)->default('sent');
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
