<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_custom_exercises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('difficulty_level')->default('intermediate');
            $table->string('image_url')->nullable();
            $table->decimal('calories_per_minute', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('user_exercise_logs', function (Blueprint $table) {
            $table->uuid('custom_exercise_id')->nullable()->after('exercise_id');
            // user_custom_exercises තේබලය සමඟ foreign key constraint එකතු කිරීම අමතක නොකරන්න
            $table->foreign('custom_exercise_id')->references('id')->on('user_custom_exercises')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('user_exercise_logs', function (Blueprint $table) {
            $table->dropColumn('custom_exercise_id');
        });

        Schema::dropIfExists('user_custom_exercises');
    }
};
