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
            $table->bigInteger('order_detail_id')->unsigned()->nullable()->after('product_id');
            
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_id')) {
                $table->dropColumn('order_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->constrained('order_details')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_detail_id']);
            $table->dropColumn('order_detail_id');
        });
    }
};