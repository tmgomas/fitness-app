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
       Schema::create('user_agreements', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users');
    $table->foreignUuid('agreement_id')->constrained('agreements');
    $table->timestamp('accepted_at');
    $table->string('ip_address')->nullable();
    $table->string('user_agent')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_agreements');
    }
};
