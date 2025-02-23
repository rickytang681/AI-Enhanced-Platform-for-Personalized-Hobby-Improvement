<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['video', 'text']);
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('category');
            $table->string('subcategory');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('library_items');
    }
}; 