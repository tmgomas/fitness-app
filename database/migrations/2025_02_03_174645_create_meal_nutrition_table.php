<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_nutrition', function (Blueprint $table) {
            $table->uuid('meal_nutrition_id')->primary();
            $table->foreignUuid('meal_id')
                  ->references('meal_id')
                  ->on('meals')
                  ->onDelete('cascade');
            $table->foreignUuid('nutrition_id')
                  ->references('nutrition_id')
                  ->on('nutrition_types')
                  ->onDelete('restrict');
            $table->decimal('amount_per_100g', 8, 2)->nullable();  // Changed to nullable
            $table->string('measurement_unit');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_nutrition');
    }
};