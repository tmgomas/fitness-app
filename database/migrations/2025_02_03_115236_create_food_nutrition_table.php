<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_nutrition', function (Blueprint $table) {
            $table->uuid('food_nutrition_id')->primary();
            $table->uuid('food_id');
            $table->uuid('nutrition_id');
            $table->float('amount_per_100g');
            $table->string('measurement_unit');
            $table->timestamps();
        
            $table->foreign('food_id')
                  ->references('food_id')   // Changed from 'id' to 'food_id'
                  ->on('food_items')
                  ->onDelete('cascade');
            
            $table->foreign('nutrition_id')
                  ->references('nutrition_id')  // Changed from 'id' to 'nutrition_id'
                  ->on('nutrition_types')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_nutrition');
    }
};