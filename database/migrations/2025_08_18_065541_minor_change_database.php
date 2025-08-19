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
        Schema::dropIfExists('transactions');

        Schema::table('membership_transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_id','membership_plan_id']);
            $table->dropColumn(['payment_id', 'amount']);
            $table->double('money');
            $table->dropColumn(['membership_plan_id']);
            $table->foreignId('membership_user_id')->constrained('membership_users')->after('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
