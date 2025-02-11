<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // User's name
            $table->string('email')->unique(); // User's email, must be unique
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // User's password
            $table->string('profile_picture')->nullable(); // Profile picture, optional
            $table->string('phone')->nullable(); // Phone number, optional
            $table->string('role')->default('user'); // Role column with a default value of 'user'
            $table->rememberToken(); // Token for "remember me" functionality
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users'); // Drop the table if it exists
    }
}
