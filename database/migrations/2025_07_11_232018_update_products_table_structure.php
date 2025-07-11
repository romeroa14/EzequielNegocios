<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Aquí agregamos o modificamos columnas
            if (!Schema::hasColumn('products', 'person_id')) {
                $table->foreignId('person_id')->nullable()->constrained('people');
            }
            if (!Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->constrained('product_categories');
            }
            if (!Schema::hasColumn('products', 'product_subcategory_id')) {
                $table->foreignId('product_subcategory_id')->constrained('product_subcategories');
            }
            if (!Schema::hasColumn('products', 'product_presentation_id')) {
                $table->foreignId('product_presentation_id')->constrained('product_presentations');
            }
            if (!Schema::hasColumn('products', 'product_line_id')) {
                $table->foreignId('product_line_id')->constrained('product_lines');
            }
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->constrained('brands');
            }
            if (!Schema::hasColumn('products', 'creator_user_id')) {
                $table->foreignId('creator_user_id')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('products', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->string('description');
            }
            if (!Schema::hasColumn('products', 'sku_base')) {
                $table->string('sku_base');
            }
            if (!Schema::hasColumn('products', 'custom_quantity')) {
                $table->decimal('custom_quantity', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('products', 'seasonal_info')) {
                $table->json('seasonal_info')->nullable();
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active');
            }
            if (!Schema::hasColumn('products', 'is_universal')) {
                $table->boolean('is_universal')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Si necesitas revertir algún cambio específico
        Schema::table('products', function (Blueprint $table) {
            // Aquí puedes eliminar columnas si es necesario
        });
    }
};
