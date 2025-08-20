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
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['membership_plan_id']);
            
            $table->dropColumn(['payment_id', 'amount', 'membership_plan_id']);
            
            if (!Schema::hasColumn('membership_transactions', 'money')) {
                $table->double('money');
            }
            
            if (!Schema::hasColumn('membership_transactions', 'membership_user_id')) {
                $table->foreignId('membership_user_id')->constrained('membership_users');
            }
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
