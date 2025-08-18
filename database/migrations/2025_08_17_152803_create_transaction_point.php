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
        Schema::create('transaction_point', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('point')->comment('Số điểm giao dịch');
            $table->text('description')->nullable()->comment('Mô tả giao dịch');
            $table->foreignId('user_id')->constrained('users')->after('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_point');
    }
};
