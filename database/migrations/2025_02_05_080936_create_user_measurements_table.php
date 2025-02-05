<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMeasurementsTable extends Migration
{
    public function up()
    {
        Schema::create('user_measurements', function (Blueprint $table) {
            $table->uuid('measurement_id')->primary();
            $table->unsignedBigInteger('user_id');  // Changed this line
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('chest')->nullable();
            $table->float('waist')->nullable();
            $table->float('hips')->nullable();
            $table->float('arms')->nullable();
            $table->float('thighs')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_measurements');
    }
}
