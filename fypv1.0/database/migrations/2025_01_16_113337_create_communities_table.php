<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('cover_image')->nullable();
            $table->string('tag');
            $table->enum('post_type', ['question', 'experience', 'discussion']);
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('communities');
    }
};
