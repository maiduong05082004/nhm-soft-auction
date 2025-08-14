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
            $table->integer('seller_rating')->default(0)->after('star_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluate', function (Blueprint $table) {
            $table->dropColumn('seller_rating');
        });
    }
};
