<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_food_logs', function (Blueprint $table) {
            $table->string('food_log_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('food_id');
            $table->datetime('date');
            $table->string('meal_type'); // breakfast, lunch, dinner, snack
            $table->float('serving_size');
            $table->string('serving_unit');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('food_id')->references('food_id')->on('food_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_food_logs');
    }
};
