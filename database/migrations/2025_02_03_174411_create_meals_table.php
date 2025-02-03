<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->uuid('meal_id')->primary();  // Changed to match pattern
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('default_serving_size', 8, 2);  // Changed to decimal
            $table->string('serving_unit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();  // Added soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};