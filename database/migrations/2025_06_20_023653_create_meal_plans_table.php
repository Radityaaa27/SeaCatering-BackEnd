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
// database/migrations/[timestamp]_create_meal_plans_table.php
Schema::create('meal_plans', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 0);
    $table->string('short_description'); 
    $table->text('long_description'); 
    $table->json('benefits')->nullable();
    $table->string('image')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
