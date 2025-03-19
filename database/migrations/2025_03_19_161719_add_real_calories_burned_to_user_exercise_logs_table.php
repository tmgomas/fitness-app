<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_exercise_logs', function (Blueprint $table) {
            $table->float('real_calories_burned', 8, 2)->nullable()->after('calories_burned');
        });
    }

    public function down()
    {
        Schema::table('user_exercise_logs', function (Blueprint $table) {
            $table->dropColumn('real_calories_burned');
        });
    }
};
