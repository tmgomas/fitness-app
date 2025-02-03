<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_foods', function (Blueprint $table) {
            $table->uuid('meal_foods_id')->primary();  // Changed to match pattern
            $table->foreignUuid('meal_id')
                  ->references('meal_id')
                  ->on('meals')
                  ->onDelete('cascade');
            $table->foreignUuid('food_id')
                  ->references('food_id')
                  ->on('food_items')
                  ->onDelete('restrict');
            $table->decimal('quantity', 8, 2);  // Changed to decimal
            $table->string('unit');
            $table->timestamps();
            $table->softDeletes();  // Added soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_foods');
    }
};