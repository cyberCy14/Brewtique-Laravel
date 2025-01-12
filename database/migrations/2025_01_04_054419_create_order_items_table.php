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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('coffee_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2)->default(0); // store the coffee price at checkout time
            $table->timestamps();
            
            // Foreign key references
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('coffee_id')->references('id')->on('coffees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
