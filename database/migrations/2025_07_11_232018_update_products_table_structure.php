<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primer paso: Agregar columnas como nullable
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'person_id')) {
                $table->foreignId('person_id')->nullable()->constrained('people');
            }
            if (!Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'product_subcategory_id')) {
                $table->foreignId('product_subcategory_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'product_presentation_id')) {
                $table->foreignId('product_presentation_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'product_line_id')) {
                $table->foreignId('product_line_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'creator_user_id')) {
                $table->foreignId('creator_user_id')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn('products', 'sku_base')) {
                $table->string('sku_base')->nullable();
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
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('products', 'is_universal')) {
                $table->boolean('is_universal')->default(false);
            }
        });

        // Mantener todas las columnas como nullable por ahora
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable()->change();
            }
            if (Schema::hasColumn('products', 'product_subcategory_id')) {
                $table->foreignId('product_subcategory_id')->nullable()->change();
            }
            if (Schema::hasColumn('products', 'product_presentation_id')) {
                $table->foreignId('product_presentation_id')->nullable()->change();
            }
            if (Schema::hasColumn('products', 'product_line_id')) {
                $table->foreignId('product_line_id')->nullable()->change();
            }
            if (Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'person_id', 'product_category_id', 'product_subcategory_id',
                'product_presentation_id', 'product_line_id', 'brand_id',
                'creator_user_id', 'name', 'description', 'sku_base',
                'custom_quantity', 'image', 'seasonal_info', 'is_active', 'is_universal'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
