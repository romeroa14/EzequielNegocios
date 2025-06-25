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
            $table->foreignId('category_id')->constrained('product_categories');
            $table->foreignId('subcategory_id')->constrained('product_subcategories');
            $table->string('name');
            $table->string('description');
            $table->string('sku_base');
            $table->enum('unit_type', ['kg', 'ton', 'saco', 'caja', 'unidad']);
            $table->string('image');
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
