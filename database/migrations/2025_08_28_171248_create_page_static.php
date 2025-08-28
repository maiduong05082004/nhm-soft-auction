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
        Schema::create('page_statics', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 
            $table->string('slug')->unique();        
            $table->longText('content')->nullable(); 
            $table->text('excerpt')->nullable();     
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->string('image')->nullable();

            $table->tinyInteger('status')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->string('template')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_statics', function (Blueprint $table) {
            $table->dropIfExists('page_statics');
        });
    }
};
