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
        Schema::create('market_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->string('currency', 3)->default('VES');
            $table->timestamp('change_date');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('people')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['product_id', 'change_date']);
            $table->comment('Historial de cambios de precios de mercado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_price_histories');
    }
};
