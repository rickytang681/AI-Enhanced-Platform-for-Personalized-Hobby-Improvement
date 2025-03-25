<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('library_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('library_item_id')->constrained('library_items')->onDelete('cascade');
            $table->integer('rating')->comment('1-5 stars');
            $table->timestamps();
            $table->unique(['user_id', 'library_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('library_ratings');
    }
}; 
