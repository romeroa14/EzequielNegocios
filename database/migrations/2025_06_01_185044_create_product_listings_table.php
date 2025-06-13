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
        Schema::create('product_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity_available');
            $table->enum('quality_grade', ['premium', 'standard', 'economic']);
            $table->date('harvest_date');
            $table->json('images')->nullable();
            $table->string('location_city');
            $table->string('location_state');
            $table->enum('status', ['active', 'pending', 'sold_out', 'inactive'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_listings');
    }
};
