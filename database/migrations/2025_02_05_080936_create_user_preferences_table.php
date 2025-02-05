<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->uuid('pref_id')->primary();
            $table->unsignedBigInteger('user_id');  // Changed this line
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('allergies')->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->text('disliked_foods')->nullable();
            $table->text('fitness_goals')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
}
