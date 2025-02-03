<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_types', function (Blueprint $table) {
            $table->uuid('nutrition_id')->primary();  // Changed from 'id' to 'nutrition_id'
            $table->string('name');
            $table->text('description');
            $table->string('unit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_types');
    }
};