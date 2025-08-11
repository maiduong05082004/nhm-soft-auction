<?php
// database/migrations/2025_08_08_071116_update_status_orders_to_orders_table.php

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
            $table->dropColumn('status');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('status')
                  ->default(1)
                  ->comment('1=new, 2=processing, 3=shipped, 4=delivered, 5=cancelled')
                  ->after('canceled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled'])
                  ->default('pending')
                  ->after('canceled_at');
        });
    }
};