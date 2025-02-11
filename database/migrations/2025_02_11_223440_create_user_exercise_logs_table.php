<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_exercise_logs', function (Blueprint $table) {
            $table->id('id')->primary();
            $table->unsignedBigInteger('user_id');  // Changed this line
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('exercise_id');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->float('duration_minutes');
            $table->float('distance')->nullable();
            $table->string('distance_unit')->nullable();
            $table->float('calories_burned');
            $table->float('avg_heart_rate')->nullable();
            $table->string('intensity_level');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['exercise_id', 'created_at']);
            $table->index(['start_time', 'end_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_exercise_logs');
    }
};
