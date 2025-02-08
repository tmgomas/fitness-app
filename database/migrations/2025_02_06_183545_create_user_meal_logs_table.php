<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_meal_logs', function (Blueprint $table) {
            $table->id('meal_log_id')->primary();
            $table->unsignedBigInteger('user_id');  // Changed to unsignedBigInteger to match users table
            $table->string('meal_id');
            $table->datetime('date');
            $table->string('meal_type');
            $table->float('serving_size');
            $table->string('serving_unit');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('meal_id')->references('meal_id')->on('meals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_meal_logs');
    }
};
