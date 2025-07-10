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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people');
            $table->foreignId('product_category_id')->constrained('product_categories');
            $table->foreignId('product_subcategory_id')->constrained('product_subcategories');
            $table->foreignId('product_presentation_id')->constrained('product_presentations');
            $table->foreignId('product_line_id')->constrained('product_lines');
            $table->foreignId('brand_id')->constrained('brands');
            $table->string('name');
            $table->string('description');
            $table->string('sku_base');
            $table->decimal('custom_quantity', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->json('seasonal_info');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
