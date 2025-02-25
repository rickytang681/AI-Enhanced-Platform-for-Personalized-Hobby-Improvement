<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('library_favorites', 'library_saves');
    }

    public function down()
    {
        Schema::rename('library_saves', 'library_favorites');
    }
}; 