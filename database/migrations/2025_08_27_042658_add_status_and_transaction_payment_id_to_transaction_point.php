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
        Schema::table('transaction_point', function (Blueprint $table) {
            $table->integer('status')->default(1)->change();
            $table->foreignId('transaction_payment_id')->constrained('transaction_payment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_point', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropForeign('transaction_payment_id');
            $table->dropColumn('transaction_payment_id');
        });
    }
};
