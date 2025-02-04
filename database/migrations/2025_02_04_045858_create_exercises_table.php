<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('exercise_categories');
            $table->string('name');
            $table->text('description');
            $table->string('difficulty_level');
            $table->string('image_url')->nullable();
            $table->float('calories_per_minute')->nullable();
            $table->float('calories_per_km')->nullable();
            $table->boolean('requires_distance')->default(false);
            $table->boolean('requires_heartrate')->default(false);
            $table->string('recommended_intensity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};