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
        if (Schema::hasColumn('payments', 'payer_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payer_id');
            });
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payer_id');
        });
    }
};
