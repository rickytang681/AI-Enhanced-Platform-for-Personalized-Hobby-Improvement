<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('hobbies'); // Changed to JSON to store multiple hobbies with experience
            $table->string('goal');
            $table->integer('progress')->default(0);
            $table->string('status')->default('in-progress'); // e.g., 'in-progress', 'completed'
            $table->date('deadline')->nullable();
            $table->text('notes')->nullable(); // Added notes field
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
}

