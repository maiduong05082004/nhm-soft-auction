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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('type_sale')->default(1)->comment('1: sale, 2: auction')->change();
            $table->integer('status')->default(1)->comment('0: inactive, 1: active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('type_sale', ['sale', 'auction'])->default('sale');
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }
};
