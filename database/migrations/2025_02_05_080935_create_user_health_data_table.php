<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHealthDataTable extends Migration
{
    public function up()
    {
        Schema::create('user_health_data', function (Blueprint $table) {
            $table->uuid('health_id')->primary();
            $table->unsignedBigInteger('user_id');  // Changed this line
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->float('bmi')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_health_data');
    }
}
