<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('title');
    $table->text('content');
    $table->string('version');
    $table->boolean('is_active')->default(true);
    $table->date('effective_date');
    $table->text('summary')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
