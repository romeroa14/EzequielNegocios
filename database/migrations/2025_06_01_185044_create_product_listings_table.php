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
            $table->foreignId('person_id')->on('persons')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->on('products')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('description');
            $table->integer('quantity_available');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('wholesale_price', 10, 2);
            $table->integer('min_quantity_order');
            $table->integer('max_quantity_order');
            $table->enum('quality_grade', ['premium', 'standard', 'economic']);
            $table->date('harvest_date');
            $table->date('expiry_date');
            $table->json('images');
            $table->string('location_city');
            $table->string('location_state');
            $table->boolean('pickup_available')->default(true);
            $table->boolean('delivery_available')->default(true);
            $table->integer('delivery_radius_km')->nullable();
            $table->enum('status', ['active', 'sold_out', 'inactive', 'expired']);
            $table->date('featured_until');
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
