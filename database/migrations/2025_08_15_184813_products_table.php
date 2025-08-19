<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->nullable()->constrained('people')->comment('Vendedor específico (solo para productos NO universales)');
            $table->foreignId('product_category_id')->constrained('product_categories');
            $table->foreignId('product_subcategory_id')->constrained('product_subcategories');
            $table->foreignId('product_presentation_id')->constrained('product_presentations');
            $table->foreignId('product_line_id')->constrained('product_lines');
            $table->foreignId('brand_id')->constrained('brands');
            $table->foreignId('creator_user_id')->nullable()->constrained('users')->comment('Productor universal que creó el producto (solo para productos universales)');
            $table->string('name');
            $table->string('description');
            $table->string('sku_base');
            $table->decimal('custom_quantity', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->json('seasonal_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_universal')->default(false)->comment('true = producto universal, false = producto específico');
            
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['is_universal', 'creator_user_id']);
            $table->index(['is_universal', 'person_id']);
        });

        // NOTA: Se eliminó el constraint check_product_type para simplificar la lógica
        // La validación se maneja a nivel de aplicación en el formulario

        // Asegurar que los productos universales existentes tengan creator_user_id
        DB::statement("
            UPDATE products 
            SET creator_user_id = (
                SELECT id FROM users 
                WHERE role = 'producer' AND is_universal = true 
                LIMIT 1
            ) 
            WHERE is_universal = true AND creator_user_id IS NULL
        ");

        // Asegurar que los productos normales existentes tengan person_id
        DB::statement("
            UPDATE products 
            SET person_id = (
                SELECT id FROM people 
                WHERE role = 'seller' AND is_active = true 
                LIMIT 1
            ) 
            WHERE is_universal = false AND person_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
