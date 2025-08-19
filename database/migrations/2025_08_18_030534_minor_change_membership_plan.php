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
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('badge')->nullable()->comment("Huy hiệu hiển thị trên trang chủ");
            $table->integer('sort')->nullable()->comment("Sắp xếp hiển thị trên trang chủ");
            $table->string('badge_color')->comment("Màu huy hiệu hiển thị trên trang chủ");
            $table->text('description')->change()->comment("Mô tả gói thành viên");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn(['badge', 'sort', 'badge_color']);
            $table->string('description')->change()->comment("Mô tả gói thành viên");
        });
    }
};
