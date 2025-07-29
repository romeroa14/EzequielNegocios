<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code'); // USD, EUR, etc.
            $table->decimal('rate', 20, 8); // Tasa con suficientes decimales
            $table->string('source')->default('BCV'); // Fuente de la tasa
            $table->timestamp('fetched_at'); // Cuando se obtuvo la tasa
            $table->boolean('is_valid')->default(true); // Para marcar si hubo error en el scraping
            $table->text('error_message')->nullable(); // Para almacenar mensajes de error
            $table->timestamps();

            // Índice para búsquedas rápidas
            $table->index(['currency_code', 'fetched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
}; 