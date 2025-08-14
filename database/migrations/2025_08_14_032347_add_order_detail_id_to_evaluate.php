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
        Schema::table('evaluate', function (Blueprint $table) {
            if (Schema::hasColumn('evaluate', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            
            $table->bigInteger('order_detail_id')->unsigned()->nullable()->after('product_id');
            
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluate', function (Blueprint $table) {
            $table->dropForeign(['order_detail_id']);
            
            $table->dropColumn('order_detail_id');
            
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
        });
    }
};
