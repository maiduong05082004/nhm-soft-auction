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
        Schema::table('membership_transactions', function (Blueprint $table) {
            $table->bigInteger('order_code')->nullable();
            $table->foreignId('membership_plan_id')
                ->constrained('membership_plans')
                ->cascadeOnDelete();
            $table->timestamp('expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_transactions', function (Blueprint $table) {
            $table->dropColumn('order_code');
            $table->dropConstrainedForeignId('membership_plan_id');
            $table->dropColumn('expired_at');
        });
    }
};
