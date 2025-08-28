<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_point', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_point', 'status')) {
                $table->integer('status')->default(1)->after('id');
            }

            if (!Schema::hasColumn('transaction_point', 'transaction_payment_id')) {
                $table->unsignedBigInteger('transaction_payment_id')->nullable()->after('status');
            }
        });

        Schema::table('transaction_point', function (Blueprint $table) {
            $table->foreign('transaction_payment_id', 'tp_transaction_payment_id_fk')
                ->references('id')
                ->on('transaction_payment')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_point', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_point', 'transaction_payment_id')) {
                $table->dropForeign('tp_transaction_payment_id_fk');
                $table->dropColumn('transaction_payment_id');
            }
            if (Schema::hasColumn('transaction_point', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};