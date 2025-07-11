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

        // Segundo paso: Establecer valores por defecto para todas las columnas requeridas
        if (Schema::hasColumn('products', 'product_category_id')) {
            $defaultCategoryId = DB::table('product_categories')->first()?->id;
            if ($defaultCategoryId) {
                DB::table('products')->whereNull('product_category_id')->update(['product_category_id' => $defaultCategoryId]);
            }
        }

        if (Schema::hasColumn('products', 'product_subcategory_id')) {
            $defaultSubcategoryId = DB::table('product_subcategories')->first()?->id;
            if ($defaultSubcategoryId) {
                DB::table('products')->whereNull('product_subcategory_id')->update(['product_subcategory_id' => $defaultSubcategoryId]);
            }
        }

        if (Schema::hasColumn('products', 'product_presentation_id')) {
            $defaultPresentationId = DB::table('product_presentations')->first()?->id;
            if ($defaultPresentationId) {
                DB::table('products')->whereNull('product_presentation_id')->update(['product_presentation_id' => $defaultPresentationId]);
            }
        }

        if (Schema::hasColumn('products', 'product_line_id')) {
            $defaultLineId = DB::table('product_lines')->first()?->id;
            if ($defaultLineId) {
                DB::table('products')->whereNull('product_line_id')->update(['product_line_id' => $defaultLineId]);
            }
        }

        if (Schema::hasColumn('products', 'brand_id')) {
            $defaultBrandId = DB::table('brands')->first()?->id;
            if ($defaultBrandId) {
                DB::table('products')->whereNull('brand_id')->update(['brand_id' => $defaultBrandId]);
            }
        }

        // Establecer valores por defecto para campos no ID
        DB::table('products')->whereNull('name')->update(['name' => 'Producto sin nombre']);
        DB::table('products')->whereNull('description')->update(['description' => 'Sin descripción']);
        DB::table('products')->whereNull('sku_base')->update(['sku_base' => 'SKU-' . uniqid()]);

        // Verificar que todas las columnas requeridas tengan valores antes de hacerlas NOT NULL
        $requiredColumns = [
            'product_category_id',
            'product_subcategory_id',
            'product_presentation_id',
            'product_line_id',
            'brand_id',
            'name',
            'description',
            'sku_base'
        ];

        foreach ($requiredColumns as $column) {
            if (Schema::hasColumn('products', $column)) {
                $nullCount = DB::table('products')->whereNull($column)->count();
                if ($nullCount > 0) {
                    throw new \Exception("La columna {$column} aún tiene {$nullCount} valores NULL. No se puede proceder con la migración.");
                }
            }
        }

        // Tercer paso: Hacer las columnas NOT NULL después de verificar que no hay valores nulos
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'product_subcategory_id')) {
                $table->foreignId('product_subcategory_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'product_presentation_id')) {
                $table->foreignId('product_presentation_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'product_line_id')) {
                $table->foreignId('product_line_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'description')) {
                $table->string('description')->nullable(false)->change();
            }
            if (Schema::hasColumn('products', 'sku_base')) {
                $table->string('sku_base')->nullable(false)->change();
            }
        });

        // Cuarto paso: Agregar las restricciones de clave foránea
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_category_id')) {
                $table->foreign('product_category_id')->references('id')->on('product_categories');
            }
            if (Schema::hasColumn('products', 'product_subcategory_id')) {
                $table->foreign('product_subcategory_id')->references('id')->on('product_subcategories');
            }
            if (Schema::hasColumn('products', 'product_presentation_id')) {
                $table->foreign('product_presentation_id')->references('id')->on('product_presentations');
            }
            if (Schema::hasColumn('products', 'product_line_id')) {
                $table->foreign('product_line_id')->references('id')->on('product_lines');
            }
            if (Schema::hasColumn('products', 'brand_id')) {
                $table->foreign('brand_id')->references('id')->on('brands');
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
