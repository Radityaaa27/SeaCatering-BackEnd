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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name'); // Tambahkan
            $table->string('phone'); // Tambahkan
            $table->string('plan_name'); // Tambahkan
            $table->decimal('total_price', 10, 2); // Tambahkan
            $table->json('meal_types'); // Untuk menyimpan array
            $table->json('delivery_days'); // Untuk menyimpan array
            $table->text('allergies')->nullable();
            $table->date('order_date')->default(now()); // Otomatis diisi tanggal sekarang
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
