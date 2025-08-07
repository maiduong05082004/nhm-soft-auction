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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code_orders');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email_receiver');
            $table->string('ship_address');
            $table->integer('payment_method');
            $table->decimal('shipping_fee', 20, 2);
            $table->decimal('subtotal', 20, 2);
            $table->decimal('total', 20, 2);
            $table->text('note')->nullable();
            $table->text('canceled_reason')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
