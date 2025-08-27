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
        Schema::table('banner_types', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->integer('status')->default(1)->comment('0: inactive, 1: active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_types', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dropColumn('status');
        });
    }
};
