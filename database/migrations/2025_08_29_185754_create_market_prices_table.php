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
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('price', 10, 2); // Precio en bolívares
            $table->string('currency', 3)->default('VES'); // Moneda (VES, USD)
            $table->date('price_date'); // Fecha del precio (ej: lunes de Coche)
            $table->text('notes')->nullable(); // Notas adicionales
            $table->boolean('is_active')->default(true); // Si el precio está activo
            $table->foreignId('updated_by')->nullable()->constrained('people')->onDelete('set null'); // Quién actualizó
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['product_id', 'price_date']);
            $table->index(['price_date', 'is_active']);
            $table->index('is_active');
            
            // Comentarios para documentación
            $table->comment('Precios de mercado para productos - Actualizaciones semanales de Coche');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};
