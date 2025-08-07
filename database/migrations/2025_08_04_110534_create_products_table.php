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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 20, 2);
            $table->text('description')->nullable();
            $table->integer('view')->default(0);
            $table->integer('stock')->default(0);
            $table->decimal('min_bid_amount', 20, 2);
            $table->decimal('max_bid_amount', 20, 2);
            $table->enum('type_sale', ['sale', 'auction'])->default('sale');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
